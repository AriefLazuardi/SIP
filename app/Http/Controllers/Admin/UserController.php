<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function index(Request $request)
    {
       $search = $request->input('search');
       $perPage = 10;
       $users = User::with('userRole')
                   ->when($search, function($query, $search) {
                       return $query->where('username', 'like', '%' . $search . '%')
                                   ->orWhereHas('userRole', function($query) use ($search) {
                                       $query->where('display_name', 'like', '%' . $search . '%');
                                   });
                   })
                   ->paginate();
            

       // Return the view with the data
       return view('admin.user.index', compact('users'));
    }

   
    public function create()
    {
     
        $roles = Role::all()->pluck('display_name', 'id'); 

        return view('admin.user.create', compact('roles'));
    }

 
    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Membuat user user baru
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => bcrypt($request->password),
            ]);

            // Menambahkan role ke user
            $user->roles()->attach($request->role_id);

            // Redirect dengan pesan sukses
            return redirect()->back()->with('status', 'user-created');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRoleId = $user->roles->first()->id ?? null;

        return view('admin.user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoleId' => $userRoleId,
        ]);
    }
    
    public function update(Request $request, $id)
    {
        // Find the user
        $user = User::findOrFail($id);

        // Validate the input
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'username' => $request->username,
                'password' => $request->password ? bcrypt($request->password) : $user->password,
            ]);

            // Update the user role
            $user->roles()->sync([$request->role_id]);

            DB::commit();

            // Redirect with success message
            return redirect()->route('admin.user.index')->with('status', 'user-updated');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat mengupdate data.'])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Delete the user and detach any associated roles
            $user->roles()->detach(); // If using pivot table for roles
            $user->delete();

            // Redirect back with a success message
            return redirect()->back()->with('status', 'user-deleted');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus data user.']);
        }
    }
}