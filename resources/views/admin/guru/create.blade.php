@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Guru') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.guru.store') }}" class="mt-6 space-y-6">
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
                <div class="mt-4">
                    <x-input-label for="tempat_lahir" :value="__('Tempat Lahir')" />
                     <x-text-input id="tempat_lahir" name="tempat_lahir" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" />
                     <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="jabatan" :value="__('Jabatan')" />
                     <x-text-input id="jabatan" name="jabatan" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" />
                     <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <div class="mb-4">
                    <x-input-label for="nip" :value="__('Nomor Identitas Pegawai')" />
                     <x-text-input id="nip" name="nip" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" />
                     <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                </div>
                <div class="mt-24">
                    <x-input-label for="tanggal_lahir" :value="__('Tanggal Lahir')" />
                    <x-date-input id="tanggal_lahir" name="tanggal_lahir" type="text" placeholder="Pilih Tanggal" class="mt-1 block w-full" required autofocus />
                    <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="golongan" :value="__('Golongan')" />
                     <x-text-input id="golongan" name="golongan" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" />
                     <x-input-error :messages="$errors->get('golongan')" class="mt-2" />
                </div>
            </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.guru.index') }}">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#tanggal_lahir", {
        dateFormat: "Y-m-d",
    });
});

</script>
@if (session('status') === 'guru-created')
    <x-success-alert message="Berhasil menambahkan guru" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
