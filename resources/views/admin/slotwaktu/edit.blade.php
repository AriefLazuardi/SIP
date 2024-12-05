@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Slot Waktu') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.slotwaktu.update', $slotWaktu->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="tingkatan_kelas" :value="__('Tingkatan Kelas')" />
                    <div x-data="tingkatanSelector" class="relative w-full">
                        <button type="button" @click="open = !open" class="border border-customColor rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
                            <span x-show="selected.length === 0">Pilih Tingkatan Kelas</span>
                            <span x-show="selected.length > 0" x-text="getTingkatanNames()"></span>
                            <span class="material-icons ml-2">
                                <span x-show="!open">keyboard_arrow_down</span>
                                <span x-show="open">keyboard_arrow_up</span>
                            </span>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
                            <div class="p-2">
                            @foreach($tingkatanKelas as $tingkatan)
                                <div class="flex items-center mb-2">
                                    <input 
                                        type="checkbox" 
                                        value="{{ $tingkatan->id }}"
                                        name="tingkatan_kelas_id[]" 
                                        @change="toggleTingkatan({{ $tingkatan->id }})"
                                        id="tingkatan_kelas_{{ $tingkatan->id }}"
                                        :checked="selected.includes({{ $tingkatan->id }})"
                                    >
                                    <label for="tingkatan_kelas_{{ $tingkatan->id }}" class="ml-2">{{ $tingkatan->nama_tingkatan }}</label>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('tingkatan_kelas')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="mulai" :value="__('Mulai')"/>
                    <x-text-input id="mulai" name="mulai" type="time" class="mt-1 block w-full" required autofocus :value="old('mulai', date('H:i', strtotime($slotWaktu->mulai)))" />
                    <x-input-error :messages="$errors->get('mulai')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="sesi_belajar_id" :value="__('Sesi Belajar')" />
                    <select name="sesi_belajar_id" id="sesi_belajar_id" class="w-full border-gray-300 rounded-md">
                        @foreach($sesiBelajar as $id => $nama)
                            <option value="{{ $id }}" {{ $selected_sesi_belajar == $id ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sesi_belajar_id')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <div class="form-check">
                        <input type="checkbox" name="is_istirahat" class="w-5 h-5 form-check-input rounded text-primaryColor"
                            value="1" id="isIstirahat"
                            {{ old('is_istirahat', $slotWaktu->is_istirahat) ? 'checked' : '' }}>
                        <label class="form-check-label text-dangerColor text-sm ml-1" for="isIstirahat">
                            *Centang apabila waktu istirahat
                        </label>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="hari" :value="__('Hari')" />
                    <div x-data="hariSelector" class="relative w-full">
                        <button type="button" @click="open = !open" class="border border-customColor rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
                            <span x-show="selected.length === 0">Pilih Tingkatan Hari</span>
                            <span x-show="selected.length > 0" x-text="getHariNames()"></span>
                            <span class="material-icons ml-2">
                                <span x-show="!open">keyboard_arrow_down</span>
                                <span x-show="open">keyboard_arrow_up</span>
                            </span>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
                            <div class="p-2">
                            @foreach($haris as $hari)
                                <div class="flex items-center mb-2">
                                    <input 
                                        type="checkbox" 
                                        value="{{ $hari->id }}"
                                        name="hari_id[]" 
                                        @change="toggleHari({{ $hari->id }})"
                                        id="hari_{{ $hari->id }}"
                                        :checked="selected.includes({{ $hari->id }})"
                                    >
                                    <label for="hari_{{ $hari->id }}" class="ml-2">{{ $hari->nama_hari }}</label>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('hari')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="selesai" :value="__('Selesai')"/>
                    <x-text-input id="selesai" name="selesai" type="time" class="mt-1 block w-full" required autofocus :value="old('selesai', date('H:i', strtotime($slotWaktu->selesai)))" />
                    <x-input-error :messages="$errors->get('selesai')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.slotwaktu.index') }}">
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
    document.addEventListener('alpine:init', () => {
    Alpine.data('tingkatanSelector', () => ({
        open: false,
        selected: @json($selectedTingkatanIds),
        tingkatanMapping: @json($tingkatanKelasMapping),
        
        getTingkatanNames() {
            return this.selected.map(id => this.tingkatanMapping[id]).join(', ');
        },
        
        toggleTingkatan(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(item => item !== id);
            } else {
                this.selected.push(id);
            }
        }
    }));
});
document.addEventListener('alpine:init', () => {
    Alpine.data('hariSelector', () => ({
        open: false,
        selected: @json($selectedHariIds),
        hariMapping: @json($hariMapping),
        
        getHariNames() {
            return this.selected.map(id => this.hariMapping[id]).join(', ');
        },
        
        toggleHari(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(item => item !== id);
            } else {
                this.selected.push(id);
            }
        }
    }));
});
</script>


@if (session('status') === 'slot-waktu-updated')
    <x-success-alert message="Berhasil mengubah slot waktu" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection