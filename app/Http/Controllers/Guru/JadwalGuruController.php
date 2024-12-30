<?php

namespace App\Http\Controllers\Guru;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SlotWaktuTingkatanKelas;
use App\Models\SlotWaktuMapping;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\TugasMengajar;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalGuruController extends Controller
{
    public function showJadwal(Request $request)
    {
        if ($request->has('search')) {
            session(['search' => $request->input('search')]);
        } elseif ($request->input('clear_search')) {
            session()->forget('search');
        }

        $search = session('search', '');

        // Ambil semua tahun ajaran untuk dropdown
        $tahunAjaran = TahunAjaran::all()->map(function ($tahun) {
            $mulaiYear = date('Y', strtotime($tahun->mulai));
            $selesaiYear = date('Y', strtotime($tahun->selesai)); 
            
            return [
                'id' => $tahun->id,
                'period' => $mulaiYear . '/' . $selesaiYear 
            ];
        })->unique('period') 
          ->sortByDesc('period') 
          ->pluck('period', 'id');

        $selectedTahunAjaranId = $request->input('tahun_ajaran_id', $this->getCurrentTahunAjaranId());
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
        // Simpan state dalam session
        if ($request->has('tingkatan_kelas_id')) {
            session(['global_selected_tingkatan_kelas' => $request->tingkatan_kelas_id]);
        }
        if ($request->has('hari_id')) {
            session(['global_selected_hari' => $request->hari_id]);
        }
        $selectedTingkatanKelas = session('global_selected_tingkatan_kelas', 1);
        $selectedHari = session('global_selected_hari', 1);
    
        // Ambil semua slot waktu
        $slotWaktu = SlotWaktuTingkatanKelas::whereHas('slotWaktuMapping', function($query) use ($selectedHari) {
            $query->where('hari_id', $selectedHari);
        })
        ->where('tingkatan_kelas_id', $selectedTingkatanKelas)
        ->with(['slotWaktu', 'slotWaktuMapping' => function($query) use ($selectedHari) {
            $query->where('hari_id', $selectedHari);
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
        // Ambil slot khusus dari fungsi getExcludedSlots()
        $slotKhusus = SlotWaktuMapping::whereIn('id', $slotKhususIds)
        ->where('hari_id', $selectedHari)
        ->whereHas('slotWaktuTingkatanKelas', function ($query) use ($selectedTingkatanKelas) {
            $query->where('tingkatan_kelas_id', $selectedTingkatanKelas);
        })
        ->with(['slotWaktuTingkatanKelas.slotWaktu'])
        ->get()
        ->map(function($mapping) use ($selectedTingkatanKelas, $selectedHari) {
            $slot = $mapping->slotWaktuTingkatanKelas->slotWaktu;
            
            // Tentukan nama kegiatan berdasarkan kondisi
            $namaKegiatan = 'TADARUS';
            if ($selectedHari == 1 && in_array($selectedTingkatanKelas, [5, 6])) {
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


        // Ambil semua kelas untuk tingkatan yang dipilih
        $kelas = Kelas::where('tingkatan_kelas_id', $selectedTingkatanKelas)
        ->orderBy('nama_kelas')
        ->pluck('nama_kelas');

        // Ambil sesi belajar
        $sesiBelajar = SlotWaktuTingkatanKelas::where('tingkatan_kelas_id', $selectedTingkatanKelas)
        ->with('sesiBelajar')
        ->first();

        $jadwalData = Jadwal::whereHas('slotWaktuMapping', function ($query) use ($selectedTingkatanKelas, $selectedHari) {
            $query->where('hari_id', $selectedHari)
                  ->whereIn('slot_waktu_tingkatan_kelas_id', function ($subQuery) use ($selectedTingkatanKelas) {
                      $subQuery->select('id')
                               ->from('slot_waktu_tingkatan_kelas')
                               ->where('tingkatan_kelas_id', $selectedTingkatanKelas);
                  });
        })
        ->where('tahun_ajaran_id', $selectedTahunAjaran)
        ->when($search, function ($query, $search) {
            $query->whereHas('kelasTugasMengajar.tugasMengajar.guru', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('kelasTugasMengajar.tugasMengajar.mataPelajaran', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        })
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
                foreach ($kelas as $kelasNama) {
                    $jadwalKelas = $jadwalData
                        ->where('slotWaktuMapping.slotWaktuTingkatanKelas.slotWaktu.id', $slot['id'])
                        ->where('kelasTugasMengajar.kelas.nama_kelas', $kelasNama)
                        ->first();

                    if ($jadwalKelas) {
                        $jadwalMatrix[$slot['waktu']]['kelas'][$kelasNama] = [
                            'mata_pelajaran' => $jadwalKelas->kelasTugasMengajar->tugasMengajar->mataPelajaran->nama,
                            'guru' => $jadwalKelas->kelasTugasMengajar->tugasMengajar->guru->name,
                            'warna' =>$jadwalKelas->kelasTugasMengajar->tugasMengajar->mataPelajaran->warna->kode_hex,
                        ];
                    } else {
                        $jadwalMatrix[$slot['waktu']]['kelas'][$kelasNama] = [
                            'mata_pelajaran' => '-',
                            'guru' => '-',
                            'warna' => 'bg-gray-100'
                        ];
                    }
                }
            }
        }

        return view('guru.jadwal.index', compact('jadwalMatrix','jadwalData', 'kelas', 'tahunAjaran', 'selectedTahunAjaran', 'selectedTingkatanKelas', 'selectedHari', 'selectedPeriod'));
    }

    public function showJadwalPribadi(Request $request)
    {
        if ($request->has('search_pribadi')) {
            session(['search_pribadi' => $request->input('search_pribadi')]);
        } elseif ($request->input('clear_search')) {
            session()->forget('search_pribadi');
        }

        $search = session('search_pribadi', '');

        $user = Auth::user();
        $guru = $user->guru;
       
        if (!$guru) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar sebagai guru.');
        }
        
        $selectedTahunAjaranId = $this->getCurrentTahunAjaranId();
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

        $tingkatanKelasGuru = TugasMengajar::where('guru_id', $guru->id)
        ->whereHas('kelasTugasMengajar.kelas.tingkatanKelas')
        ->with('kelasTugasMengajar.kelas.tingkatanKelas')
        ->get()
        ->flatMap(function ($tugas) {
            return $tugas->kelasTugasMengajar->pluck('kelas.tingkatanKelas.id');
        })
        ->unique();

        $selectedTingkatanKelas = $request->tingkatan_kelas_id;
        if (!$tingkatanKelasGuru->contains($selectedTingkatanKelas)) {
            $selectedTingkatanKelas = $tingkatanKelasGuru->first(); // Pilih nilai pertama yang valid
            if (!$selectedTingkatanKelas) {
                return redirect()->back()->with('error', 'Anda tidak mengajar di tingkatan kelas mana pun.');
            }
        }
        session(['pribadi_selected_tingkatan_kelas' => $selectedTingkatanKelas]);

        $hariDiajar = Jadwal::whereHas('kelasTugasMengajar.tugasMengajar', function ($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })
        ->whereHas('kelasTugasMengajar.kelas.tingkatanKelas', function ($query) use ($selectedTingkatanKelas) {
            $query->where('id', $selectedTingkatanKelas);
        })
        ->with('slotWaktuMapping.hari')
        ->get()
        ->pluck('slotWaktuMapping.hari.id')
        ->unique()
        ->sort();

        $selectedHari = $request->hari_id;
        if (!$hariDiajar->contains($selectedHari)) {
            $selectedHari = $hariDiajar->first(); // Pilih nilai pertama yang valid
            if (!$selectedHari) {
                return redirect()->back()->with('error', 'Tidak ada jadwal yang diajar di hari mana pun.');
            }
        }
        session(['pribadi_selected_hari' => $selectedHari]);

        $kelas = Jadwal::whereHas('kelasTugasMengajar.tugasMengajar', function($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })
        ->whereHas('kelasTugasMengajar.kelas.tingkatanKelas', function($query) use ($selectedTingkatanKelas) {
            $query->where('id', $selectedTingkatanKelas);
        })
        ->whereHas('slotWaktuMapping', function($query) use ($selectedHari) {
            $query->where('hari_id', $selectedHari);
        })
        ->with('kelasTugasMengajar.kelas')
        ->get()
        ->pluck('kelasTugasMengajar.kelas.nama_kelas')
        ->unique()
        ->sort();

        $slotWaktu = SlotWaktuTingkatanKelas::where('tingkatan_kelas_id', $selectedTingkatanKelas)
        ->whereHas('slotWaktuMapping', function($query) use ($selectedHari) {
            $query->where('hari_id', $selectedHari);
        })
        ->with(['slotWaktu', 'slotWaktuMapping' => function($query) use ($selectedHari) {
            $query->where('hari_id', $selectedHari);
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
        $slotKhusus = SlotWaktuMapping::whereIn('id', $slotKhususIds)
        ->where('hari_id', $selectedHari)
        ->whereHas('slotWaktuTingkatanKelas', function ($query) use ($selectedTingkatanKelas) {
            $query->where('tingkatan_kelas_id', $selectedTingkatanKelas);
        })
        ->with(['slotWaktuTingkatanKelas.slotWaktu'])
        ->get()
        ->map(function($mapping) use ($selectedTingkatanKelas, $selectedHari) {
            $slot = $mapping->slotWaktuTingkatanKelas->slotWaktu;
            
            // Tentukan nama kegiatan berdasarkan kondisi
            $namaKegiatan = 'TADARUS';
            if ($selectedHari == 1 && in_array($selectedTingkatanKelas, [5, 6])) {
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

        // Ambil sesi belajar
        $sesiBelajar = SlotWaktuTingkatanKelas::where('tingkatan_kelas_id', $selectedTingkatanKelas)
        ->with('sesiBelajar')
        ->first();

        $jadwalData = Jadwal::whereHas('slotWaktuMapping', function ($query) use ($selectedTingkatanKelas, $selectedHari) {
            $query->where('hari_id', $selectedHari)
                  ->whereHas('slotWaktuTingkatanKelas', function($subQuery) use ($selectedTingkatanKelas) {
                      $subQuery->where('tingkatan_kelas_id', $selectedTingkatanKelas);
                  });
        })
        ->whereHas('kelasTugasMengajar.tugasMengajar', function ($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })
        ->with([
            'slotWaktuMapping.slotWaktuTingkatanKelas.slotWaktu',
            'kelasTugasMengajar.kelas.tingkatanKelas',
            'kelasTugasMengajar.tugasMengajar.mataPelajaran',
            'kelasTugasMengajar.tugasMengajar.guru'
        ])
        ->get();
        
        // Buat matrix jadwal
        $jadwalMatrix = [];
        foreach ($mergedSlots as $slot) {
            $jadwalMatrix[$slot['waktu']] = [
               'is_istirahat' => $slot['is_istirahat'] ?? false,
                'is_khusus' => $slot['is_khusus'] ?? false,
                'nama_kegiatan' => $slot['nama_kegiatan'] ?? null,
                'sesi_belajar' => $sesiBelajar ? $sesiBelajar->sesiBelajar->nama : '-', // Kembalikan sesi belajar
                'kelas' => []
            ];
    
            if (!($slot['is_istirahat'] ?? false) && !($slot['is_khusus'] ?? false)) {
                foreach ($kelas as $kelasNama) {
                    $jadwalKelas = $jadwalData
                        ->where('slotWaktuMapping.slotWaktuTingkatanKelas.slotWaktu.id', $slot['id'])
                        ->where('kelasTugasMengajar.kelas.nama_kelas', $kelasNama)
                        ->first();  
    
                    if ($jadwalKelas) {
                        $mataPelajaran = $jadwalKelas->kelasTugasMengajar->tugasMengajar->mataPelajaran;
                        if (empty($search) || stripos($mataPelajaran->nama, $search) !== false) {
                            $jadwalMatrix[$slot['waktu']]['kelas'][$kelasNama] = [
                                'mata_pelajaran' => $mataPelajaran->nama,
                                'guru' => $jadwalKelas->kelasTugasMengajar->tugasMengajar->guru->name,
                                'warna' => $mataPelajaran->warna->kode_hex,
                            ];
                        }
                    } else {
                        $jadwalMatrix[$slot['waktu']]['kelas'][$kelasNama] = [
                            'mata_pelajaran' => '-',
                            'guru' => '-',
                            'warna' => 'bg-gray-100'
                        ];
                    }
                }
            }
        }

        if (!empty($search)) {
            $jadwalMatrix = array_filter($jadwalMatrix, function($slot) {
                // Keep the slot if it's a special slot or has any classes matching the search
                if ($slot['is_istirahat'] || $slot['is_khusus']) {
                    return true;
                }
                
                // Check if any class in this slot has a matching mata pelajaran
                return count(array_filter($slot['kelas'], function($kelas) {
                    return $kelas['mata_pelajaran'] !== '-';
                })) > 0;
            });
        }
        

        return view('guru.jadwal.pribadi', compact('jadwalMatrix', 'hariDiajar', 'kelas', 'tahunAjaran', 'selectedTingkatanKelas', 'selectedHari', 'tingkatanKelasGuru', 'selectedPeriod', 'search'));
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
