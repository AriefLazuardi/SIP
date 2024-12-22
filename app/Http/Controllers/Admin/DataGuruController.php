<?php

namespace App\Http\Controllers\Admin;

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
        $usedUserIds = Guru::whereNotNull('user_id')->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $usedUserIds)->pluck('username', 'id');

        
        return view('admin.guru.create', compact('roles', 'users'));
    }

    public function showGuru(Request $request)
    {
        $search = $request->input('search');
        $perPage = 10;
        
        $gurus = Guru::with('User')
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%')
                ->orWhere('tempat_lahir', 'like', '%' . $search . '%')
                ->orWhere('jabatan', 'like', '%' . $search . '%')
                ->orWhereHas('User', function ($query) use ($search) {
                    $query->where('username', 'like', '%' . $search . '%');
                });
        })
        ->paginate($perPage);

        $guruCount = Guru::count();

        return view('admin.guru.index', compact('gurus', 'guruCount'));
    }

    public function storeGuru(Request $request)
    {
        try {
            Log::info('Tanggal Lahir: ' . $request->input('tanggal_lahir'));
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'nip' => 'required|string|unique:guru,nip',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'jabatan' => 'nullable|string|max:255',
                'golongan' => 'nullable|string|max:255',
            ]);
            $totalJamPerminggu = $validatedData['total_jam_perminggu'] ?? 0;

            Guru::create([
                'name' => $validatedData['name'],
                'nip' => $validatedData['nip'],
                'tempat_lahir' => $validatedData['tempat_lahir'],
                'tanggal_lahir' => $validatedData['tanggal_lahir'],
                'jabatan' => $validatedData['jabatan'],
                'golongan' => $validatedData['golongan'],
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
    
        return view('admin.guru.edit', [
            'guru' => $guru,
            'users' => $users,
        ]);
    }

    public function updateGuru(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jabatan' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:255',
            'total_jam_perminggu' => 'nullable|integer',
            'user_id' => 'exists:users,id',
        ]);
        try {
            $guru->update($validatedData);
            return redirect()->route('admin.guru.index')->with('status', 'guru-updated');
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

    public function showDetailGuru($id){
        $guru = Guru::find($id);
        

        return view('admin.guru.detail', compact('guru'));
    }

}
