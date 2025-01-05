<?php
namespace App\Http\Controllers\WakilKurikulum;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\TingkatanKelas;
use App\Models\KelasTugasMengajar;
use App\Models\SlotWaktuMapping;
use App\Models\TahunAjaran;
use App\Http\Controllers\Controller;
use App\Models\SlotWaktuTingkatanKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\PDF;

class PenjadwalanController extends Controller
{
    public function showJadwal(Request $request)
    {
        if ($request->has('search')) {
            session(['search' => $request->input('search')]);
        } elseif ($request->input('clear_search')) {
            session()->forget('search');
        }

        $search = session('search', '');

        $selectedTahunAjaran = $request->input('tahun_ajaran_id', session('selected_tahun_ajaran'));
    
        if (!$selectedTahunAjaran) {
            $selectedTahunAjaran = $this->getCurrentTahunAjaranId();
        }
        
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

        // Simpan state dalam session
        if ($request->has('tingkatan_kelas_id')) {
            session(['selected_tingkatan_kelas' => $request->tingkatan_kelas_id]);
        }
        if ($request->has('hari_id')) {
            session(['selected_hari' => $request->hari_id]);
        }
        $selectedTingkatanKelas = session('selected_tingkatan_kelas', 1);
        $selectedHari = session('selected_hari', 1);
    
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
            // Menambahkan pencarian pada nama guru dan mata pelajaran
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
        session([
            'selected_tahun_ajaran' => $selectedTahunAjaran,
            'selected_hari' => $selectedHari,
            'selected_tingkatan_kelas' => $selectedTingkatanKelas,
        ]);

        return view('wakilkurikulum.penjadwalan.index', compact('jadwalMatrix', 'jadwalData', 'kelas', 'tahunAjaran', 'selectedTahunAjaran', 'selectedTingkatanKelas', 'selectedHari', 'search'));
    }

