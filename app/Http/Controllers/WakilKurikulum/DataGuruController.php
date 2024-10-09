<?php

namespace App\Http\Controllers\WakilKurikulum;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use Illuminate\Support\Facades\Log;


class DataGuruController extends Controller
{
    public function createGuru()
    {
        $roles = Role::all()->pluck('display_name', 'id');
        $users = User::all()->pluck('username', 'id');

        return view('wakilkurikulum.guru.create', compact('roles', 'users'));
    }

    public function showGuru(Request $request)
    {
        $search = $request->input('search');
        
        $gurus = Guru::with('User') 
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->orWhereHas('User', function ($query) use ($search) {
                    $query->where('username', 'like', '%' . $search . '%');
                });
        })
        ->get();
    
        $guruCount = Guru::count();

        return view('wakilkurikulum.guru.index', compact('gurus', 'guruCount'));
    }

    public function storeGuru(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'nip' => 'required|string|unique:guru,nip',
                'total_jam_perminggu' => 'integer|min:0',
            ]);
            $totalJamPerminggu = $validatedData['total_jam_perminggu'] ?? 0;

            Guru::create([
                'name' => $validatedData['name'],
                'nip' => $validatedData['nip'],
                'total_jam_perminggu' => $totalJamPerminggu,
                'user_id' => $request->user_id,
            ]);

           return redirect()->back()->with('status', 'guru-created');
          
        } catch (\Exception $e) {
            Log::error('Terjadi error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->with('status', 'error')->withErrors(['message' => 'Gagal menambahkan akun'])->withInput();
        }
    }

    public function editGuru($id)
    {
        $guru = Guru::findOrFail($id);
        $users = User::all();
    
        return view('wakilkurikulum.guru.edit', [
            'guru' => $guru,
            'users' => $users,
        ]);
    }

    public function updateGuru(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255' . $guru->id,
            'nip' => 'nullable|string',
            'user_id' => 'exists:users,id',
            'total_jam_perminggu' => 'nullable|integer',
        ]);

        try {
            $guru->update([
                'name' => $request->name,
                'nip' => $request->nip,
                'user_id' => $request->user_id,
                'total_jam_perminggu' => $request->total_jam_perminggu,
            ]);

            $guru->user_id = $request->user_id;
            $guru->save();

            return redirect()->route('wakilkurikulum.guru.index')->with('status', 'guru-updated');
        } catch (\Exception $e) {
            Log::error('Error creating user: '.$e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat mengupdate data.'])->withInput();
        }
    }

    public function destroyGuru($id)
    {
        try {
            $guru = Guru::findOrFail($id);

            // $guru->users()->detach(); 
            $guru->delete();

       
            return redirect()->back()->with('status', 'guru-deleted');
        } catch (\Exception $e) {
            // Redirect back with an error message if something goes wrong
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data guru.']);
        }
    }
}
