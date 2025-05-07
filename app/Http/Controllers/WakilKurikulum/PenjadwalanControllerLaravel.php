<?php

namespace App\Http\Controllers\WakilKurikulum;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\SlotWaktu;
use App\Models\TingkatanKelas;
use App\Models\Ruangan;
use App\Models\DetailMataPelajaran;
use App\Models\KelasTugasMengajar;
use App\Models\SlotWaktuMapping;
use App\Models\TahunAjaran;
use App\Models\Hari;
use App\Http\Controllers\Controller;
use App\Models\SlotWaktuTingkatanKelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenjadwalanController extends Controller
{
    private $conflicts = [];
     private $colorAssignments = [];
    private $conflictDetails = [];

    public function showJadwal()
    {
        $jadwal = Jadwal::with([
            'kelasTugasMengajar.kelas.tingkatanKelas',
            'kelasTugasMengajar.tugasMengajar.guru',
            'kelasTugasMengajar.tugasMengajar.mataPelajaran',
            'slotWaktuMapping.slotWaktuTingkatanKelas.slotWaktu',
            'slotWaktuMapping.hari',
            'ruangan',                'tahunAjaran'
        ])->get();
        
            // Ambil data kelas dari database
        $kelas = Kelas::with('tingkatanKelas')
            ->orderBy('tingkatan_kelas_id')
            ->orderBy('nama_kelas')                ->get()
             ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->tingkatanKelas->nama_tingkatan . $item->nama_kelas
               ];
        })->toArray();
        
            // Ambil slot waktu dari database dan urutkan berdasarkan waktu mulai
        $slotWaktuCollection = SlotWaktu::orderBy('mulai')->get();
        
        $slotWaktu = [];
        foreach($slotWaktuCollection as $sw) {
            $slotWaktu[] = [
                'id' => $sw->id,
                    'waktu' => $sw->mulai->format('H:i') . ' - ' . $sw->selesai->format('H:i')
                ];
            }
            // dd($slotWaktu);
        
            // Initialize jadwal table
            $jadwalTable = [];
            foreach ($slotWaktu as $time) {
                foreach ($kelas as $k) {
                    $jadwalTable[$time['waktu']][$k['nama']] = [
                        'mataPelajaran' => '-',
                        'guru' => '-'
                    ];
                }
            }
        
            // Fill in the jadwal data
            foreach ($jadwal as $item) {
                $mulai = $item->slotWaktuMapping->slotWaktuTingkatanKelas->slotWaktu->mulai->format('H:i');
                $selesai = $item->slotWaktuMapping->slotWaktuTingkatanKelas->slotWaktu->selesai->format('H:i');
                $slotWaktuString = "$mulai - $selesai";
                
                $kelasNama = $item->kelasTugasMengajar->kelas->tingkatanKelas->nama_tingkatan . 
                            $item->kelasTugasMengajar->kelas->nama_kelas;
                $mataPelajaran = $item->kelasTugasMengajar->tugasMengajar->mataPelajaran->name;
                $guru = $item->kelasTugasMengajar->tugasMengajar->guru->name;
        
                if (isset($jadwalTable[$slotWaktuString][$kelasNama])) {
                    $jadwalTable[$slotWaktuString][$kelasNama] = [
                        'mataPelajaran' => $mataPelajaran,
                        'guru' => $guru
                    ];
                }
            }
            $hasJadwal = $jadwal->isNotEmpty();
        
            return view('wakilkurikulum.penjadwalan.index', compact('jadwalTable', 'slotWaktu', 'kelas', 'hasJadwal'));
        }
    public function generateJadwal()
    {
        try {
            DB::beginTransaction();
            Log::channel('penjadwalan')->info('Memulai proses generate jadwal');
            Jadwal::where('tahun_ajaran_id', $this->getCurrentTahunAjaranId())->delete();
        
            $tahunAjaranId = $this->getCurrentTahunAjaranId();
            Log::channel('penjadwalan')->info('Tahun ajaran ID: ' . $tahunAjaranId);
            $kelasList = KelasTugasMengajar::with([
                'kelas.tingkatanKelas', 
                'tugasMengajar.mataPelajaran',
                'tugasMengajar.guru'
            ])->get();
            Log::channel('penjadwalan')->info('Jumlah kelas: ' . $kelasList->count());
            // Bangun graf konflik
            $this->buildConflictGraph($kelasList);
            // Terapkan algoritma Welch-Powell
            $coloredNodes = $this->applyWelchPowell();
            // Generate jadwal berdasarkan pewarnaan graf
            $jadwal = $this->generateScheduleFromColoring($coloredNodes, $tahunAjaranId);
            Log::channel('penjadwalan')->info('Jadwal yang dihasilkan:', ['jadwal' => $jadwal]);
            // Validasi hasil generate jadwal
            if (!$this->validateSchedule($jadwal)) {
                throw new \Exception('Terdapat konflik yang tidak dapat diselesaikan.');
            }

            if (!empty($jadwal)) {
                Jadwal::insert($jadwal);
                DB::commit();
                Log::channel('penjadwalan')->info('Jadwal berhasil digenerate', [
                    'total_jadwal' => count($jadwal)
            ]);

            return response()->json([
                'message' => 'Jadwal berhasil digenerate.',
                'total_jadwal' => count($jadwal),
                'jadwal' => $jadwal // tambahkan data jadwal untuk debugging
            ]);
            }

            throw new \Exception('Tidak dapat membuat jadwal yang sesuai dengan constraint.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('penjadwalan')->error('Error detail: ' . $e->getMessage());
            Log::channel('penjadwalan')->error('Stack trace: ' . $e->getTraceAsString());
            Log::channel('penjadwalan')->error('Gagal generate jadwal: ' . $e->getMessage());
                // $this->notifyConflict($e->getMessage());
        return response()->json([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

        private function validateSchedule($jadwal)
        {
            $isValid = true;
            $this->conflictDetails = [];

            // Validasi ruangan
            if (!$this->validateRoomAvailability($jadwal)) {
                $this->conflictDetails[] = [
                    'type' => 'room_conflict',
                    'message' => 'Terdapat konflik penggunaan ruangan'
                ];
                $isValid = false;
            }

            // Validasi jadwal guru
            if (!$this->validateTeacherSchedule($jadwal)) {
                $this->conflictDetails[] = [
                    'type' => 'teacher_conflict',
                    'message' => 'Terdapat konflik jadwal mengajar guru'
                ];
                $isValid = false;
            }

            // Validasi jadwal kelas
            if (!$this->validateClassSchedule($jadwal)) {
                $this->conflictDetails[] = [
                    'type' => 'class_conflict',
                    'message' => 'Terdapat konflik jadwal kelas'
                ];
                $isValid = false;
            }

            if (!$isValid) {
                Log::channel('penjadwalan')->error('Validasi jadwal gagal', [
                    'conflicts' => $this->conflictDetails
                ]);
            }

            return $isValid;
        }
        private function validateClassSchedule($jadwal)
        {
            $classSchedules = [];
            
            foreach ($jadwal as $schedule) {
                $kelasTugasMengajar = KelasTugasMengajar::with('kelas')
                    ->find($schedule['kelas_tugas_mengajar_id']);
                
                $kelasId = $kelasTugasMengajar->kelas_id;
                $key = $schedule['slot_waktu_mapping_id'] . '-' . $kelasId;
                
                if (isset($classSchedules[$key])) {
                    Log::channel('penjadwalan')->error('Konflik jadwal kelas terdeteksi', [
                        'kelas_id' => $kelasId,
                        'slot_id' => $schedule['slot_waktu_mapping_id']
                    ]);
                    return false;
                }
                
                $classSchedules[$key] = $schedule['kelas_tugas_mengajar_id'];
            }
            
            return true;
        }

        private function validateTeacherSchedule($jadwal)
            {
                $teacherSchedules = [];
                
                foreach ($jadwal as $schedule) {
                    $kelasTugasMengajar = KelasTugasMengajar::with('tugasMengajar.guru')
                        ->find($schedule['kelas_tugas_mengajar_id']);
                    
                    // Pastikan `tugasMengajar` adalah koleksi dan iterasi di dalamnya
                    foreach ($kelasTugasMengajar->tugasMengajar as $tugasMengajar) {
                        $guruId = $tugasMengajar->guru_id;
                        $key = $schedule['slot_waktu_mapping_id'] . '-' . $guruId;
                        
                        if (isset($teacherSchedules[$key])) {
                            Log::channel('penjadwalan')->error('Konflik jadwal guru terdeteksi', [
                                'guru_id' => $guruId,
                                'slot_id' => $schedule['slot_waktu_mapping_id']
                            ]);
                            return false;
                        }
                        
                        $teacherSchedules[$key] = $schedule['kelas_tugas_mengajar_id'];
                    }
                }
                
                return true;
            }
            private function buildConflictGraph($kelasList)
            {
                $this->conflicts = [];
                foreach ($kelasList as $kelas1) {
                    $this->conflicts[$kelas1->id] = [];
                    foreach ($kelasList as $kelas2) {
                        if ($kelas1->id !== $kelas2->id) {
                            foreach ($kelas1->tugasMengajar as $tugas1) {
                                if (!isset($tugas1->guru_id)) continue;
                                foreach ($kelas2->tugasMengajar as $tugas2) {
                                    if (!isset($tugas2->guru_id)) continue;
                                    if ($tugas1->guru_id === $tugas2->guru_id) {
                                        $this->conflicts[$kelas1->id][] = $kelas2->id;
                                        continue 2; 
                                    }
                                }
                            }
                            // Konflik jika kelas berbagi ruangan yang sama
                            if ($this->usesSameRoom($kelas1, $kelas2)) {
                                $this->conflicts[$kelas1->id][] = $kelas2->id;
                            }
                        }
                    }
                }
                Log::channel('penjadwalan')->info('Graf konflik berhasil dibangun', [
                    'total_nodes' => count($this->conflicts)
        ]);
    }
            

    private function usesSameRoom($kelas1, $kelas2)
    {
        return $kelas1->kelas->tingkatan_kelas_id === $kelas2->kelas->tingkatan_kelas_id;
    }

    private function applyWelchPowell()
    {
            // Urutkan node berdasarkan derajat (jumlah konflik) tertinggi
        $orderedNodes = collect($this->conflicts)
        ->map(function($conflicts, $nodeId) {
            return [
                'id' => $nodeId,
                'degree' => count($conflicts)
            ];
        })
        ->sortByDesc('degree')
        ->pluck('id');

        $this->colorAssignments = [];
        $currentColor = 0;

        $this->exportToDotFile('graf_sebelum_pewarnaan'. now()->timestamp . '.dot');

        while ($orderedNodes->isNotEmpty()) {
            $coloredNodesThisRound = [];
                
            foreach ($orderedNodes as $nodeId) {
                if (!isset($this->colorAssignments[$nodeId])) {
                    if ($this->canAssignColor($nodeId, $currentColor)) {
                        $this->colorAssignments[$nodeId] = $currentColor;
                        $coloredNodesThisRound[] = $nodeId;
                    }
                }
            }
                
            // Hapus node yang sudah diwarnai
            $orderedNodes = $orderedNodes->diff($coloredNodesThisRound);
            $currentColor++;
        }
        $this->exportToDotFile('graf_setelah_pewarnaan' . now()->timestamp. '.dot');
        Log::channel('penjadwalan')->info('Pewarnaan graf selesai', [
            'total_colors' => $currentColor,
            'assignments' => $this->colorAssignments
        ]);

        return $this->colorAssignments;
    }
        private function exportToDotFile($filename)
    {
        $dotContent = "graph G {\n";

        foreach ($this->conflicts as $node => $neighbors) {
            foreach ($neighbors as $neighbor) {
                $dotContent .= "  \"$node\" -- \"$neighbor\";\n";
            }
        }

        // Tambahkan warna untuk node yang sudah diwarnai
        foreach ($this->colorAssignments as $node => $color) {
            $dotContent .= "  \"$node\" [style=filled, fillcolor=\"/pastel19/$color\"];\n";
        }

        $dotContent .= "}";

        // Simpan file DOT ke direktori storage
        file_put_contents(storage_path($filename), $dotContent);
    }

        private function canAssignColor($nodeId, $color)
        {
            foreach ($this->conflicts[$nodeId] as $conflictingNode) {
                if (isset($this->colorAssignments[$conflictingNode]) 
                    && $this->colorAssignments[$conflictingNode] === $color) {
                    return false;
                }
            }
            return true;
        }
    private function getAvailableSlots($tingkatanKelasId, $slotWaktuMapping)
    {
        return $slotWaktuMapping->filter(function ($slot) use ($tingkatanKelasId) {
            $hari = $slot->hari->nama_hari;
            $sesiId = $slot->slotWaktuTingkatanKelas->id;
            // Aturan Upacara dan Tadarus berdasarkan tingkat kelas
            if (in_array($tingkatanKelasId, [5, 6])) {
                if ($hari === 'Senin' && in_array($sesiId, [1, 2])) return false; // Upacara
                if (in_array($hari, ['Selasa', 'Rabu', 'Kamis', 'Jumat']) && $sesiId === 1) return false; // Tadarus
            }
            elseif (in_array($tingkatanKelasId, [3, 4])) {
                if ($sesiId === 1) return false; // Tadarus
            }

            $kelasMataPelajaran = $this->getKelasMataPelajaran($tingkatanKelasId);
            if ($kelasMataPelajaran->total_jam_perminggu > 2 && $sesiId > 2) {
                return false;
            }
            return true;
        });
    }

    private function getKelasMataPelajaran($tingkatanKelasId)
    {
        return DetailMataPelajaran::where('tingkatan_kelas_id', $tingkatanKelasId)
        ->first();
    }


   private function generateScheduleFromColoring($coloredNodes, $tahunAjaranId)
    {
        $jadwal = [];
        $slotWaktuMappings = SlotWaktuMapping::with('hari', 'sesiBelajar')->get();
        $ruanganMapping = $this->initializeRuanganMapping();
        
        // Ambil semua jadwal untuk guru dan kelas di tahun ajaran ini sekaligus
        $jadwalGuru = Jadwal::where('tahun_ajaran_id', $tahunAjaranId)
            ->with('kelasTugasMengajar.tugasMengajar.guru')
            ->get();
        
        $jadwalKelas = Jadwal::where('tahun_ajaran_id', $tahunAjaranId)
            ->whereIn('kelas_tugas_mengajar_id', KelasTugasMengajar::pluck('id'))
            ->get();
        
        $kelasList = KelasTugasMengajar::with(['kelas.tingkatanKelas', 'tugasMengajar.guru'])->get();
        foreach ($kelasList as $kelas) {
            $kelasId = $kelas->id;
            $timeSlotColor = $coloredNodes[$kelasId] ?? null;
 
            if ($timeSlotColor !== null) {
                $availableSlots = $this->getAvailableSlots($kelas->kelas->tingkatan_kelas_id, $slotWaktuMappings);
                Log::channel('penjadwalan')->info('Available slots untuk kelas ' . $kelasId, ['available_slots' => $availableSlots]);

                foreach ($availableSlots as $slot) {
                    if ($this->shouldSchedule($kelas, $slot, $jadwalGuru, $jadwalKelas)) {
                        $ruanganId = $this->findAvailableRoom($kelas, $slot, $ruanganMapping);
                        Log::channel('penjadwalan')->info('Mapping ruangan untuk slot ' . $slot->id . ' dan ruangan ' . $ruanganId, [
                            'ruangan_mapping' => $ruanganMapping
                        ]);
                        

                        if ($ruanganId) {
                            // Periksa apakah ruangan sudah terpakai
                            if (!isset($ruanganMapping[$slot->id][$ruanganId])) {
                                $ruanganMapping[$slot->id][$ruanganId] = $kelasId;

                                $jadwal[] = [
                                    'slot_waktu_mapping_id' => $slot->id,
                                    'kelas_tugas_mengajar_id' => $kelasId,
                                    'ruangan_id' => $ruanganId,
                                    'tahun_ajaran_id' => $tahunAjaranId,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];

                                Log::channel('penjadwalan')->info('Jadwal ditambahkan', [
                                    'kelas_id' => $kelasId,
                                    'slot_id' => $slot->id,
                                    'ruangan_id' => $ruanganId
                                ]);

                                break;
                            }
                        }
                    }
                }
            }
        }
        Log::channel('penjadwalan')->info('Time slot color untuk kelas ' . $kelasId, ['timeSlotColor' => $timeSlotColor]);

        return $jadwal;
    }
    private function shouldSchedule($kelas, $slot, $jadwalGuru, $jadwalKelas)
    {
        $guru = $kelas->tugasMengajar->guru;
        
        // Cek ketersediaan guru dari cache jadwal guru
        $isGuruAvailable = !$jadwalGuru->where('kelasTugasMengajar.tugasMengajar.guru.id', $guru->id)
                                        ->where('slot_waktu_mapping_id', $slot->id)
                                        ->isNotEmpty();
    
        if (!$isGuruAvailable) {
            Log::channel('penjadwalan')->info('Guru tidak tersedia', ['guru_id' => $guru->id]);
            return false;
        }
    
        // Cek ketersediaan kelas dari cache jadwal kelas
        $isKelasScheduled = $jadwalKelas->where('kelas_tugas_mengajar_id', $kelas->id)
                                        ->where('slot_waktu_mapping_id', $slot->id)
                                        ->isNotEmpty();
    
        if ($isKelasScheduled) {
            Log::channel('penjadwalan')->info('Kelas sudah terjadwal', ['kelas_id' => $kelas->id, 'slot_id' => $slot->id]);
            return false;
        }
    
        // Cek tingkatan kelas
        if ($kelas->kelas->tingkatan_kelas_id !== $slot->tingkatan_kelas_id) {
            Log::channel('penjadwalan')->info('Tingkatan kelas tidak sesuai dengan slot', [
                'kelas_id' => $kelas->id,
                'tingkatan_kelas_id' => $kelas->kelas->tingkatan_kelas_id,
                'slot_tingkatan_kelas_id' => $slot->tingkatan_kelas_id
            ]);
            return false;
        }
    
        return true;
    }

        private function initializeRuanganMapping()
        {
            $mapping = [];
            $slotWaktuMappings = SlotWaktuMapping::all();
            $ruangan = Ruangan::all();
            foreach ($slotWaktuMappings as $slot) {
                $mapping[$slot->id] = [];
                foreach ($ruangan as $room) {
                    $mapping[$slot->id][$room->id] = null;
                }
            }
        
            $kelasGrouping = $this->getKelasGrouping();
            foreach ($kelasGrouping as $group) {
                $this->reserveRoomsForClassGroup($mapping, $group);
            }
            
        
            return $mapping;
        }
    
        
        private function getKelasGrouping()
        {
            $kelasGrouping = [];
            $kelas = Kelas::with('tingkatanKelas')->get();
        
            foreach ($kelas as $k) {
                $kelasGrouping[$k->tingkatanKelas->id][] = $k->id;
            }
        
            return $kelasGrouping;
        }
        
        private function reserveRoomsForClassGroup($mapping, $classGroup)
        {
            // Pastikan setiap kelas dalam satu kelompok menggunakan ruangan yang sama
            $ruangan = Ruangan::all();
            $ruanganIndex = 0;
        
            foreach ($classGroup as $kelasId) {
                $kelas = Kelas::find($kelasId);
                $slotWaktuTingkatanKelas = SlotWaktuTingkatanKelas::where('tingkatan_kelas_id', $kelasId)->get();
                foreach ($slotWaktuTingkatanKelas as $slot) {
                    $mapping[$slot->id][$kelas->ruangan_id] = $kelasId;
                }
        
                $ruanganIndex++;
            }
        }

        private function findAvailableRoom($kelas, $slot, &$ruanganMapping)
        {
            $availableRooms = DB::table('ruangan')->get();

            foreach ($availableRooms as $room) {
                if (!isset($ruanganMapping[$slot->id][$room->id]) || 
                    $ruanganMapping[$slot->id][$room->id] === null) {
                    return $room->id;
                }
            }
            Log::channel('penjadwalan')->info('Mencari ruangan untuk kelas ' . $kelas->id . ' pada slot ' . $slot->id);

            Log::warning('Tidak ada ruangan tersedia', [
                'kelas_id' => $kelas->id,
                'slot_id' => $slot->id
            ]);

            return null;
        }

        private function validateRoomAvailability($jadwal)
        {
            $roomUsage = [];
            
            foreach ($jadwal as $schedule) {
                $key = $schedule['slot_waktu_mapping_id'] . '-' . $schedule['ruangan_id'];
                
                if (isset($roomUsage[$key])) {
                    Log::error('Konflik penggunaan ruangan terdeteksi', [
                        'slot_id' => $schedule['slot_waktu_mapping_id'],
                        'ruangan_id' => $schedule['ruangan_id'],
                        'kelas_1' => $roomUsage[$key],
                        'kelas_2' => $schedule['kelas_tugas_mengajar_id']
                    ]);
                    
                    return false;
                }
                
                $roomUsage[$key] = $schedule['kelas_tugas_mengajar_id'];
            }
            
            return true;
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

