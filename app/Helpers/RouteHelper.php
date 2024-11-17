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
            'guru' => ['wakilkurikulum.guru.index', 'wakilkurikulum.guru.edit', 'wakilkurikulum.guru.create'],
            'mapel' => ['wakilkurikulum.mapel.index', 'wakilkurikulum.mapel.edit', 'wakilkurikulum.mapel.create', 'wakilkurikulum.mapel.detail'],
            'kelas' => ['wakilkurikulum.kelas.index', 'wakilkurikulum.kelas.edit', 'wakilkurikulum.kelas.create'],
            'ruangan' => ['wakilkurikulum.ruangan.index', 'wakilkurikulum.ruangan.edit', 'wakilkurikulum.ruangan.create'],
            'hari' => ['wakilkurikulum.hari.index', 'wakilkurikulum.hari.edit', 'wakilkurikulum.hari.create'],
            'walikelas' => ['wakilkurikulum.walikelas.index', 'wakilkurikulum.walikelas.edit', 'wakilkurikulum.walikelas.create'],
            'slotwaktu' => ['wakilkurikulum.slotwaktu.index', 'wakilkurikulum.slotwaktu.edit', 'wakilkurikulum.slotwaktu.create'],
            'tahunajaran' => ['wakilkurikulum.tahunajaran.index', 'wakilkurikulum.tahunajaran.edit', 'wakilkurikulum.tahunajaran.create'],
            'tugasmengajar' => ['wakilkurikulum.tugasmengajar.index', 'wakilkurikulum.tugasmengajar.edit', 'wakilkurikulum.tugasmengajar.create'],
            
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