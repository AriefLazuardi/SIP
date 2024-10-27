<?php

namespace App\Helpers;

class RouteTitleHelper {
    public static function getTitle($route) {
        $titles = [
            'profile.edit' => 'Akun',
            'home' => 'Beranda',
            'admin.user.create' => 'Tambah Akun',
            'admin.user.edit' => 'Edit Data Akun',
            'wakilkurikulum.guru.index' => 'Tabel Data Guru',
            'wakilkurikulum.guru.create' => 'Tambah Data Guru',
            'wakilkurikulum.guru.edit' => 'Edit Data Guru',
            'wakilkurikulum.kelas.index' => 'Tabel Data Kelas',
            'wakilkurikulum.kelas.create' => 'Tambah Data Kelas',
            'wakilkurikulum.kelas.edit' => 'Edit Data Kelas',
            'wakilkurikulum.mapel.index' => 'Tabel Data Mata Pelajaran',
            'wakilkurikulum.mapel.create' => 'Tambah Data Mata Pelajaran',
            'wakilkurikulum.mapel.edit' => 'Edit Data Mata Pelajaran',
            'wakilkurikulum.mapel.detail' => 'Detail Mata Pelajaran',
            'wakilkurikulum.ruangan.index' => 'Tabel Ruangan',
            'wakilkurikulum.ruangan.create' => 'Tambah Data Ruangan',
            'wakilkurikulum.ruangan.edit' => 'Edit Data Ruangan',
            'wakilkurikulum.walikelas.index' => 'Tabel Wali Kelas',
            'wakilkurikulum.walikelas.create' => 'Tambah Data Wali Kelas',
            'wakilkurikulum.walikelas.edit' => 'Edit Data Wali Kelas',
            'wakilkurikulum.hari.index' => 'Tabel Hari',
            'wakilkurikulum.hari.create' => 'Tambah Data Hari',
            'wakilkurikulum.hari.edit' => 'Edit Data Hari',
            'wakilkurikulum.slotwaktu.index' => 'Tabel Slot Waktu',
            'wakilkurikulum.slotwaktu.create' => 'Tambah Data Slot Waktu',
            'wakilkurikulum.slotwaktu.edit' => 'Edit Data Slot Waktu',
            'wakilkurikulum.tahunajaran.index' => 'Tabel Tahun Ajaran',
            'wakilkurikulum.tahunajaran.create' => 'Tambah Data Tahun Ajaran',
            'wakilkurikulum.tahunajaran.edit' => 'Edit Data Tahun Ajaran',
            'wakilkurikulum.tugasmengajar.index' => 'Tabel Tugas Mengajar',
            'wakilkurikulum.tugasmengajar.create' => 'Tambah Data Tugas Mengajar',
            'wakilkurikulum.tugasmengajar.edit' => 'Edit Data Tugas Mengajar',
        ];

        return $titles[$route] ?? 'Beranda';
    }
}