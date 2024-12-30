@extends('layouts.utama')

@section('content')

@php
$options = [
    'Senin' => 'Senin',
    'Selasa' => 'Selasa',
    'Rabu' => 'Rabu',
    'Kamis' => 'Kamis',
    'Jumat' => 'Jumat',
    'Sabtu' => 'Sabtu',
    'Minggu' => 'Minggu',
];
@endphp

<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Hari') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.hari.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <!-- Horizontal layout for Password Sekarang and vertical stack for Password Baru and Konfirmasi Password -->
        <div class="flex space-x-6 justify-start">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="nama_hari" :value="__('Nama Hari')" />
                    <x-dropdown-custom id="nama_hari" name="nama_hari" :options="$options" :selected="$hari" placeholder="Pilih Hari" class="mt-1 block w-full" required autofocus />
                    <x-input-error :messages="$errors->get('nama_hari')" class="mt-2" />
                </div>
            </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.hari.index') }}">
                    {{ __('Batal') }}
                </x-cancel-button>
            </div>
            <div class="justify-center mb-4 md:w-1/2">
                <x-save-button class="bg-primaryColor text-white py-2 px-4 rounded">
                    {{ __('Simpan') }}
                </x-save-button>
            </div>
        </div>
    </form>
</div>

@if (session('status') === 'hari-created')
    <x-success-alert message="Berhasil menambahkan hari" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
