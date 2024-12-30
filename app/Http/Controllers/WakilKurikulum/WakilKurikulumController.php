<?php

namespace App\Http\Controllers\WakilKurikulum;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Http\Controllers\Controller;
use App\Models\SlotWaktuMapping;
use App\Models\TugasMengajar;
use Illuminate\Support\Facades\Log;

class WakilKurikulumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {     
        $guruCount = Guru::count();
        $mapelCount = MataPelajaran::count();
        $kelasCount = Kelas::count();
        $tugasMengajarCount = TugasMengajar::count();
        $slotWaktuCount = SlotWaktuMapping::count();
        Log::info('Slot Waktu Count: ' . $slotWaktuCount);


        return view('wakilkurikulum.dashboard', compact('guruCount', 'mapelCount', 'kelasCount', 'slotWaktuCount', 'tugasMengajarCount'));
    }



   
    
}
