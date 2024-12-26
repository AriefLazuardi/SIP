<?php

namespace App\Http\Controllers\Guru;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     private $colors = [
        '#FF5733', // Merah
        '#33FF57', // Hijau
        '#3357FF', // Biru
        '#F1C40F', // Kuning
        '#8E44AD', // Ungu
        '#FF7F50', // Coral
        '#00CED1', // Turquoise
        '#FFD700', // Emas
        '#FF69B4', // Hot Pink
        '#7FFF00', // Chartreuse
        '#FF4500', // Oranye Merah
        '#1E90FF', // Dodger Blue
        '#ADFF2F', // Green Yellow
        '#FF1493', // Deep Pink
        '#9400D3', // Violet
        '#00BFFF', // Deep Sky Blue
        '#FF8C00', // Dark Orange
        '#8A2BE2', // Blue Violet
        '#FF6347', // Tomato
        '#20B2AA', // Light Sea Green
    ];

    public function index()
    {
        $user = Auth::user();
    
        // Cari guru berdasarkan nip (username)
        $guru = Guru::where('nip', $user->username)->first();
        $currentTahunAjaranId = $this->getCurrentTahunAjaranId();
        $waliKelas = $guru ? $guru->waliKelas()->where('tahun_ajaran_id', $currentTahunAjaranId)->first() : null;
        $mataPelajaran = $guru->mataPelajaran()->with('warna')->get();
      
        $kelas = DB::table('kelas')
        ->join('tingkatan_kelas', 'kelas.tingkatan_kelas_id', '=', 'tingkatan_kelas.id')
        ->join('kelas_tugas_mengajar', 'kelas.id', '=', 'kelas_tugas_mengajar.kelas_id')
        ->join('tugas_mengajar', 'kelas_tugas_mengajar.tugas_mengajar_id', '=', 'tugas_mengajar.id')
        ->where('tugas_mengajar.guru_id', $guru['id'])
        ->select('kelas.id', 'kelas.nama_kelas', 'tingkatan_kelas.nama_tingkatan')
        ->distinct()
        ->get()
        ->groupBy('nama_tingkatan'); 


        $totalJamMengajar = $guru['total_jam_perminggu'];
        $pieChartData = [
            'totalJamMengajar' => $totalJamMengajar,
            'maksimalJam' => 30,
            'sisaJam' => 30 - $totalJamMengajar,
        ];

        // dd($mataPelajaran);
        $usedColors = [];

        $kelasGrouped = $kelas->groupBy('nama_tingkatan');

        // Tambahkan warna acak ke setiap kelas
        foreach ($kelas as $tingkatan => $kelasGroup) {
            foreach ($kelasGroup as $kelasItem) {
                $randomIndex = array_rand($this->colors);
                $kelasItem->color = $this->colors[$randomIndex];
                $usedColors[] = $this->colors[$randomIndex];
                unset($this->colors[$randomIndex]);
            }
        }

        $chartData = $this->fetchTeachingHoursData($guru['id']);
        
          
        // Jika guru ditemukan, tampilkan dashboard dengan data guru
        if ($guru) {
            return view('guru.dashboard', compact('guru', 'waliKelas', 'mataPelajaran', 'kelas', 'pieChartData', 'chartData', 'kelasGrouped'));
        } else {
            return back()->with('error', 'Guru tidak ditemukan.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function fetchTeachingHoursData($guruId)
    {
        $tahunAjaranId =$this->getCurrentTahunAjaranId();

        $teachingHours = DB::table('jadwal')
        ->join('kelas_tugas_mengajar', 'jadwal.kelas_tugas_mengajar_id', '=', 'kelas_tugas_mengajar.id')
        ->join('tugas_mengajar', 'kelas_tugas_mengajar.tugas_mengajar_id', '=', 'tugas_mengajar.id')
        ->join('slot_waktu_mapping', 'jadwal.slot_waktu_mapping_id', '=', 'slot_waktu_mapping.id')
        ->join('slot_waktu_tingkatan_kelas', 'slot_waktu_mapping.slot_waktu_tingkatan_kelas_id', '=', 'slot_waktu_tingkatan_kelas.id')
        ->join('slot_waktu', 'slot_waktu_tingkatan_kelas.slot_waktu_id', '=', 'slot_waktu.id')
        ->join('hari', 'slot_waktu_mapping.hari_id', '=', 'hari.id')
        ->where('tugas_mengajar.guru_id', $guruId)
        ->where('jadwal.tahun_ajaran_id', $tahunAjaranId) // Tambahkan filter tahun ajaran
        ->where('slot_waktu.is_istirahat', 0) // Kecualikan slot istirahat
        ->whereNotIn('slot_waktu_mapping.id', $this->getExcludedSlots()) // Kecualikan slot khusus
        ->select('hari.nama_hari', DB::raw('COUNT(DISTINCT jadwal.id) as total_hours'))
        ->groupBy('hari.nama_hari')
        ->get();
        
        
        return [
            'labels' => $teachingHours->pluck('nama_hari')->toArray(),
            'datasets' => [[
                'data' => $teachingHours->pluck('total_hours')->toArray(),
            ]]
        ];
        
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
