@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Ruangan') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.ruangan.update', $ruangan->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="flex space-x-6 justify-start">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="nama_ruangan" :value="__('Nama Ruangan')"/>
                    <x-text-input id="nama_ruangan" name="nama_ruangan" type="text" class="mt-1 block w-full" required autofocus :value="old('nama_ruangan', $ruangan->nama_ruangan)" />
                    <x-input-error :messages="$errors->get('nama_ruangan')" class="mt-2" />
                </div>
            </div>
            <div class="w-full md:w-1/2">
                    <div>
                        <x-input-label for="kapasitas" :value="__('Kapasitas')"/>
                        <x-text-input id="kapasitas" name="kapasitas" type="text" class="mt-1 block w-full" required autofocus :value="old('kapasitas', $ruangan->kapasitas)" />
                        <x-input-error :messages="$errors->get('kapasitas')" class="mt-2" />
                    </div>
                </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.ruangan.index') }}">
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

@if (session('status') === 'ruangan-updated')
    <x-success-alert message="Berhasil mengubah mata pelajaran" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
