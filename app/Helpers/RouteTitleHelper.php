<?php

namespace App\Helpers;

class RouteTitleHelper {
    public static function getTitle($route) {
        $titles = [
            'profile.edit' => 'Profil',
            'home' => 'Beranda',
            'admin.user.create' => 'Tambah Akun',
            'admin.user.edit' => 'Edit Data Akun',
            'admin.guru.index' => 'Tabel Data Guru',
            'admin.guru.create' => 'Tambah Data Guru',
            'admin.guru.edit' => 'Edit Data Guru',
            'admin.kelas.index' => 'Tabel Data Kelas',
            'admin.kelas.create' => 'Tambah Data Kelas',
            'admin.kelas.edit' => 'Edit Data Kelas',
            'admin.mapel.index' => 'Tabel Data Mata Pelajaran',
            'admin.mapel.create' => 'Tambah Data Mata Pelajaran',
            'admin.mapel.edit' => 'Edit Data Mata Pelajaran',
            'admin.mapel.detail' => 'Detail Mata Pelajaran',
            'admin.ruangan.index' => 'Tabel Ruangan',
            'admin.ruangan.create' => 'Tambah Data Ruangan',
            'admin.ruangan.edit' => 'Edit Data Ruangan',
            'admin.walikelas.index' => 'Tabel Wali Kelas',
            'admin.walikelas.create' => 'Tambah Data Wali Kelas',
            'admin.walikelas.edit' => 'Edit Data Wali Kelas',
            'admin.hari.index' => 'Tabel Hari',
            'admin.hari.create' => 'Tambah Data Hari',
            'admin.hari.edit' => 'Edit Data Hari',
            'admin.tahunajaran.index' => 'Tabel Tahun Ajaran',
            'admin.tahunajaran.create' => 'Tambah Data Tahun Ajaran',
            'admin.tahunajaran.edit' => 'Edit Data Tahun Ajaran',
            'admin.slotwaktu.index' => 'Tabel Slot Waktu',
            'admin.slotwaktu.create' => 'Tambah Data Slot Waktu',
            'admin.slotwaktu.edit' => 'Edit Data Slot Waktu',
            'wakilkurikulum.tugasmengajar.index' => 'Tabel Tugas Mengajar',
            'wakilkurikulum.tugasmengajar.create' => 'Tambah Data Tugas Mengajar',
            'wakilkurikulum.tugasmengajar.edit' => 'Edit Data Tugas Mengajar',
            'guru.jadwal.index' => 'Penjadwalan',
            'guru.jadwal.pribadi' => 'Penjadwalan',
        ];

        return $titles[$route] ?? 'Beranda';
    }
}