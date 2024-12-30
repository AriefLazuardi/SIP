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
            {{ __('Edit Data Hari') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.hari.update', $hari->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="nama_hari" :value="__('Nama Hari')" /> 
                    <x-dropdown-custom id="nama_hari" name="nama_hari" :options="$options" class="mt-1 block w-full" :selected="old('nama_hari', $hari->nama_hari)" required autofocus />
                    <x-input-error :messages="$errors->get('nama_hari')" class="mt-2" />
                </div>
            </div>
        </div>

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


@if (session('status') === 'hari-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif

@endsection