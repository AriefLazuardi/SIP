@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Tugas Mengajar') }}
        </h2>
    </header>

    <form method="post" action="{{ route('wakilkurikulum.tugasmengajar.update', $tugasMengajar->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')
        
        <h1 class="font-semibold text-lg text-customColor">Guru</h1>
        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="guru" :value="__('Nama Guru')"/>
                    <x-dropdown-custom name="guru_id" :options="$gurus" :selected="$tugasMengajar->guru_id" placeholder="Pilih Guru" class="w-full border-customColor rounded-md" />
                    <x-input-error :messages="$errors->get('guru_id')" class="mt-2" />
                </div>
            </div>

            <div class="w-full md:w-1/2">
            </div>
        </div>
        <div x-data="{
    subjects: {{ json_encode($existingSubjects) }},
    addSubject() {
        this.subjects.push({
            id: this.subjects.length + 1,
            mata_pelajaran_id: '',
            kelas: [],
            kelas_ids: []
        });
    },
    removeSubject(index) {
        this.subjects = this.subjects.filter((_, i) => i !== index);
    },
    updateKelas(subject, kelasName, kelasId, checked) {
        if (checked) {
            if (!subject.kelas.includes(kelasName)) {
                subject.kelas.push(kelasName);
                subject.kelas_ids.push(kelasId);
            }
        } else {
            subject.kelas = subject.kelas.filter(k => k !== kelasName);
            subject.kelas_ids = subject.kelas_ids.filter(id => id !== kelasId);
        }
    }
}">
    <h1 class="font-semibold text-lg text-customColor">Tugas Mengajar</h1>
    
    <template x-for="(subject, index) in subjects" :key="subject.id">
        <div class="mb-6">
            <div class="flex space-x-6 justify-start">
                <div class="w-full md:w-1/2">
                    <div>
                        <label :for="`mata_pelajaran_${subject.id}`" class="block font-medium text-sm text-gray-700" x-text="'Mata Pelajaran ' + (index + 1)"></label>
                        <x-dropdown-custom 
                            :name="'mata_pelajaran_id[]'" 
                            :options="$mataPelajarans" 
                            x-bind:value="subject.mata_pelajaran_id"
                            placeholder="Pilih Mata Pelajaran" 
                            class="w-full border-customColor rounded-md" 
                        />
                        @error('mata_pelajaran_id.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <div>
                        <label :for="`kelas_${subject.id}`" class="block font-medium text-sm text-gray-700">Kelas</label>
                        <div x-data="{ open: false, openTingkatan: null }" class="relative w-full">
                            <!-- Tombol untuk membuka dropdown kelas -->
                            <button @click="open = !open" type="button" class="border border-gray-300 rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
                                <span x-show="subject.kelas.length === 0">Pilih Kelas</span>
                                <span x-show="subject.kelas.length > 0" x-text="subject.kelas.join(', ')"></span>
                                <span class="material-icons ml-2">
                                    <span x-show="!open">keyboard_arrow_down</span>
                                    <span x-show="open">keyboard_arrow_up</span>
                                </span>
                            </button>

                            <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg flex">
                                <div class="w-9/12 p-2 bg-baseColor border-r">
                                    @foreach($kelas as $tingkatan => $kelasTingkatan)
                                        <div 
                                            @mouseenter="openTingkatan = '{{ $tingkatan }}'" 
                                            class="py-2 px-4 cursor-pointer hover:bg-green-600 hover:text-white"
                                            :class="{ 'bg-green-600 text-white': openTingkatan === '{{ $tingkatan }}' }">
                                            <span>Kelas</span>
                                            {{ $tingkatan }}
                                            <span class="float-right material-icons">chevron_right</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="w-1 border-r bg-whiteColor"></div>
                                <div class="w-3/12 p-2 items-center">
                                    @foreach($kelas as $tingkatan => $kelasTingkatan)
                                        <div x-show="openTingkatan === '{{ $tingkatan }}'" x-transition>
                                            @foreach($kelasTingkatan as $kelas)
                                            <div class="flex items-center mb-2">
                                                <input 
                                                    type="checkbox" 
                                                    :value="{{ $kelas['id'] }}" 
                                                    :name="'kelas_id[' + index + '][]'"
                                                    x-model="subject.kelas_ids"
                                                    @change="updateKelas(subject, '{{ $tingkatan }} {{ $kelas['nama'] }}', {{ $kelas['id'] }}, $event.target.checked)"
                                                >
                                                <label class="ml-2">{{ $kelas['nama'] }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('kelas_id.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="mt-4" x-show="subjects.length > 1">
            <button @click="removeSubject(index)" type="button" class="bg-dangerColor text-white py-2 px-4 rounded items-center flex space-x-2">
                <span class="material-icons text-sm">delete</span>
                <span>Hapus Mata Pelajaran</span>
            </button>
        </div>
        </div>
    </template>

    <div class="mt-4">
        <x-add-button @click="addSubject" type="button" class="bg-primaryColor text-white py-2 rounded w-full md:w-1/2">
            {{ __('Tambah Mata Pelajaran') }}
        </x-add-button>
    </div>
</div>




        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('wakilkurikulum.tugasmengajar.index') }}">
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
<!-- @php
    dump(session('status'));
    dump(session('errors'));
@endphp -->
@if (session('status') === 'tugasmengajar-updated')
    <x-success-alert message="Berhasil mengupdate tugas mengajar" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert :message="session('message')" icon="error" />
@endif
@endsection