<?php

namespace App\Http\Controllers\Admin;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\SlotWaktuMapping;
use App\Models\TingkatanKelas;
use App\Models\TahunAjaran;
use App\Models\SlotWaktuTingkatanKelas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;

class JadwalCetakController extends Controller
{
    public function cetakJadwalSemua(Request $request)
    {
        try {
            $selectedTahunAjaranId = $request->input('tahun_ajaran_id');
            if (empty($selectedTahunAjaranId)) {
                $selectedTahunAjaranId = $this->getCurrentTahunAjaranId();
            }

            $tahunAjaran = TahunAjaran::all()->map(function ($tahun) {
                $mulaiYear = date('Y', strtotime($tahun->mulai));
                $selesaiYear = date('Y', strtotime($tahun->selesai)); 
                
                return [
                    'id' => $tahun->id,
                    'period' => $mulaiYear . '/' . $selesaiYear
                ];
            })->unique('period')
            ->sortByDesc('period');
            
            $formattedTahunAjaran = $tahunAjaran->pluck('period', 'id');
            $selectedTahunAjaran = $tahunAjaran->where('id', $selectedTahunAjaranId)->first();
            $selectedPeriod = $selectedTahunAjaran ? $selectedTahunAjaran['period'] : null;
        
            // Ambil semua tingkatan kelas
            $tingkatanKelas = TingkatanKelas::all();
            
            $jadwalPerTingkatan = [];
            
            foreach ($tingkatanKelas as $tingkatan) {
                $jadwalPerHari = [];
                
                // Loop untuk setiap hari
                for ($hari = 1; $hari <= 5; $hari++) {
                    // Ambil semua slot waktu
                    $slotWaktu = SlotWaktuTingkatanKelas::whereHas('slotWaktuMapping', function($query) use ($hari) {
                        $query->where('hari_id', $hari);
                    })
                    ->where('tingkatan_kelas_id', $tingkatan->id)
                    ->with(['slotWaktu', 'slotWaktuMapping' => function($query) use ($hari) {
                        $query->where('hari_id', $hari);
                    }])
                    ->orderBy('slot_waktu_id')
                    ->get()
                    ->map(function($slot) {
                        return [
                            'id' => $slot->slot_waktu_id,
                            'waktu' => \Carbon\Carbon::parse($slot->slotWaktu->mulai)->format('H:i') . '-' . 
                                    \Carbon\Carbon::parse($slot->slotWaktu->selesai)->format('H:i'),
                            'is_istirahat' => $slot->slotWaktu->is_istirahat ?? false,
                            'is_khusus' => false
                        ];
                    });
        
                    $slotKhususIds = $this->getExcludedSlots();
                    // Ambil slot khusus
                    $slotKhusus = SlotWaktuMapping::whereIn('id', $slotKhususIds)
                    ->where('hari_id', $hari)
                    ->whereHas('slotWaktuTingkatanKelas', function ($query) use ($tingkatan) {
                        $query->where('tingkatan_kelas_id', $tingkatan->id);
                    })
                    ->with(['slotWaktuTingkatanKelas.slotWaktu'])
                    ->get()
                    ->map(function($mapping) use ($tingkatan, $hari) {
                        $slot = $mapping->slotWaktuTingkatanKelas->slotWaktu;
                        
                        $namaKegiatan = 'TADARUS';
                        if ($hari == 1 && in_array($tingkatan->id, [5, 6])) {
                            $namaKegiatan = 'UPACARA';
                        }
        
                        return [
                            'id' => $slot->id,
                            'waktu' => \Carbon\Carbon::parse($slot->mulai)->format('H:i') . '-' . 
                                    \Carbon\Carbon::parse($slot->selesai)->format('H:i'),
                            'is_istirahat' => false,
                            'nama_kegiatan' => $namaKegiatan,
                            'is_khusus' => true,
                        ];
                    });
        
                    $slotWaktuArray = collect($slotWaktu)->keyBy('waktu')->toArray();
                    $slotKhususArray = collect($slotKhusus)->keyBy('waktu')->toArray();
                    $mergedSlots = array_replace($slotWaktuArray, $slotKhususArray);
        
                    // Ambil semua kelas untuk tingkatan
                    $kelas = Kelas::where('tingkatan_kelas_id', $tingkatan->id)
                    ->orderBy('nama_kelas')
                    ->get();
        
                    // Ambil sesi belajar
                    $sesiBelajar = SlotWaktuTingkatanKelas::where('tingkatan_kelas_id', $tingkatan->id)
                    ->with('sesiBelajar')
                    ->first();
        
                    $jadwalData = Jadwal::whereHas('slotWaktuMapping', function ($query) use ($tingkatan, $hari) {
                        $query->where('hari_id', $hari)
                            ->whereIn('slot_waktu_tingkatan_kelas_id', function ($subQuery) use ($tingkatan) {
                                $subQuery->select('id')
                                        ->from('slot_waktu_tingkatan_kelas')
                                        ->where('tingkatan_kelas_id', $tingkatan->id);
                            });
                    })
                    ->where('tahun_ajaran_id', $selectedTahunAjaran)
                    ->with([
                        'slotWaktuMapping.slotWaktuTingkatanKelas.slotWaktu',
                        'slotWaktuMapping.slotWaktuTingkatanKelas.sesiBelajar',
                        'kelasTugasMengajar.tugasMengajar.mataPelajaran',
                        'kelasTugasMengajar.tugasMengajar.guru',
                        'kelasTugasMengajar.kelas'
                    ])
                    ->get();
        
                    // Buat matrix jadwal
                    $jadwalMatrix = [];
                    foreach ($mergedSlots as $slot) {
                        $jadwalMatrix[$slot['waktu']] = [
                            'is_istirahat' => $slot['is_istirahat'] ?? false,
                            'is_khusus' => $slot['is_khusus'] ?? false,
                            'nama_kegiatan' => $slot['nama_kegiatan'] ?? null,
                            'sesi_belajar' => $sesiBelajar->sesiBelajar->nama ?? '-',
                            'kelas' => []
                        ];
        
                        if (!($slot['is_istirahat'] ?? false) && !($slot['is_khusus'] ?? false)) {
                            foreach ($kelas as $kelasObj) {
                                $jadwalKelas = $jadwalData
                                    ->where('slotWaktuMapping.slotWaktuTingkatanKelas.slotWaktu.id', $slot['id'])
                                    ->where('kelasTugasMengajar.kelas.id', $kelasObj->id)
                                    ->first();
        
                                if ($jadwalKelas) {
                                    $jadwalMatrix[$slot['waktu']]['kelas'][$kelasObj->id] = [
                                        'mata_pelajaran' => $jadwalKelas->kelasTugasMengajar->tugasMengajar->mataPelajaran->nama,
                                        'guru' => $jadwalKelas->kelasTugasMengajar->tugasMengajar->guru->name,
                                        'warna' => $jadwalKelas->kelasTugasMengajar->tugasMengajar->mataPelajaran->warna->kode_hex,
                                    ];
                                } else {
                                    $jadwalMatrix[$slot['waktu']]['kelas'][$kelasObj->id] = [
                                        'mata_pelajaran' => '-',
                                        'guru' => '-',
                                        'warna' => 'bg-gray-100'
                                    ];
                                }
                            }
                        }
                    }
        
                    $jadwalPerHari[$hari] = [
                        'matrix' => $jadwalMatrix,
                        'kelas' => $kelas
                    ];
                }
        
                $jadwalPerTingkatan[$tingkatan->id] = $jadwalPerHari;
            }
        
            $pdf = PDF::loadView('cetak.jadwal', [
                'jadwalPerTingkatan' => $jadwalPerTingkatan,
                'tingkatanKelas' => $tingkatanKelas,
                'tahunAjaran' => $tahunAjaran,
                'selectedTahunAjaran' => $selectedTahunAjaran,
                'selectedPeriod' => $selectedPeriod,
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('jadwal-keseluruhan.pdf');
        } catch (\Exception $e) {
            \Log::error('Error in cetakJadwal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    private function getExcludedSlots()
    {
        // Get slots to exclude (upacara and tadarus)
        $slotWaktuUpacara = DB::table('slot_waktu')
        ->join('slot_waktu_tingkatan_kelas', 'slot_waktu.id', '=', 'slot_waktu_tingkatan_kelas.slot_waktu_id')
        ->join('tingkatan_kelas', 'slot_waktu_tingkatan_kelas.tingkatan_kelas_id', '=', 'tingkatan_kelas.id')
        ->join('slot_waktu_mapping', 'slot_waktu_tingkatan_kelas.id', '=', 'slot_waktu_mapping.slot_waktu_tingkatan_kelas_id')
        ->join('hari', 'slot_waktu_mapping.hari_id', '=', 'hari.id')
        ->whereIn('tingkatan_kelas.id', [5, 6])
        ->where('hari.nama_hari', 'Senin')
        ->orderBy('slot_waktu.mulai')
        ->limit(4)
        ->pluck('slot_waktu_mapping.id');

        $slotWaktuTadarus3dan4 = DB::table('slot_waktu')
        ->select('slot_waktu_mapping.id')
        ->join('slot_waktu_tingkatan_kelas', 'slot_waktu.id', '=', 'slot_waktu_tingkatan_kelas.slot_waktu_id')
        ->join('slot_waktu_mapping', 'slot_waktu_tingkatan_kelas.id', '=', 'slot_waktu_mapping.slot_waktu_tingkatan_kelas_id')
        ->join('hari', 'slot_waktu_mapping.hari_id', '=', 'hari.id')
        ->join('tingkatan_kelas', 'slot_waktu_tingkatan_kelas.tingkatan_kelas_id', '=', 'tingkatan_kelas.id')
        ->whereIn('tingkatan_kelas.id', [3, 4])
        ->whereIn('hari.nama_hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])
        ->where('slot_waktu.is_istirahat', 0)
        ->where('slot_waktu.mulai', function($query) {
             $query->select(DB::raw('MIN(slot_waktu2.mulai)'))
                ->from('slot_waktu as slot_waktu2')
                ->join('slot_waktu_tingkatan_kelas as swtk2', 'slot_waktu2.id', '=', 'swtk2.slot_waktu_id')
                ->join('slot_waktu_mapping as swm2', 'swtk2.id', '=', 'swm2.slot_waktu_tingkatan_kelas_id')
                ->whereColumn('swm2.hari_id', 'slot_waktu_mapping.hari_id')
                ->whereColumn('swtk2.tingkatan_kelas_id', 'slot_waktu_tingkatan_kelas.tingkatan_kelas_id')
                ->where('slot_waktu2.is_istirahat', 0);
        })
        ->pluck('id');

        $slotWaktuTadarus5dan6 = DB::table('slot_waktu')
        ->select('slot_waktu_mapping.id')
        ->join('slot_waktu_tingkatan_kelas', 'slot_waktu.id', '=', 'slot_waktu_tingkatan_kelas.slot_waktu_id')
        ->join('slot_waktu_mapping', 'slot_waktu_tingkatan_kelas.id', '=', 'slot_waktu_mapping.slot_waktu_tingkatan_kelas_id')
        ->join('hari', 'slot_waktu_mapping.hari_id', '=', 'hari.id')
        ->join('tingkatan_kelas', 'slot_waktu_tingkatan_kelas.tingkatan_kelas_id', '=', 'tingkatan_kelas.id')
        ->whereIn('tingkatan_kelas.id', [5, 6])
        ->whereIn('hari.nama_hari', ['Selasa', 'Rabu', 'Kamis', 'Jumat'])
        ->where('slot_waktu.is_istirahat', 0)
        ->where('slot_waktu.mulai', function($query) {
            $query->select(DB::raw('MIN(slot_waktu2.mulai)'))
                ->from('slot_waktu as slot_waktu2')
                ->join('slot_waktu_tingkatan_kelas as swtk2', 'slot_waktu2.id', '=', 'swtk2.slot_waktu_id')
                ->join('slot_waktu_mapping as swm2', 'swtk2.id', '=', 'swm2.slot_waktu_tingkatan_kelas_id')
                ->whereColumn('swm2.hari_id', 'slot_waktu_mapping.hari_id')
                ->whereColumn('swtk2.tingkatan_kelas_id', 'slot_waktu_tingkatan_kelas.tingkatan_kelas_id')
                ->where('slot_waktu2.is_istirahat', 0);
        })
        ->pluck('id');

        return $slotWaktuUpacara->concat($slotWaktuTadarus3dan4)->concat($slotWaktuTadarus5dan6)->unique();
    }


    private function getCurrentTahunAjaranId()
    {
        $currentDate = now();
        $tahunAjaran = TahunAjaran::where('mulai', '<=', $currentDate)
        ->where('selesai', '>=', $currentDate)
        ->first();

        return $tahunAjaran ? $tahunAjaran->id : null;
    }
}
