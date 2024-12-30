@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Kurikulum') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.kurikulum.update', $kurikulum->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="flex space-x-6 justify-start">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                    <div>
                        <x-input-label for="nama_kurikulum" :value="__('Nama Kurikulum')"/>
                        <x-text-input id="nama_kurikulum" name="nama_kurikulum" type="text" class="mt-1 block w-full" required autofocus :value="old('nama_kurikulum', $kurikulum->nama_kurikulum)" />
                        <x-input-error :messages="$errors->get('nama_kurikulum')" class="mt-2" />
                    </div>
                </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
            <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.kurikulum.index') }}">
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

@if (session('status') === 'kurikulum-updated')
    <x-success-alert message="Berhasil mengubah mata pelajaran" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
