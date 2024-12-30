@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Wali Kelas') }}
        </h2>
    </header>

    <form method="post" action="{{ route('wakilkurikulum.tugasmengajar.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')
        <h1 class="font-semibold text-lg text-customColor">Guru</h1>
        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="guru" :value="__('Nama Guru')"/>
                    <x-dropdown-custom name="guru_id" :options="$gurus" selected="{{ old('guru_id') }}" placeholder="Pilih Guru" class="w-full border-customColor rounded-md" />
                    <x-input-error :messages="$errors->get('guru_id')" class="mt-2" />
                </div>
            </div>

            <div class="w-full md:w-1/2">
            </div>
        </div>
        
        <div x-data="{
            subjects: [{ id: 1, mata_pelajaran_id: '', kelas: [], kelas_ids: [] }],
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
            }
        }">
            <h1 class="font-semibold text-lg text-customColor">Tugas Mengajar</h1>
            
            <!-- Form dengan perulangan -->
            <template x-for="(subject, index) in subjects" :key="subject.id">
                <div class="mb-6">
                    <div class="flex space-x-6 justify-start">
                        <div class="w-full md:w-1/2">
                            <div>
                                <label :for="'mata_pelajaran_' + subject.id" class="block font-medium text-sm text-gray-700" x-text="'Mata Pelajaran ' + subject.id"></label>
                                <x-dropdown-custom 
                                    :name="'mata_pelajaran_id[]'" 
                                    :options="$mataPelajarans" 
                                    selected=""
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
                                <label :for="'kelas_' + subject.id" class="block font-medium text-sm text-gray-700">Kelas</label>
                                <div x-data="{ open: false, openTingkatan: null }" class="relative w-full">
                                    <!-- Tombol Dropdown -->
                                    <button @click="open = !open" type="button" class="border border-gray-300 rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
                                        <span x-show="subject.kelas.length === 0">Pilih Kelas</span>
                                        <span x-show="subject.kelas.length > 0" x-text="subject.kelas.join(', ')"></span>
                                        <span class="material-icons ml-2">
                                            <span x-show="!open">keyboard_arrow_down</span>
                                            <span x-show="open">keyboard_arrow_up</span>
                                        </span>
                                    </button>

                                    <!-- Dropdown Pilihan Tingkatan dan Kelas -->
                                    <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg flex">
                                        <!-- Daftar Tingkatan -->
                                        <div class="w-9/12  p-2 bg-baseColor border-r">
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
                                        <div class="w-1  border-r bg-whiteColor">
                                                
                                        </div>
                                        <!-- Daftar Kelas sesuai Tingkatan -->
                                        <div class="w-3/12 p-2 items-center">
                                            @foreach($kelas as $tingkatan => $kelasTingkatan)
                                                <div x-show="openTingkatan === '{{ $tingkatan }}'" x-transition>
                                                    @foreach($kelasTingkatan as $kelas)
                                                    <div class="flex items-center mb-2">
    <input 
        type="checkbox" 
        value="{{ $kelas['id'] }}"
        :name="'kelas_id[' + index + '][]'"
        @click="
            if (!subject.kelas.includes('{{ $tingkatan }} {{ $kelas['nama'] }}')) { 
                subject.kelas.push('{{ $tingkatan }} {{ $kelas['nama'] }}');
                if (!subject.kelas_ids[index]) {
                    subject.kelas_ids[index] = [];
                }
                subject.kelas_ids[index].push({{ $kelas['id'] }});
            } else { 
                const idxToRemove = subject.kelas.indexOf('{{ $tingkatan }} {{ $kelas['nama'] }}');
                if (idxToRemove > -1) {
                    subject.kelas.splice(idxToRemove, 1);
                    if (subject.kelas_ids[index]) {
                        const idIdx = subject.kelas_ids[index].indexOf({{ $kelas['id'] }});
                        if (idIdx > -1) {
                            subject.kelas_ids[index].splice(idIdx, 1);
                        }
                    }
                }
            }
        "
        :id="'kelas_' + {{ $kelas['id'] }} + '_' + subject.id + '_{{ $tingkatan }}'"
        :checked="subject.kelas.includes('{{ $tingkatan }} {{ $kelas['nama'] }}')"
    >
    <label :for="'kelas_' + {{ $kelas['id'] }} + '_' + subject.id + '_{{ $tingkatan }}'" class="ml-2">
        {{ $kelas['nama'] }}
    </label>
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

                        
                    <!-- Tombol Hapus -->
                    
                </div>
                <div class="mt-4" x-show="subjects.length > 1">
                        <button @click="removeSubject(index)" type="button" class="bg-dangerColor text-white py-2 px-4 rounded items-center flex space-x-2">
                            <span class="material-icons text-sm">
                            delete
                            </span>
                            <span>
                            Hapus Mata Pelajaran
                            </span>
                        </button>
                    </div>
            </template>

            <!-- Tombol Tambah Mata Pelajaran -->
            <div class="mt-4">
                <x-add-button @click="addSubject" type="button" class="bg-primaryColor text-white py-2 rounded w-full md:w-1/2">
                    {{ __('Tambah Mata Pelajaran') }}
                </x-add-button>
            </div>
        </div>


        <!-- Submit button -->
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


@if (session('status') === 'tugasmengajar-created')
    <x-success-alert :message="session('message')" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert :message="session('message')" icon="error" />
@endif
@endsection
