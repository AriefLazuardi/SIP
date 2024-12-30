@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Kelas') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.kelas.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <!-- Horizontal layout for Password Sekarang and vertical stack for Password Baru and Konfirmasi Password -->
        <div class="flex space-x-6 justify-start">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="tingkatan_kelas_id" :value="__('Tingkatan Kelas')" />
                    <x-dropdown-custom name="tingkatan_kelas_id" :options="$tingkatanKelas" selected="{{ old('tingkatan_kelas_id') }}" placeholder="Pilih Tingkatan Kelas" class="w-full" />
                    <x-input-error :messages="$errors->get('tingkatan_kelas_id')" class="mt-2" />
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="nama_kelas" :value="__('Kelas')"/>
                    <x-text-input id="nama_kelas" name="nama_kelas" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" required autofocus />
                    <x-input-error :messages="$errors->get('nama_kelas')" class="mt-2" />
                </div>
            </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.kelas.index') }}">
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

<style>

.no-arrows {
    -moz-appearance: textfield; 
}

.no-arrows::-webkit-outer-spin-button,
.no-arrows::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

@if (session('status') === 'kelas-created')
    <x-success-alert message="Berhasil menambahkan mata pelajaran" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
