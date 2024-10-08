@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Guru') }}
        </h2>
    </header>

    <form method="post" action="{{ route('wakilkurikulum.guru.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <!-- Horizontal layout for Password Sekarang and vertical stack for Password Baru and Konfirmasi Password -->
        <div class="flex space-x-6 justify-end">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="name" :value="__('Nama')"/>
                    <x-text-input id="name" name="name" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="user" :value="__('User')" />
                    <x-dropdown-custom name="user_id" :options="$users" selected="{{ old('user_id') }}" placeholder="Pilih User" class="w-full" />
                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <div class="mb-4">
                    <x-input-label for="nip" :value="__('Nomor Identitas Pegawai')" />
                     <x-text-input id="nip" name="nip" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" />
                     <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                </div>
            </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-whiteColor py-2 px-4 rounded" onclick="window.history.back()">
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

@if (session('status') === 'guru-created')
    <x-success-alert message="Berhasil menambahkan guru" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
