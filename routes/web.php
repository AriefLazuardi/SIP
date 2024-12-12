<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\KurikulumController;
use App\Http\Controllers\Admin\DataGuruController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\RuanganController;
use App\Http\Controllers\Admin\WaliKelasController;
use App\Http\Controllers\Admin\HariController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\JadwalCetakController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\SlotWaktuController;
use App\Http\Controllers\WakilKurikulum\TugasMengajarController;
use App\Http\Controllers\WakilKurikulum\PenjadwalanController;
use App\Http\Controllers\WakilKurikulum\WakilKurikulumController;
use App\Http\Controllers\Guru\GuruController;
use App\Http\Controllers\Guru\JadwalGuruController;
use App\Http\Controllers\Guru\JadwalCetakGuruController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->hasRole('administrator')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('guru')) {
            return redirect()->route('guru.dashboard');
        } elseif ($user->hasRole('wakakurikulum')) {
            return redirect()->route('wakilkurikulum.dashboard');
        }
    }

    return view('auth.login');
});


// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/jadwal/cetak-guru', [JadwalCetakGuruController::class, 'cetakJadwalPribadi'])->name('cetak.jadwal-guru');
});

// Admin Routes
Route::middleware(['auth', 'role:administrator'])->prefix('admin')->name('admin.')->group(function () {
    // Akun
    Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('data-user', [UserController::class, 'index'])->name('user.index');
    Route::get('users/create', [UserController::class, 'create'])->name('user.create');
    Route::post('users/store', [UserController::class, 'store'])->name('user.store');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('users/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('users/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // Kurikulum
    Route::get('data-kurikulum', [KurikulumController::class, 'showKurikulum'])->name('kurikulum.index');
    Route::get('kurikulum/create', [KurikulumController::class, 'createKurikulum'])->name('kurikulum.create');
    Route::post('kurikulum/store', [KurikulumController::class, 'storeKurikulum'])->name('kurikulum.store');
    Route::get('kurikulum/edit/{id}', [KurikulumController::class, 'editKurikulum'])->name('kurikulum.edit');
    Route::put('kurikulum/update/{id}', [KurikulumController::class, 'updateKurikulum'])->name('kurikulum.update');
    Route::delete('kurikulum/delete/{id}', [KurikulumController::class, 'destroyKurikulum'])->name('kurikulum.destroy');

    // Kelas
    Route::get('data-kelas', [KelasController::class, 'showKelas'])->name('kelas.index');
    Route::get('kelas/create', [KelasController::class, 'createKelas'])->name('kelas.create');
    Route::post('kelas/store', [KelasController::class, 'storeKelas'])->name('kelas.store');
    Route::get('kelas/edit/{id}', [KelasController::class, 'editKelas'])->name('kelas.edit');
    Route::put('kelas/update/{id}', [KelasController::class, 'updateKelas'])->name('kelas.update');
    Route::delete('kelas/delete/{id}', [KelasController::class, 'destroyKelas'])->name('kelas.destroy');

    // Guru
    Route::get('data-guru', [DataGuruController::class, 'showGuru'])->name('guru.index');
    Route::get('guru/create', [DataGuruController::class, 'createGuru'])->name('guru.create');
    Route::post('guru/store', [DataGuruController::class, 'storeGuru'])->name('guru.store');
    Route::get('guru/edit/{id}', [DataGuruController::class, 'editGuru'])->name('guru.edit');
    Route::get('guru/detail/{id}', [DataGuruController::class, 'showDetailGuru'])->name('guru.detail');
    Route::put('guru/update/{id}', [DataGuruController::class, 'updateGuru'])->name('guru.update');
    Route::delete('guru/delete/{id}', [DataGuruController::class, 'destroyGuru'])->name('guru.destroy');

    // Wali Kelas
    Route::get('data-wali-kelas', [WaliKelasController::class, 'showWaliKelas'])->name('walikelas.index');
    Route::get('walikelas/create', [WaliKelasController::class, 'createWaliKelas'])->name('walikelas.create');
    Route::post('walikelas/store', [WaliKelasController::class, 'storeWaliKelas'])->name('walikelas.store');
    Route::get('walikelas/edit/{id}', [WaliKelasController::class, 'editWaliKelas'])->name('walikelas.edit');
    Route::put('walikelas/update/{id}', [WaliKelasController::class, 'updateWaliKelas'])->name('walikelas.update');
    Route::delete('walikelas/delete/{id}', [WaliKelasController::class, 'destroyWaliKelas'])->name('walikelas.destroy');

    // Mata Pelajaran
    Route::get('data-mapel', [MataPelajaranController::class, 'showMapel'])->name('mapel.index');
    Route::get('mapel/create', [MataPelajaranController::class, 'createMapel'])->name('mapel.create');
    Route::post('mapel/store', [MataPelajaranController::class, 'storeMapel'])->name('mapel.store');
    Route::get('mapel/edit/{id}', [MataPelajaranController::class, 'editMapel'])->name('mapel.edit');
    Route::get('mapel/detail/{id}', [MataPelajaranController::class, 'showDetailMapel'])->name('mapel.detail');
    Route::put('mapel/update/{id}', [MataPelajaranController::class, 'updateMapel'])->name('mapel.update');
    Route::delete('mapel/delete/{id}', [MataPelajaranController::class, 'destroyMapel'])->name('mapel.destroy');

     // Tahun Ajaran
     Route::get('data-tahun-ajaran', [TahunAjaranController::class, 'showTahunAjaran'])->name('tahunajaran.index');
     Route::get('tahunajaran/create', [TahunAjaranController::class, 'createTahunAjaran'])->name('tahunajaran.create');
     Route::post('tahunajaran/store', [TahunAjaranController::class, 'storeTahunAjaran'])->name('tahunajaran.store');
     Route::get('tahunajaran/edit/{id}', [TahunajaranController::class, 'editTahunAjaran'])->name('tahunajaran.edit');
     Route::put('tahunajaran/update/{id}', [TahunAjaranController::class, 'updateTahunAjaran'])->name('tahunajaran.update');
     Route::delete('tahunajaran/delete/{id}', [TahunAjaranController::class, 'destroyTahunAjaran'])->name('tahunajaran.destroy');
     // Slot Waktu
     Route::get('data-slot-waktu', [SlotWaktuController::class, 'showSlotWaktu'])->name('slotwaktu.index');
     Route::get('slotwaktu/create', [SlotWaktuController::class, 'createSlotWaktu'])->name('slotwaktu.create');
     Route::post('slotwaktu/store', [SlotWaktuController::class, 'storeSlotWaktu'])->name('slotwaktu.store');
     Route::get('slotwaktu/edit/{id}', [SlotWaktuController::class, 'editSlotWaktu'])->name('slotwaktu.edit');
     Route::put('slotwaktu/update/{id}', [SlotWaktuController::class, 'updateSlotWaktu'])->name('slotwaktu.update');
     Route::delete('slotwaktu/delete/{id}', [SlotWaktuController::class, 'destroySlotWaktu'])->name('slotwaktu.destroy');


         // Ruangan
    Route::get('data-ruangan', [RuanganController::class, 'showRuangan'])->name('ruangan.index');
    Route::get('ruangan/create', [RuanganController::class, 'createRuangan'])->name('ruangan.create');
    Route::post('ruangan/store', [RuanganController::class, 'storeRuangan'])->name('ruangan.store');
    Route::get('ruangan/edit/{id}', [RuanganController::class, 'editRuangan'])->name('ruangan.edit');
    Route::put('ruangan/update/{id}', [RuanganController::class, 'updateRuangan'])->name('ruangan.update');
    Route::delete('ruangan/delete/{id}', [RuanganController::class, 'destroyRuangan'])->name('ruangan.destroy');

    // Hari
    Route::get('data-hari', [HariController::class, 'showHari'])->name('hari.index');
    Route::get('hari/create', [HariController::class, 'createHari'])->name('hari.create');
    Route::post('hari/store', [HariController::class, 'storeHari'])->name('hari.store');
    Route::get('hari/edit/{id}', [HariController::class, 'editHari'])->name('hari.edit');
    Route::put('hari/update/{id}', [HariController::class, 'updateHari'])->name('hari.update');
    Route::delete('hari/delete/{id}', [HariController::class, 'destroyHari'])->name('hari.destroy');

    Route::get('jadwal', [JadwalController::class, 'showJadwal'])->name('jadwal.index');
    Route::get('/jadwal/cetak-semua', [JadwalCetakController::class, 'cetakJadwalSemua'])->name('cetak.jadwal');
});

// Wakil Kurikulum Routes
Route::middleware(['auth', 'role:wakakurikulum'])->prefix('wakilkurikulum')->name('wakilkurikulum.')->group(function () {
    Route::get('dashboard', [WakilKurikulumController::class, 'index'])->name('dashboard');


    // Tugas Mengajar
    Route::get('data-tugas-mengajar', [TugasMengajarController::class, 'showTugasMengajar'])->name('tugasmengajar.index');
    Route::get('tugasmengajar/create', [TugasMengajarController::class, 'createTugasMengajar'])->name('tugasmengajar.create');
    Route::post('tugasmengajar/store', [TugasMengajarController::class, 'storeTugasMengajar'])->name('tugasmengajar.store');
    Route::get('tugasmengajar/edit/{id}', [TugasMengajarController::class, 'editTugasMengajar'])->name('tugasmengajar.edit');
    Route::put('tugasmengajar/update/{id}', [TugasMengajarController::class, 'updateTugasMengajar'])->name('tugasmengajar.update');
    Route::delete('tugasmengajar/delete/{id}', [TugasMengajarController::class, 'destroyTugasMengajar'])->name('tugasmengajar.destroy');

    //Jadwal
    Route::get('penjadwalan', [PenjadwalanController::class, 'showJadwal'])->name('penjadwalan.index');
    Route::post('/generate-jadwal', [PenjadwalanController::class, 'generateJadwal'])->name('penjadwalan.generate');
    Route::get('/jadwal/cetak-semua', [JadwalCetakController::class, 'cetakJadwalSemua'])->name('cetak.jadwal');
    
});

// Guru Routes
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('dashboard', [GuruController::class, 'index'])->name('dashboard');
    Route::get('jadwal', [JadwalGuruController::class, 'showJadwal'])->name('jadwal.index');
    Route::get('jadwal/pribadi', [JadwalGuruController::class, 'showJadwalPribadi'])->name('jadwal.pribadi');
    Route::get('/jadwal/cetak-semua', [JadwalCetakController::class, 'cetakJadwalSemua'])->name('cetak.jadwal');

   
    // Add more guru routes here as needed
});


require __DIR__.'/auth.php';
