<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\TingkatanKurikulum;
use App\Models\Kurikulum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class KurikulumController extends Controller
{
    public function createKurikulum()
    {
        $kurikulum = Kurikulum::all()->pluck('nama_kurikulum', 'id');
    
        return view('admin.kurikulum.create', compact('kurikulum'));
    }

    public function showKurikulum(Request $request)
    {
        $search = $request->input('search');
        $perPage = 10;
        $kurikulums = Kurikulum::when($search, function ($query, $search) {
            return $query->where('nama_kurikulum', 'like', '%' . $search . '%');
        })
        ->paginate($perPage);

        $kurikulumCount = Kurikulum::count();

        return view('admin.kurikulum.index', compact('kurikulums', 'kurikulumCount'));
    }

    public function storeKurikulum(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_kurikulum' => 'required|string|max:255',
            ]);
    

            Kurikulum::create([
                'nama_kurikulum' => $validatedData['nama_kurikulum'],
            ]);
    
            return redirect()->route('admin.kurikulum.index')->with('status', 'kurikulum-created');
        } catch (\Exception $e) {
            Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->with('status', 'error')->withErrors(['message' => 'Gagal menambahkan kurikulum'])->withInput();
        }
    }

    public function editKurikulum($id)
    {
        $kurikulum = Kurikulum::findOrFail($id);
    
        return view('admin.kurikulum.edit', [
            'kurikulum' => $kurikulum,
        ]);
    }

    public function updateKurikulum(Request $request, $id)
    {
        try {
            $kurikulum = Kurikulum::findOrFail($id);

            $validatedData = $request->validate([
                'nama_kurikulum' => 'required|string|max:255',
            ]);

            $kurikulum->update([
                'nama_kurikulum' => $validatedData['nama_kurikulum'],
            ]);

            return redirect()->route('admin.kurikulum.index')
                ->with('status', 'kurikulum-updated')
                ->with('message', 'Kurikulum berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Terjadi error saat memperbarui data: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('status', 'error')
                ->withErrors(['message' => 'Gagal memperbarui Kurikulum'])
                ->withInput();
        }
    }

    public function destroyKurikulum($id)
    {
        try {
            $kurikulum = Kurikulum::findOrFail($id);

            // $guru->users()->detach(); 
            $kurikulum->delete();

       
            return redirect()->back()->with('status', 'kurikulum-deleted');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data kurikulum.']);
        }
    }


}
