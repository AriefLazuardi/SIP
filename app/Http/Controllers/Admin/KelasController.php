<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\TingkatanKelas;
use App\Models\Kelas;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class KelasController extends Controller
{
    public function createKelas()
    {
        $kelas = Kelas::all()->pluck('nama_kelas', 'id');
        $tingkatanKelas = TingkatanKelas::all()->pluck('nama_tingkatan', 'id');
    
        return view('admin.kelas.create', compact('kelas', 'tingkatanKelas'));
    }

    public function showKelas(Request $request)
    {
        $search = $request->input('search');
        $perPage = 10;
        
        $kelass = Kelas::with('tingkatanKelas')
        ->when($search, function ($query, $search) {
            return $query->where('nama_kelas', 'like', '%' . $search . '%')
                ->orWhereHas('tingkatanKelas', function ($query) use ($search) {
                    $query->where('nama_tingkatan', 'like', '%' . $search . '%');
                });
        })
        ->paginate($perPage);

        $kelasCount = Kelas::count();

        return view('admin.kelas.index', compact('kelass', 'kelasCount'));
    }

    public function storeKelas(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_kelas' => 'required|string|max:1',
                'tingkatan_kelas_id' => 'required|exists:tingkatan_kelas,id',
            ]);
    

            Kelas::create([
                'nama_kelas' => $validatedData['nama_kelas'],
                'tingkatan_kelas_id' => $validatedData['tingkatan_kelas_id'],
            ]);
    
            return redirect()->route('admin.kelas.index')->with('status', 'kelas-created');
        } catch (\Exception $e) {
            Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->with('status', 'error')->withErrors(['message' => 'Gagal menambahkan kelas'])->withInput();
        }
    }

    public function editKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $tingkatan_kelas = TingkatanKelas::pluck('nama_tingkatan', 'id')->toArray();
    
        return view('admin.kelas.edit', [
            'kelas' => $kelas,
            'tingkatan_kelas' => $tingkatan_kelas,
            'selected_tingkatan_kelas_id' => $kelas->tingkatan_kelas_id,
        ]);
    }

    public function updateKelas(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            $validatedData = $request->validate([
                'nama_kelas' => 'required|string|max:255',
                'tingkatan_kelas_id' => 'required|exists:tingkatan_kelas,id',
            ]);

            $kelas->update([
                'nama_kelas' => $validatedData['nama_kelas'],
                'tingkatan_kelas_id' => $validatedData['tingkatan_kelas_id'],
            ]);

            return redirect()->route('admin.kelas.index')
                ->with('status', 'kelas-updated')
                ->with('message', 'Kelas berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Terjadi error saat memperbarui data: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('status', 'error')
                ->withErrors(['message' => 'Gagal memperbarui Kelas'])
                ->withInput();
        }
    }

    public function destroyKelas($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            // $guru->users()->detach(); 
            $kelas->delete();

       
            return redirect()->back()->with('status', 'kelas-deleted');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data kelas.']);
        }
    }


}