    public function generateJadwal(Request $request)
    {
        set_time_limit(300);
        $tahunAjaranId = $request->input('tahun_ajaran_id');

        if (!$tahunAjaranId) {
            $tahunAjaranId = $this->getCurrentTahunAjaranId();
        }
        // dd($tahunAjaranId);

        DB::beginTransaction();
    
        try {
            // Hapus jadwal yang sudah ada berdasarkan tahun ajaran
            Jadwal::where('tahun_ajaran_id', $tahunAjaranId)->delete();
    
            // Ambil semua data tugas mengajar yang akan dijadwalkan
            $teachingAssignments = KelasTugasMengajar::with([
                'tugasMengajar.guru',
                'tugasMengajar.mataPelajaran.detailMataPelajaran',
                'kelas.tingkatanKelas'
            ])->get();
    
            // Ambil slot waktu yang tersedia, dikelompokkan berdasarkan tingkatan kelas
            $availableSlotsByTingkatan = $this->getAvailableSlotsByTingkatanKelas();
    
            $finalSchedule = [];
            $counter = 0;
    
            // Kelompokkan tugas mengajar berdasarkan tingkatan kelas
            $assignmentsByTingkatan = $teachingAssignments->groupBy(fn($assignment) => $assignment->kelas->tingkatanKelas->id);
    
            // Proses penjadwalan per tingkatan kelas
            foreach ($assignmentsByTingkatan as $tingkatanId => $assignments) {
                // Pastikan ada slot untuk tingkatan ini
                if (!isset($availableSlotsByTingkatan[$tingkatanId]) || $availableSlotsByTingkatan[$tingkatanId]->isEmpty()) {
                    Log::error("Tidak ada slot untuk tingkatan $tingkatanId");
                    continue;
                }
    
                $availableSlots = $availableSlotsByTingkatan[$tingkatanId];
                $colors = $this->colorGraph($assignments); // Panggil fungsi pewarnaan graf
    
                // Mapping hasil pewarnaan ke jadwal
                foreach ($colors as $index => $color) {
                    $assignment = $assignments[$index];
                    $detail = $assignment->tugasMengajar->mataPelajaran->detailMataPelajaran
                        ->where('tingkatan_kelas_id', $assignment->kelas->tingkatan_kelas_id)
                        ->first();
    
                    if (!$detail) {
                        Log::error('Detail mata pelajaran tidak ditemukan.');
                        continue;
                    }
    
                    $totalHours = $detail->total_jam_perminggu;
                    $this->allocateSlots($assignment, $totalHours, $availableSlots, $finalSchedule, $tahunAjaranId, $counter);
                }
            }
    
            // Commit transaksi jika semua sukses
            DB::commit();
    
            return redirect()
                ->route('wakilkurikulum.penjadwalan.index')
                ->with('status', 'penjadwalan-created')
                ->with('message', 'Berhasil membuat Jadwal Mata pelajaran');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            Log::error('Error dalam generate jadwal: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('status','penjadwalan-error')
                ->with('message', 'Terjadi kesalahan saat membuat jadwal: ' . $e->getMessage());
        }
    }
        
    private function colorGraph($assignments)
    {
        $assignmentCount = $assignments->count();
        $adjacencyMatrix = array_fill(0, $assignmentCount, array_fill(0, $assignmentCount, 0));
    
        // Cek konflik
        foreach ($assignments as $i => $assignment1) {
            foreach ($assignments as $j => $assignment2) {
                if ($i !== $j && ($assignment1->tugasMengajar->guru_id === $assignment2->tugasMengajar->guru_id ||
                    ($assignment1->kelas->tingkatan_kelas_id === $assignment2->kelas->tingkatan_kelas_id &&
                    $assignment1->kelas_id === $assignment2->kelas_id))) {
                    $adjacencyMatrix[$i][$j] = 1; // Ada konflik
                }
            }
        }
    
        // Derajat simpul
        $degrees = array_map('array_sum', $adjacencyMatrix);
        arsort($degrees);
        $sortedVertices = array_keys($degrees);
    
        // Pewarnaan graf
        $colors = [];
        foreach ($sortedVertices as $vertex) {
            $usedColors = [];
            foreach ($adjacencyMatrix[$vertex] as $neighbor => $isConnected) {
                if ($isConnected && isset($colors[$neighbor])) {
                    $usedColors[] = $colors[$neighbor];
                }
            }
    
            // Cari warna yang tersedia
            $color = 1;
            while (in_array($color, $usedColors)) {
                $color++;
            }
            $colors[$vertex] = $color;
        }
    
        return $colors;
    }
        
    private function allocateSlots($assignment, $totalHours, $availableSlots, &$finalSchedule, $tahunAjaranId, &$counter)
    {
        // Jika total jam per minggu <= 3, coba alokasikan berurutan dalam satu hari
        if ($totalHours <= 3) {
            $availableSlotsInOneDay = $availableSlots->groupBy('hari.nama_hari');
            $allocatedSuccessfully = false;
    
            foreach ($availableSlotsInOneDay as $day => $daySlots) {
                // Coba berbagai kombinasi slot
                $combinations = $this->generateSlotCombinations($daySlots, $totalHours);
                
                foreach ($combinations as $slotsToAllocate) {
                    // Validasi tidak ada konflik guru atau kelas di slot yang sama
                    $canAllocate = collect($slotsToAllocate)->every(function ($slot) use ($assignment, $finalSchedule) {
                        return $this->isSlotAvailable($slot, $assignment, $finalSchedule);
                    });
                    
                    if ($canAllocate) {
                        foreach ($slotsToAllocate as $timeSlot) {
                            $finalSchedule[] = [
                                'subject' => $assignment->tugasMengajar->mataPelajaran->nama,
                                'teacher' => $assignment->tugasMengajar->guru->name,
                                'tingkatan_kelas_id' => $assignment->kelas->tingkatan_kelas_id,
                                'class' => $assignment->kelas->nama_kelas,
                                'day' => $timeSlot->hari->nama_hari,
                                'time' => $timeSlot->slotWaktu->mulai . '-' . $timeSlot->slotWaktu->selesai,
                            ];
                            
                            Jadwal::create([
                                'kelas_tugas_mengajar_id' => $assignment->id,
                                'slot_waktu_mapping_id' => $timeSlot->id,
                                'tahun_ajaran_id' => $tahunAjaranId,
                            ]);
                            $counter++;
                        }
                        $allocatedSuccessfully = true;
                        break;
                    }
                }
    
                if ($allocatedSuccessfully) {
                    break;
                }
            }
            
            // Jika tidak bisa dialokasikan, gunakan metode default
            if (!$allocatedSuccessfully) {
                $this->allocateSlotsDefault($assignment, $totalHours, $availableSlots, $finalSchedule, $tahunAjaranId, $counter);
            }
        } else {
            // Existing code for > 3 hours
            $this->allocateSlotsDefault($assignment, $totalHours, $availableSlots, $finalSchedule, $tahunAjaranId, $counter);
        }
    }

    private function allocateSlotsDefault($assignment, $totalHours, $availableSlots, &$finalSchedule, $tahunAjaranId, &$counter)
    {
        // Ambil slot waktu berdasarkan warna
        $slotsForColor = $availableSlots->filter(function ($slot) use ($assignment, $finalSchedule) {
            return $this->isSlotAvailable($slot, $assignment, $finalSchedule);
        });

        if ($slotsForColor->isEmpty()) {
            Log::error('Tidak ada slot waktu untuk assignment: ' . $assignment->id);
            return;
        }

        // Validasi berdasarkan total jam per minggu
        if ($totalHours == 2) {
            // Jika total jam per minggu 2, alokasikan dalam satu hari
            $timeSlotsAllocated = 0;
            $dayAllocated = null;

            foreach ($slotsForColor as $timeSlot) {
                if ($timeSlotsAllocated < 2) {
                    // Alokasikan slot waktu
                    $finalSchedule[] = [
                        'subject' => $assignment->tugasMengajar->mataPelajaran->nama,
                        'teacher' => $assignment->tugasMengajar->guru->name,
                        'tingkatan_kelas_id' => $assignment->kelas->tingkatan_kelas_id,
                        'class' => $assignment->kelas->nama_kelas,
                        'day' => $timeSlot->hari->nama_hari,
                        'time' => $timeSlot->slotWaktu->mulai . '-' . $timeSlot->slotWaktu->selesai,
                    ];

                    Jadwal::create([
                        'kelas_tugas_mengajar_id' => $assignment->id,
                        'slot_waktu_mapping_id' => $timeSlot->id,
                        'tahun_ajaran_id' => $tahunAjaranId,
                    ]);
                    $counter++;
                    $timeSlotsAllocated++;
                    $dayAllocated = $timeSlot->hari->nama_hari;
                }
            }

            // Jika sudah alokasikan 2 jam, pastikan tidak ada slot lain yang dialokasikan untuk hari yang sama
            if ($timeSlotsAllocated == 2) {
                $slotsForColor = $slotsForColor->filter(function ($slot) use ($dayAllocated) {
                    return $slot->hari->nama_hari !== $dayAllocated;
                });
            }

        } elseif ($totalHours > 3) {
            // Jika total jam per minggu lebih dari 3, alokasikan maksimal 3 dalam satu hari
            $remainingHours = $totalHours;

            foreach ($slotsForColor as $timeSlot) {
                if ($remainingHours <= 0) break;

                // Alokasikan slot waktu
                $finalSchedule[] = [
                    'subject' => $assignment->tugasMengajar->mataPelajaran->nama,
                    'teacher' => $assignment->tugasMengajar->guru->name,
                    'tingkatan_kelas_id' => $assignment->kelas->tingkatan_kelas_id,
                    'class' => $assignment->kelas->nama_kelas,
                    'day' => $timeSlot->hari->nama_hari,
                    'time' => $timeSlot->slotWaktu->mulai . '-' . $timeSlot->slotWaktu->selesai,
                ];

                Jadwal::create([
                    'kelas_tugas_mengajar_id' => $assignment->id,
                    'slot_waktu_mapping_id' => $timeSlot->id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                ]);
                $counter++;
                $remainingHours--;
            }
        } else {
            // Alokasikan slot waktu untuk total jam per minggu yang kurang dari atau sama dengan 3
            for ($i = 0; $i < $totalHours; $i++) {
                $timeSlot = $slotsForColor->shift();

                if (!$timeSlot) {
                    Log::error('Tidak cukup slot waktu untuk memenuhi jadwal');
                    break;
                }

                $finalSchedule[] = [
                    'subject' => $assignment->tugasMengajar->mataPelajaran->nama,
                    'teacher' => $assignment->tugasMengajar->guru->name,
                    'tingkatan_kelas_id' => $assignment->kelas->tingkatan_kelas_id,
                    'class' => $assignment->kelas->nama_kelas,
                    'day' => $timeSlot->hari->nama_hari,
                    'time' => $timeSlot->slotWaktu->mulai . '-' . $timeSlot->slotWaktu->selesai,
                ];

                Jadwal::create([
                    'kelas_tugas_mengajar_id' => $assignment->id,
                    'slot_waktu_mapping_id' => $timeSlot->id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                ]);
                $counter++;
            }
        }
    }

    private function generateSlotCombinations($daySlots, $totalHours)
    {
        $combinations = [];
        $slotCount = $daySlots->count();

        // Cari kombinasi slot yang memenuhi total jam
        for ($i = 0; $i <= $slotCount - $totalHours; $i++) {
            $combination = $daySlots->slice($i, $totalHours);
            if ($combination->count() == $totalHours) {
                $combinations[] = $combination;
            }
        }

        return $combinations;
    }

    private function isSlotAvailable($slot, $assignment, $finalSchedule)
    {
        foreach ($finalSchedule as $schedule) {
            // Cek konflik guru pada hari dan waktu yang sama
            if ($schedule['teacher'] === $assignment->tugasMengajar->guru->name &&
                $schedule['day'] === $slot->hari->nama_hari &&
                $schedule['time'] === ($slot->slotWaktu->mulai . '-' . $slot->slotWaktu->selesai)) {
                return false;
            }
            
            // Cek konflik kelas pada hari dan waktu yang sama
            if ($schedule['class'] === $assignment->kelas->nama_kelas &&
                $schedule['tingkatan_kelas_id'] == $assignment->kelas->tingkatan_kelas_id &&
                $schedule['day'] === $slot->hari->nama_hari &&
                $schedule['time'] === ($slot->slotWaktu->mulai . '-' . $slot->slotWaktu->selesai)) {
                return false;
            }
        }
        return true;
    }

    private function getAvailableSlotsByTingkatanKelas()
    {
        $excludedSlots = $this->getExcludedSlots();
            
        $slots = SlotWaktuMapping::with([
            'slotWaktuTingkatanKelas.slotWaktu',
            'slotWaktuTingkatanKelas.tingkatanKelas',
            'hari'
        ])
        ->whereNotIn('id', $excludedSlots)
        ->whereHas('slotWaktuTingkatanKelas.slotWaktu', function ($query) {
            $query->where('is_istirahat', false);
        })
        ->get();

        return $slots->groupBy(function($slot) {
            return $slot->slotWaktuTingkatanKelas->tingkatan_kelas_id;
        });
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

    public function cetakJadwalSemua(Request $request)
    {
        try {
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
    
}