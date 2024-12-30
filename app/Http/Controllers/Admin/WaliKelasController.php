<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class WaliKelasController extends Controller
{
    public function createWaliKelas()
    {
        $waliKelas = WaliKelas::all();
        $kelas = Kelas::with('tingkatanKelas')->get()->map(function ($kelas) {
            return [
                'id' => $kelas->id,
                'name' => $kelas->tingkatanKelas->nama_tingkatan . $kelas->nama_kelas
            ];
        })->pluck('name', 'id');

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

        $existingWaliKelasGuruIds = WaliKelas::pluck('guru_id')->toArray();
        $gurus = Guru::whereNotIn('id', $existingWaliKelasGuruIds)
              ->get()
              ->pluck('name', 'id');

        return view('admin.walikelas.create', compact('waliKelas','kelas', 'gurus', 'tahunAjaran'));
    }

    public function showWaliKelas(Request $request)
    {
        $search = $request->input('search');
        $perPage = 10;

        $waliKelass = WaliKelas::with(['guru', 'kelas.tingkatanKelas', 'tahunajaran'])
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('kelas', function ($query) use ($search) {
                    $query->where('nama_kelas', 'like', '%' . $search . '%');
                })
                ->orWhereHas('guru', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('tahunajaran', function ($query) use ($search) {
                    $query->where('mulai', 'like', '%' . $search . '%')
                        ->orWhere('selesai', 'like', '%' . $search . '%');
                });
            });

        // Clone query untuk mendapatkan total count sebelum pagination
        $waliKelasCount = (clone $waliKelass)->count();

        // Ambil data dengan pagination
        $waliKelass = $waliKelass->paginate($perPage)
            ->through(function ($waliKelas) {
                if ($waliKelas->tahunajaran) {
                    $waliKelas->mulaiYear = date('Y', strtotime($waliKelas->tahunajaran->mulai));
                    $waliKelas->selesaiYear = date('Y', strtotime($waliKelas->tahunajaran->selesai));
                } else {
                    $waliKelas->mulaiYear = null;
                    $waliKelas->selesaiYear = null;
                }
                return $waliKelas;
            });

        return view('admin.walikelas.index', compact('waliKelass', 'waliKelasCount'));
    }

    public function storeWaliKelas(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'guru_id' => 'required|exists:guru,id',
                'kelas_id' => 'required|exists:kelas,id',
                'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            ]);
        
            // Membuat Wali Kelas baru
            WaliKelas::create([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
            ]);
    
            return redirect()->route('admin.walikelas.index')->with('status', 'wali-kelas-created');
        } catch (\Exception $e) {
            Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->with('status', 'error')->withErrors(['message' => 'Gagal menambahkan wali kelas'])->withInput();
        }
    }

    public function editWaliKelas($id)
    {
        $waliKelas = WaliKelas::findOrFail($id);
        $kelas = Kelas::with('tingkatanKelas')->get()->map(function ($kelas) {
            return [
                'id' => $kelas->id,
                'name' => $kelas->tingkatanKelas->nama_tingkatan . $kelas->nama_kelas // Pastikan 'nama_tingkatan' ada di model TingkatanKelas
            ];
        })->pluck('name', 'id');

        $tahunAjaran = TahunAjaran::all()->map(function ($tahun) {
            return [
                'id' => $tahun->id,
                'period' => date('Y', strtotime($tahun->mulai)) . '/' . date('Y', strtotime($tahun->selesai))
            ];
        })->unique('period')->sortByDesc('period')->pluck('period', 'id');
       
        $existingWaliKelasGuruIds = Walikelas::where('id', '!=', $id)->pluck('id')->toArray();
        $gurus = Guru::where(function($query) use ($existingWaliKelasGuruIds, $waliKelas) {
            $query->whereNotIn('id', $existingWaliKelasGuruIds)
                  ->orWhere('id', $waliKelas->gurus_id);
        })
        ->pluck('name', 'id');

        return view('admin.walikelas.edit', compact('waliKelas', 'kelas', 'tahunAjaran', 'gurus'));
    }

    public function updateWaliKelas(Request $request, $id)
    {
        try {
            $request->validate([
                'guru_id' => 'required|exists:guru,id',
                'kelas_id' => 'required|exists:kelas,id',
                'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            ]);
        
            $waliKelas = WaliKelas::findOrFail($id);
            
            $waliKelas->update([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
            ]);

            return redirect()->route('admin.walikelas.index')
                ->with('status', 'wali-kelas-updated')
                ->with('message', 'Wali Kelas berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Terjadi error saat memperbarui data: ' . $e->getMessage());

            return redirect()->back()
                ->with('status', 'error')
                ->withErrors(['message' => 'Gagal memperbarui wali kelas'])
                ->withInput();
        }
    }


    public function destroyWaliKelas($id)
    {
        try {
            $waliKelas = WaliKelas::findOrFail($id);
            $waliKelas->delete();

            return redirect()->back()->with('status', 'walikelas-deleted');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus wali kelas.']);
        }
    }
}
