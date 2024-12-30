<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $guruCount = Guru::count();
        $mapelCount = MataPelajaran::count();
        $kelasCount = Kelas::count();
        $rolesCount = Role::count();
        $usersCount = User::count();

       // Return the view with the data
       return view('admin.dashboard', compact('guruCount', 'mapelCount', 'kelasCount', 'rolesCount', 'mapelCount', 'usersCount'));
    }
   
}
