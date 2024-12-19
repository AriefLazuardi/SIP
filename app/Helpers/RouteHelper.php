<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

if (!function_exists('getDashboardRoute')) {
    function getDashboardRoute()
    {
        if (Auth::user()->hasRole('administrator')) {
            return 'admin.dashboard';
        } elseif (Auth::user()->hasRole('wakakurikulum')) {
            return 'wakilkurikulum.dashboard';
        } elseif (Auth::user()->hasRole('guru')) {
            return 'guru.dashboard';
        }
        
        // Default route jika tidak ada yang cocok
        return 'dashboard';
    }
}

if (!function_exists('setActiveClass')) {
    function setActiveClass($menu, $activeClass = 'bg-primaryColor text-white', $inactiveClass = 'bg-baseColor text-customColor')
    {
        // Definisikan routes yang terkait dengan setiap menu
        $routeMap = [
            'beranda' => ['admin.dashboard', 'wakilkurikulum.dashboard', 'guru.dashboard'],
            'kelola_akun' => ['admin.user.index', 'admin.user.edit', 'admin.user.create'],
            'guru' => ['admin.guru.index', 'admin.guru.edit', 'admin.guru.create'],
            'kurikulum' => ['admin.kurikulum.index', 'admin.kurikulum.edit', 'admin.kurikulum.create'],
            'mapel' => ['admin.mapel.index', 'admin.mapel.edit', 'admin.mapel.create', 'admin.mapel.detail'],
            'kelas' => ['admin.kelas.index', 'admin.kelas.edit', 'admin.kelas.create'],
            'ruangan' => ['admin.ruangan.index', 'admin.ruangan.edit', 'admin.ruangan.create'],
            'hari' => ['admin.hari.index', 'admin.hari.edit', 'admin.hari.create'],
            'walikelas' => ['admin.walikelas.index', 'admin.walikelas.edit', 'admin.walikelas.create'],
            'tahunajaran' => ['admin.tahunajaran.index', 'admin.tahunajaran.edit', 'admin.tahunajaran.create'],
            'jadwal' => ['admin.jadwal.index'],
            'slotwaktu' => ['admin.slotwaktu.index', 'admin.slotwaktu.edit', 'admin.slotwaktu.create'],
            'tugasmengajar' => ['wakilkurikulum.tugasmengajar.index', 'wakilkurikulum.tugasmengajar.edit', 'wakilkurikulum.tugasmengajar.create'],
            'penjadwalan' => ['wakilkurikulum.penjadwalan.index'],
            'jadwalKeseluruhan' => ['guru.jadwal.index'],
            'jadwalPribadi' => ['guru.jadwal.pribadi'],
            
        ];

        // Cek apakah menu ada di routeMap dan apakah route saat ini termasuk dalam array tersebut
        if (array_key_exists($menu, $routeMap)) {
            foreach ($routeMap[$menu] as $route) {
                if (Route::currentRouteName() === $route) {
                    return $activeClass;
                }
            }
        }

        return $inactiveClass;
    }
}