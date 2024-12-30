<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use App\Models\DetailMataPelajaran;
use App\Models\TingkatanKelas;
use App\Models\TahunAjaran;
use App\Models\Warna;
use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MataPelajaranController extends Controller
{
    public function createMapel()
    {
        $mapels = MataPelajaran::all()->pluck('nama', 'id');
        $usedWarnaIds = MataPelajaran::pluck('warna_id')->toArray();
        $warnas = Warna::whereNotIn('id', $usedWarnaIds)
        ->pluck('kode_hex', 'id');
        $tingkatanKelas = TingkatanKelas::orderBy('nama_tingkatan')
            ->pluck('nama_tingkatan', 'id');
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

        $kurikulum = Kurikulum::all()->pluck('nama_kurikulum', 'id'); 

        return view('admin.mapel.create', compact('mapels', 'warnas', 'tahunAjaran', 'tingkatanKelas', 'kurikulum'));
    }

    public function showMapel(Request $request)
    {
        $search = $request->input('search');
        $perPage = 10;
        
        $mapels = MataPelajaran::with('Warna')
        ->when($search, function ($query, $search) {
            return $query->where('nama', 'like', '%' . $search . '%');
        })
        ->paginate($perPage);

        $mapelCount = MataPelajaran::count();

        return view('admin.mapel.index', compact('mapels', 'mapelCount'));
    }
  
    public function storeMapel(Request $request) 
    {
        DB::beginTransaction();
        try {
            
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'warna_id' => 'required|exists:warna,id',
                'tingkatan_kelas_id' => 'required|array',
                'tingkatan_kelas_id.*' => 'array',
                'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
                'total_jam_perminggu' => 'required|array',
                'total_jam_perminggu.*' => 'integer|min:1',
                'kurikulum_id' => 'required|array',
                'kurikulum_id.*' => 'nullable|exists:kurikulum,id',
            ]);
            
            // Buat data mata pelajaran
            $mapel = MataPelajaran::create([
                'nama' => $validatedData['nama'],
                'warna_id' => $validatedData['warna_id'],
            ]);
    
            // Loop untuk setiap group tingkatan kelas
            foreach ($request->tingkatan_kelas_id as $groupIndex => $tingkatanKelasIds) {
                $jamPerminggu = $request->total_jam_perminggu[$groupIndex];
                $kurikulumId = $request->kurikulum_id[$groupIndex] ?? null;
                
                // Loop untuk setiap tingkatan kelas dalam group
                foreach ($tingkatanKelasIds as $tingkatanKelasId) {
                    DetailMataPelajaran::create([
                        'mata_pelajaran_id' => $mapel->id,
                        'tingkatan_kelas_id' => $tingkatanKelasId,
                        'tahun_ajaran_id' => $request->tahun_ajaran_id,
                        'total_jam_perminggu' => $jamPerminggu,
                        'kurikulum_id' => $kurikulumId,
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('admin.mapel.index')->with('status', 'mapel-created');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()
                ->with('status', 'error')
                ->withErrors(['message' => 'Gagal menambahkan mata pelajaran'])
                ->withInput();
        }
    }
   
    public function editMapel($id)
    {
        $mapel = MataPelajaran::with(['tingkatanKelas', 'detailMataPelajaran'])->findOrFail($id);
        $tingkatanKelas = TingkatanKelas::pluck('nama_tingkatan', 'id');
        $kurikulum = Kurikulum::pluck('nama_kurikulum', 'id'); 
        $usedWarnaIds = MataPelajaran::where('id', '!=', $id)->pluck('warna_id')->toArray();
        $warnas = Warna::where(function($query) use ($usedWarnaIds, $mapel) {
            $query->whereNotIn('id', $usedWarnaIds)
                  ->orWhere('id', $mapel->warna_id);
        })
        ->pluck('kode_hex', 'id');
        $tahunAjaran = TahunAjaran::all()->map(function ($tahun) {
            return [
                'id' => $tahun->id,
                'period' => date('Y', strtotime($tahun->mulai)) . '/' . date('Y', strtotime($tahun->selesai))
            ];
        })->pluck('period', 'id');
    
        // Kelompokkan detail mata pelajaran berdasarkan total jam perminggu
        $existingSubjects = [];
        $groupedDetails = $mapel->detailMataPelajaran->groupBy('total_jam_perminggu');
        
        foreach($groupedDetails as $jamPerminggu => $details) {
            $existingSubjects[] = [
                'id' => uniqid(),
                'total_jam_perminggu' => $jamPerminggu,
                'kurikulum_id' => $details->first()->kurikulum_id ?? '',
                'tingkatanKelas' => $details->pluck('tingkatanKelas.nama_tingkatan')->toArray(),
                'tingkatan_kelas_ids' => $details->pluck('tingkatan_kelas_id')->toArray()
            ];
        }
    
        // Jika tidak ada detail, buat array kosong
        if (empty($existingSubjects)) {
            $existingSubjects = [[
                'id' => uniqid(),
                'total_jam_perminggu' => '',
                'tingkatanKelas' => [],
                'kurikulum_id' => '',
                'tingkatan_kelas_ids' => []
            ]];
        }
        // dd($existingSubjects);
        return view('admin.mapel.edit', compact(
            'mapel',
            'tingkatanKelas',
            'warnas',
            'kurikulum',
            'tahunAjaran',
            'existingSubjects'
        ));
    }
    
    public function updateMapel(Request $request, $id)
    {
        DB::beginTransaction();
        try {
    
            // Validasi data
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'warna_id' => 'required|exists:warna,id',
                'tingkatan_kelas_ids' => 'required|array',
                'tingkatan_kelas_ids.*' => 'required|array',
                'tingkatan_kelas_ids.*.*' => 'exists:tingkatan_kelas,id',
                'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
                'total_jam_perminggu' => 'required|array',
                'total_jam_perminggu.*' => 'required|integer|min:1',
                'kurikulum_id' => 'required|array',
                'kurikulum_id.*' => 'nullable|exists:kurikulum,id',
            ]);
    
            // Temukan data mata pelajaran
            $mapel = MataPelajaran::findOrFail($id);
            $mapel->update([
                'nama' => $validatedData['nama'],
                'warna_id' => $validatedData['warna_id'],
            ]);
    
            // Hapus semua DetailMataPelajaran yang terkait
            DetailMataPelajaran::where('mata_pelajaran_id', $mapel->id)->delete();
    
            // Loop untuk setiap group
            foreach ($request->tingkatan_kelas_ids as $index => $tingkatanKelasIds) {
                $jamPerminggu = $request->total_jam_perminggu[$index];
                $kurikulumId = $request->kurikulum_id[$index] ?? null;
                
                // Buat detail untuk setiap tingkatan kelas dalam group
                foreach ($tingkatanKelasIds as $tingkatanKelasId) {
                    DetailMataPelajaran::create([
                        'mata_pelajaran_id' => $mapel->id,
                        'tingkatan_kelas_id' => $tingkatanKelasId,
                        'tahun_ajaran_id' => $request->tahun_ajaran_id,
                        'total_jam_perminggu' => $jamPerminggu,
                        'kurikulum_id' => $kurikulumId,
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('admin.mapel.index')->with('status', 'mapel-updated');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Terjadi error saat memperbarui data: ' . $e->getMessage());
            return redirect()->back()->with('status', 'error')->withErrors(['message' => 'Gagal memperbarui mata pelajaran'])->withInput();
        }
    }
    
    

    public function destroyMapel($id)
    {
        DB::beginTransaction();
        try {
            $mapel = MataPelajaran::findOrFail($id);

            $mapel->detailMataPelajaran()->delete();
            $mapel->delete();

            DB::commit();
            return redirect()->back()->with('status', 'mapel-deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Terjadi error saat menghapus data: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data mata pelajaran.']);
        }
    }

    public function showDetailMapel($id)
    {
        $mapel = MataPelajaran::with(['detailMataPelajaran' => function ($query) {
            $query->with(['tingkatanKelas', 'tahunAjaran', 'kurikulum']);
        }, 'warna'])->findOrFail($id);
        
        $detailMapelGrouped = [];

        if ($mapel->detailMataPelajaran->count() > 0) {
            foreach ($mapel->detailMataPelajaran as $detail) {
                $key = $detail->total_jam_perminggu . ' - ' . $detail->kurikulum->nama_kurikulum . ' - ' . 
                    substr($detail->tahunAjaran->mulai, 0, 4) . '/' . substr($detail->tahunAjaran->selesai, 0, 4);

                if (!isset($detailMapelGrouped[$key])) {
                    $detailMapelGrouped[$key] = [
                        'tingkatanKelas' => [],
                        'jamPerMinggu' => $detail->total_jam_perminggu,
                        'kurikulum' => $detail->kurikulum->nama_kurikulum,
                        'tahunAjaran' => substr($detail->tahunAjaran->mulai, 0, 4) . '/' . substr($detail->tahunAjaran->selesai, 0, 4),
                    ];
                }
                $detailMapelGrouped[$key]['tingkatanKelas'][] = $detail->tingkatanKelas->nama_tingkatan;
            }

            foreach ($detailMapelGrouped as $key => $value) {
                $detailMapelGrouped[$key]['tingkatanKelas'] = implode(', ', $value['tingkatanKelas']);
            }
        }

        return view('admin.mapel.detail', compact('mapel', 'detailMapelGrouped'));
    }

    
}
