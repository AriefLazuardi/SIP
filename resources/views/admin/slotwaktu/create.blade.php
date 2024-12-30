@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Slot Waktu') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.slotwaktu.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="tingkatan_kelas" :value="__('Tingkatan Kelas')" />
                    <div x-data="{ 
                            open: false, 
                            selected: [] 
                        }" 
                        x-init="selected = []" 
                        class="relative w-full">
                        <button type="button" @click="open = !open" class="border border-customColor rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
                            <span x-show="selected.length === 0">Pilih Tingkatan Kelas</span>
                            <span x-show="selected.length > 0" x-text="selected.join(', ')"></span>
                            <span class="material-icons ml-2">
                                <span x-show="!open">keyboard_arrow_down</span>
                                <span x-show="open">keyboard_arrow_up</span>
                            </span>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
                            <div class="p-2">
                                @foreach($tingkatanKelas as $id => $nama_tingkatan)
                                    <div class="flex items-center mb-2">
                                        <input 
                                            type="checkbox" 
                                            value="{{ $id }}"
                                            name="tingkatan_kelas[]" 
                                            @change="if($event.target.checked) { selected.push('{{ $nama_tingkatan }}'); } else { selected = selected.filter(item => item !== '{{ $nama_tingkatan }}'); }" 
                                            id="tingkatan_kelas_{{ $id }}"
                                            :checked="selected.includes('{{ $nama_tingkatan }}') ? true : false"
                                        >
                                        <label for="tingkatan_kelas_{{ $id }}" class="ml-2">{{ $nama_tingkatan }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('tingkatan_kelas')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="mulai" :value="__('Mulai')"/>
                    <x-text-input id="mulai" name="mulai" type="time" placeholder="Ketikkan disini" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('mulai')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="sesi_belajar_id" :value="__('Sesi Belajar')" />
                    <x-dropdown-custom name="sesi_belajar_id" :options="$sesiBelajar" selected="{{ old('sesi_belajar_id') }}" placeholder="Pilih Sesi Belajar" class="w-full" />
                    <x-input-error :messages="$errors->get('sesi_belajar_id')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <div class="form-check">
                        <input type="checkbox" 
                            name="is_istirahat" 
                            class="form-check-input rounded" 
                            value="1" 
                            id="isIstirahat">
                        <label class="form-check-label text-dangerColor text-sm ml-1" for="isIstirahat">
                            *Centang apabila waktu istirahat
                        </label>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="hari" :value="__('Hari')" />
                    <div x-data="{ 
                            open: false, 
                            selected: [] 
                        }" 
                        x-init="selected = []" 
                        class="relative w-full">
                        <button type="button" @click="open = !open" class="border border-customColor rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
                            <span x-show="selected.length === 0">Pilih Hari</span>
                            <span x-show="selected.length > 0" x-text="selected.join(', ')"></span>
                            <span class="material-icons ml-2">
                                <span x-show="!open">keyboard_arrow_down</span>
                                <span x-show="open">keyboard_arrow_up</span>
                            </span>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
                            <div class="p-2">
                                @foreach($hari as $id => $nama_hari)
                                    <div class="flex items-center mb-2">
                                        <input 
                                            type="checkbox" 
                                            value="{{ $id }}"
                                            name="hari[]" 
                                            @change="if($event.target.checked) { selected.push('{{ $nama_hari }}'); } else { selected = selected.filter(item => item !== '{{ $nama_hari }}'); }" 
                                            id="hari_{{ $id }}"
                                            :checked="selected.includes('{{ $nama_hari }}') ? true : false"
                                        >
                                        <label for="hari_{{ $id }}" class="ml-2">{{ $nama_hari }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('hari')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="selesai" :value="__('Selesai')"/>
                    <x-text-input id="selesai" name="selesai" type="time" placeholder="Ketikkan disini" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('selesai')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex space-x-5 justify-center">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded w-full" href="{{ route('admin.slotwaktu.index') }}">
                    {{ __('Batal') }}
                </x-cancel-button>
            </div>
            <div class="justify-center mb-4 md:w-1/2">
                <x-save-button class="bg-primaryColor text-white py-2 px-4 rounded w-full">
                    {{ __('Simpan') }}
                </x-save-button>
            </div>
        </div>
    </form>
</div>

@if (session('status') === 'slot-waktu-created')
    <x-success-alert message="Berhasil menambahkan slot waktu" icon="success" />
@elseif (session('status') === 'error' || $errors->any())
    <x-error-alert message="{{ $errors->first('message') ?? session('message') }}" icon="error">
        @if ($errors->any())
            <ul class="mt-2 text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </x-error-alert>
@endif
@endsection