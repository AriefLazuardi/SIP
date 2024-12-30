<?php

namespace App\Http\Controllers\WakilKurikulum;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TugasMengajar;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasTugasMengajar;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TugasMengajarController extends Controller
{
    public function showTugasMengajar()
    {
        $search = request('search');
        $perPage = 10; // Jumlah item per halaman
        
        $query = TugasMengajar::with(['guru', 'mataPelajaran', 'kelas.tingkatanKelas'])
        ->select('guru_id', 'mata_pelajaran_id', \DB::raw('MAX(id) as latest_id'))
            ->groupBy('guru_id', 'mata_pelajaran_id')
            ->orderBy('latest_id', 'DESC');
    
        if ($search) {
            $query->whereHas('guru', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('mataPelajaran', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }
    
        $allResults = $query->get();
        
        // Transform data
        $transformedResults = $allResults->map(function ($item) {
            $relatedTugas = TugasMengajar::where('guru_id', $item->guru_id)
                ->where('mata_pelajaran_id', $item->mata_pelajaran_id)
                ->with(['kelas.tingkatanKelas'])
                ->get();
    
            $kelas = $relatedTugas->pluck('kelas')->flatten();
    
            return (object)[
                'id' => $relatedTugas->first()->id,
                'guru' => $relatedTugas->first()->guru,
                'mataPelajaran' => $relatedTugas->first()->mataPelajaran,
                'kelas' => $kelas,
                'total_jam_perminggu' => $relatedTugas->first()->guru->total_jam_perminggu
            ];
        });
    
        // Manual pagination
        $currentPage = request()->get('page', 1);
        $tugasMengajars = new \Illuminate\Pagination\LengthAwarePaginator(
            $transformedResults->forPage($currentPage, $perPage),
            $transformedResults->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    
        return view('wakilkurikulum.tugasmengajar.index', compact('tugasMengajars'));
    }

public function createTugasMengajar()
{
    $gurus = Guru::all()->pluck('name', 'id');
    $mataPelajarans = MataPelajaran::all()->pluck('nama', 'id');

    $kelas = Kelas::with('tingkatanKelas')->get()->groupBy(function ($kelas) {
        return $kelas->tingkatanKelas->nama_tingkatan; 
    })->map(function ($groupedKelas) {
        return $groupedKelas->map(function ($kelas) {
            return [
                'id' => $kelas->id,
                'nama' => $kelas->nama_kelas
            ];
        });
    });

    return view('wakilkurikulum.tugasmengajar.create', compact('gurus', 'mataPelajarans', 'kelas'));
}



public function editTugasMengajar($id)
{
    $tugasMengajar = TugasMengajar::with(['guru', 'mataPelajaran', 'kelas.tingkatanKelas'])->findOrFail($id);
    $gurus = Guru::pluck('name', 'id');
    $mataPelajarans = MataPelajaran::pluck('nama', 'id');
    
    $kelas = Kelas::with('tingkatanKelas')
        ->get()
        ->groupBy(function ($kelas) {
            return $kelas->tingkatanKelas->nama_tingkatan;
        })
        ->map(function ($kelasGroup) {
            return $kelasGroup->map(function ($kelas) {
                return [
                    'id' => $kelas->id,
                    'nama' => $kelas->nama_kelas
                ];
            });
        });

    // Ambil semua tugas mengajar untuk guru yang sama
    $allTugasMengajar = TugasMengajar::with(['mataPelajaran', 'kelas.tingkatanKelas'])
        ->where('guru_id', $tugasMengajar->guru_id)
        ->get();

    // Format existing subjects
    $existingSubjects = $allTugasMengajar->map(function($tugas) {
        return [
            'id' => $tugas->id,
            'mata_pelajaran_id' => $tugas->mata_pelajaran_id,
            'kelas' => $tugas->kelas->map(function($k) {
                return $k->tingkatanKelas->nama_tingkatan . ' ' . $k->nama_kelas;
            })->toArray(),
            'kelas_ids' => $tugas->kelas->pluck('id')->toArray()
        ];
    })->toArray();

    if (empty($existingSubjects)) {
        $existingSubjects = [
            [
                'id' => 1,
                'mata_pelajaran_id' => '',
                'kelas' => [],
                'kelas_ids' => []
            ]
        ];
    }

    return view('wakilkurikulum.tugasmengajar.edit', compact(
        'tugasMengajar',
        'gurus',
        'mataPelajarans',
        'kelas',
        'existingSubjects'
    ));
}

public function storeTugasMengajar(Request $request)
{
    DB::beginTransaction();
    try {
        // Validasi request
        $validatedData = $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'mata_pelajaran_id' => 'required|array',
            'mata_pelajaran_id.*' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|array',
            'kelas_id.*' => 'required|exists:kelas,id',
        ]);

        $conflictErrors = [];

        // Lakukan pengecekan konflik untuk semua mata pelajaran dan kelas
        foreach ($request->mata_pelajaran_id as $index => $mataPelajaranId) {
            foreach ($request->kelas_id[$index] as $kelasId) {
                // Cari konflik dengan guru lain
                $conflictingTugasOtherTeacher = TugasMengajar::where('mata_pelajaran_id', $mataPelajaranId)
                    ->whereHas('kelas', function ($query) use ($kelasId) {
                        $query->where('kelas.id', $kelasId);
                    })
                    ->where('guru_id', '!=', $validatedData['guru_id'])
                    ->first();

                // Cari konflik dengan guru yang sama
                $conflictingTugasSameTeacher = TugasMengajar::where('mata_pelajaran_id', $mataPelajaranId)
                    ->whereHas('kelas', function ($query) use ($kelasId) {
                        $query->where('kelas.id', $kelasId);
                    })
                    ->where('guru_id', $validatedData['guru_id'])
                    ->first();

                // Jika ada konflik dengan guru lain
                if ($conflictingTugasOtherTeacher) {
                    $tingkatanKelas = Kelas::find($kelasId)->tingkatanKelas->nama_tingkatan ?? 'Tingkatan tidak diketahui';
                    $namaKelas = Kelas::find($kelasId)->nama_kelas ?? 'Kelas tidak diketahui';
                    $namaMataPelajaran = MataPelajaran::find($mataPelajaranId)->nama ?? 'Mata Pelajaran tidak diketahui';
                    $namaGuruKonflik = Guru::find($conflictingTugasOtherTeacher->guru_id)->name ?? 'Guru tidak diketahui';
                    
                    $conflictErrors[] = "Mata pelajaran {$namaMataPelajaran} di kelas {$tingkatanKelas}{$namaKelas} sudah diajarkan oleh guru {$namaGuruKonflik}";
                }

                // Jika ada konflik dengan guru yang sama (tugas mengajar duplikat)
                if ($conflictingTugasSameTeacher) {
                    $tingkatanKelas = Kelas::find($kelasId)->tingkatanKelas->nama_tingkatan ?? 'Tingkatan tidak diketahui';
                    $namaKelas = Kelas::find($kelasId)->nama_kelas ?? 'Kelas tidak diketahui';
                    $namaMataPelajaran = MataPelajaran::find($mataPelajaranId)->nama ?? 'Mata Pelajaran tidak diketahui';
                    
                    $conflictErrors[] = "Sudah ada Guru yang mengajar mata pelajaran {$namaMataPelajaran} di kelas {$tingkatanKelas}{$namaKelas}";
                }
            }
        }
       
        // Jika ada konflik, kembalikan dengan error
        if (!empty($conflictErrors)) {
            $errorMessage = 'Konflik ditemukan! ' . implode(' ', $conflictErrors);
            throw new \Exception($errorMessage);
        }

        // Ambil total jam mengajar sebelumnya
        $totalJamMengajar = Guru::find($validatedData['guru_id'])->total_jam_perminggu ?? 0;

        foreach ($request->mata_pelajaran_id as $index => $mataPelajaranId) {
            // Lanjutkan proses pembuatan tugas mengajar
            $tugasMengajar = TugasMengajar::create([
                'guru_id' => $validatedData['guru_id'],
                'mata_pelajaran_id' => $mataPelajaranId,
            ]);

            if (!empty($request->kelas_id[$index]) && is_array($request->kelas_id[$index])) {
                foreach ($request->kelas_id[$index] as $kelasId) {
                    KelasTugasMengajar::create([
                        'tugas_mengajar_id' => $tugasMengajar->id,
                        'kelas_id' => $kelasId,
                    ]);

                    // Hitung jam pelajaran
                    $jamPelajaran = DB::table('detail_mata_pelajaran')
                        ->where('mata_pelajaran_id', $mataPelajaranId)
                        ->where('tingkatan_kelas_id', function($query) use ($kelasId) {
                            $query->select('tingkatan_kelas_id')
                                ->from('kelas')
                                ->where('id', $kelasId);
                        })
                        ->value('total_jam_perminggu') ?? 0;

                    $totalJamMengajar += $jamPelajaran;
                }
            }
        }        

        // Update total jam mengajar guru
        Guru::where('id', $validatedData['guru_id'])->update([
            'total_jam_perminggu' => $totalJamMengajar,
        ]);

        DB::commit();
        return redirect()
            ->route('wakilkurikulum.tugasmengajar.index')
            ->with('status', 'tugasmengajar-created')
            ->with('message', 'Berhasil menambahkan tugas mengajar')
            ->with('icon', 'success');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
        
        return redirect()
            ->back()
            ->with('status', 'error')
            ->with('message','Gagal menambahkan tugas mengajar: ' . $e->getMessage())
            ->with('icon', 'error')
            ->withInput();
    }
}
    
    public function updateTugasMengajar(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validasi request
            $validatedData = $request->validate([
                'guru_id' => 'required|exists:guru,id',
                'mata_pelajaran_id' => 'required|array',
                'mata_pelajaran_id.*' => 'required|exists:mata_pelajaran,id',
                'kelas_id' => 'required|array',
                'kelas_id.*' => 'required|array',
                'kelas_id.*.*' => 'required|exists:kelas,id',
            ]);

            $conflictErrors = [];

            // Pertama, lakukan pengecekan konflik untuk semua mata pelajaran dan kelas
            foreach ($validatedData['mata_pelajaran_id'] as $index => $mataPelajaranId) {
                foreach ($validatedData['kelas_id'][$index] as $kelasId) {
                    // Cari konflik
                    $conflictingTugas = TugasMengajar::where('mata_pelajaran_id', $mataPelajaranId)
                        ->whereHas('kelas', function ($query) use ($kelasId) {
                            $query->where('kelas.id', $kelasId);
                        })
                        ->where('guru_id', '!=', $validatedData['guru_id'])
                        ->first();

                    // Jika ada konflik, simpan detail konflik
                    if ($conflictingTugas) {
                        $tingkatanKelas = Kelas::find($kelasId)->tingkatanKelas->nama_tingkatan ?? 'Tingkatan tidak diketahui';
                        $namaKelas = Kelas::find($kelasId)->nama_kelas ?? 'Kelas tidak diketahui';
                        $namaMataPelajaran = MataPelajaran::find($mataPelajaranId)->nama ?? 'Mata Pelajaran tidak diketahui';
                        $namaGuruKonflik = Guru::find($conflictingTugas->guru_id)->name ?? 'Guru tidak diketahui';
                        
                        $conflictErrors[] = "Mata pelajaran {$namaMataPelajaran} di kelas {$tingkatanKelas}{$namaKelas}  sudah diajarkan oleh guru {$namaGuruKonflik}";
                        
                    }
                }
            }
           
            // Jika ada konflik, kembalikan dengan error
        if (!empty($conflictErrors)) {
            $errorMessage = 'Konflik ditemukan! ' . implode(' ', $conflictErrors);
            throw new \Exception($errorMessage);
        }

            $totalJamMengajar = 0;

            // Hapus tugas mengajar yang lama
            TugasMengajar::where('guru_id', $validatedData['guru_id'])->delete();

            foreach ($validatedData['mata_pelajaran_id'] as $index => $mataPelajaranId) {
                // Lanjutkan proses pembuatan tugas mengajar
                $tugasMengajar = TugasMengajar::create([
                    'guru_id' => $validatedData['guru_id'],
                    'mata_pelajaran_id' => $mataPelajaranId,
                ]);

                if (!empty($validatedData['kelas_id'][$index])) {
                    foreach ($validatedData['kelas_id'][$index] as $kelasId) {
                        KelasTugasMengajar::create([
                            'tugas_mengajar_id' => $tugasMengajar->id,
                            'kelas_id' => $kelasId,
                        ]);

                        // Ambil total jam dari detail mata pelajaran
                        $jamPelajaran = DB::table('detail_mata_pelajaran')
                            ->where('mata_pelajaran_id', $mataPelajaranId)
                            ->where('tingkatan_kelas_id', function ($query) use ($kelasId) {
                                $query->select('tingkatan_kelas_id')
                                    ->from('kelas')
                                    ->where('id', $kelasId);
                            })
                            ->value('total_jam_perminggu') ?? 0;

                        $totalJamMengajar += $jamPelajaran;
                    }
                }
            }

            // Update total jam mengajar guru
            Guru::where('id', $validatedData['guru_id'])->update([
                'total_jam_perminggu' => $totalJamMengajar,
            ]);

            DB::commit();
            return redirect()
                ->route('wakilkurikulum.tugasmengajar.index')
                ->with('status', 'tugasmengajar-updated')
                ->with('message', 'Berhasil mengupdate tugas mengajar')
                ->with('icon', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Terjadi error saat mengupdate data: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('status', 'error')
                ->with('message', 'Gagal mengupdate tugas mengajar: ' . $e->getMessage())
                ->with('icon', 'error')
                ->withInput();
        }
    }

    public function destroyTugasMengajar($id)
    {
        DB::beginTransaction();
        try {
            $mapel = TugasMengajar::findOrFail($id);
            $guruId = $mapel->guru_id;

            $mapel->kelasTugasMengajar()->delete();
            $mapel->delete();

            $totalJamMengajar = TugasMengajar::where('guru_id', $guruId)
            ->get()
            ->sum(function ($tugas) {
                return DB::table('detail_mata_pelajaran')
                    ->where('mata_pelajaran_id', $tugas->mata_pelajaran_id)
                    ->whereIn('tingkatan_kelas_id', $tugas->kelas->pluck('tingkatan_kelas_id'))
                    ->sum('total_jam_perminggu') ?? 0;
            });

            Guru::where('id', $guruId)->update([
                'total_jam_perminggu' => $totalJamMengajar,
            ]);

            DB::commit();
            return redirect()->back()->with('status', 'tugasmengajar-deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Terjadi error saat menghapus data: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data mata pelajaran.']);
        }
    }
}
