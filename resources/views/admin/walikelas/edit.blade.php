@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Wali Kelas') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.walikelas.update', $waliKelas->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="kelas_id" :value="__('Kelas')" />
                    <x-dropdown-custom name="kelas_id" :options="$kelas" selected="{{ old('kelas_id', $waliKelas->kelas_id) }}" placeholder="Pilih Kelas" class="w-full" />
                    <x-input-error :messages="$errors->get('kelas_id')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="tahun_ajaran_id" :value="__('Tahun Ajaran')" />
                    <x-dropdown-custom name="tahun_ajaran_id" :options="$tahunAjaran" selected="{{ old('tahun_ajaran_id', $waliKelas->tahun_ajaran_id) }}" placeholder="Pilih Tahun Ajaran" class="w-full" />
                    <x-input-error :messages="$errors->get('tahun_ajaran_id')" class="mt-2" />
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="guru" :value="__('Guru')"/>
                    <x-dropdown-custom name="guru_id" :options="$gurus" selected="{{ old('guru_id', $waliKelas->guru_id) }}" placeholder="Pilih Guru" class="w-full" />
                    <x-input-error :messages="$errors->get('guru_id')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.walikelas.index') }}">
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

@if (session('status') === 'walikelas-updated')
    <x-success-alert message="Berhasil mengupdate data wali kelas" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat mengupdate data." icon="error" />
@endif
@endsection