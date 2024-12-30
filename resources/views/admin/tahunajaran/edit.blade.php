@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Tahun Ajaran') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.tahunajaran.update', $tahunAjaran->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="flex space-x-6 justify-start">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="mulai" :value="__('Tanggal Mulai Tahun Ajaran')" />
                    <x-date-input id="mulai" name="mulai" type="text" class="mt-1 block w-full" required autofocus :value="old('mulai', $tahunAjaran->mulai)" />
                    <x-input-error :messages="$errors->get('mulai')" class="mt-2" />
                </div>
            </div>
            <div class="w-full md:w-1/2">
                    <div>
                        <x-input-label for="selesai" :value="__('Tanggal Berakhir Tahun Ajaran')"/>
                        <x-date-input id="selesai" name="selesai" type="text" class="mt-1 block w-full" required autofocus :value="old('selesai', $tahunAjaran->selesai)" />
                        <x-input-error :messages="$errors->get('selesai')" class="mt-2" />
                    </div>
                </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.tahunajaran.index') }}">
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
    flatpickr("#mulai", {
        dateFormat: "Y-m-d",
    });
    flatpickr("#selesai", {
        dateFormat: "Y-m-d",
    });
});

</script>
@if (session('status') === 'tahunajaran-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
