<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Models\Hari;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class HariController extends Controller
{
    public function createHari()
    {
        $hari = Hari::all()->pluck('nama_hari', 'id');
       
        return view('admin.hari.create', compact('hari'));
    }

    public function showHari(Request $request)
    {
        $search = $request->input('search');
        
        $haris = Hari::when($search, function ($query, $search) {
            return $query->where('nama_hari', 'like', '%' . $search . '%');
        })
        ->get();
    
        $hariCount = Hari::count();
    
        return view('admin.hari.index', compact('haris', 'hariCount'));
    }

    public function storeHari(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            ]);
    

           Hari::create([
                'nama_hari' => $validatedData['nama_hari'],
            ]);
    
            return redirect()->route('admin.hari.index')->with('status', 'hari-created');
        } catch (\Exception $e) {
            Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->with('status', 'error')->withErrors(['message' => 'Gagal menambahkan hari'])->withInput();
        }
    }

    public function editHari($id)
    {
        $hari = Hari::findOrFail($id);
     
    
        return view('admin.hari.edit', [
            'hari' => $hari,
        ]);
    }

    public function updatehari(Request $request, $id)
    {
        try {
            $hari = Hari::findOrFail($id);

            $validatedData = $request->validate([
                'nama_hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            ]);

            $hari->update([
                'nama_hari' => $validatedData['nama_hari'],
            ]);

            return redirect()->route('admin.hari.index')
                ->with('status', 'hari-updated')
                ->with('message', 'hari berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Terjadi error saat memperbarui data: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('status', 'error')
                ->withErrors(['message' => 'Gagal memperbarui Hari'])
                ->withInput();
        }
    }

    public function destroyHari($id)
    {
        try {
            $hari = Hari::findOrFail($id);

            // $guru->users()->detach(); 
            $hari->delete();

       
            return redirect()->back()->with('status', 'hari-deleted');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data Hari.']);
        }
    }
}
