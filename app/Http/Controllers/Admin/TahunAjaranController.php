<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class TahunAjaranController extends Controller
{
    public function createTahunAjaran()
    {
        $tahunajarans = TahunAjaran::all();
    
        return view('admin.tahunajaran.create', compact('tahunajarans'));
    }

    public function showTahunAjaran(Request $request)
    {
        $search = $request->input('search');
        
        $tahunAjarans = TahunAjaran::when($search, function ($query, $search) {
            return $query->where('mulai', 'like', '%' . $search . '%')
                        ->orWhere('selesai', 'like', '%' . $search . '%');
        })
        ->get();
    
        $tahunAjaranCount = Tahunajaran::count();
    
        return view('admin.tahunajaran.index', compact('tahunAjarans', 'tahunAjaranCount'));
    }

    public function storeTahunAjaran(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'mulai' => 'required|date',
                'selesai' => 'required|date|after:mulai',
            ]);
            
            $mulaiDate = $validatedData['mulai'];
            $selesaiDate = $validatedData['selesai'];

            TahunAjaran::create([
                'mulai' => $mulaiDate,
                'selesai' => $selesaiDate,
            ]);
    
            return redirect()->route('admin.tahunajaran.index')->with('status', 'tahunAjaran-created');
        } catch (\Exception $e) {
            Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->with('status', 'error')->withErrors(['message' => 'Gagal menambahkan tahunajaran'])->withInput();
        }
    }

    public function editTahunAjaran($id)
    {
        $tahunAjaran = Tahunajaran::findOrFail($id);
       
    
        return view('admin.tahunajaran.edit', [
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    public function updateTahunAjaran(Request $request, $id)
    {
        try {
            $tahunAjaran = TahunAjaran::findOrFail($id);

            $validatedData = $request->validate([
                'mulai' => 'required|date',
                'selesai' => 'required|date|after:mulai',
            ]);

            $mulaiDate = $validatedData['mulai'];
            $selesaiDate = $validatedData['selesai'];

            $tahunAjaran->update([
                'mulai' => $mulaiDate,
                'selesai' => $selesaiDate,
            ]);

            return redirect()->route('admin.tahunajaran.index')
                ->with('status', 'tahunAjaran-updated')
                ->with('message', 'tahunajaran berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Terjadi error saat memperbarui data: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('status', 'error')
                ->withErrors(['message' => 'Gagal memperbarui tahunajaran'])
                ->withInput();
        }
    }

    public function destroyTahunAjaran($id)
    {
        try {
            $tahunAjaran = TahunAjaran::findOrFail($id);

            // $guru->users()->detach(); 
            $tahunAjaran->delete();

       
            return redirect()->back()->with('status', 'tahunAjaran-deleted');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data tahunajaran.']);
        }
    }
}
