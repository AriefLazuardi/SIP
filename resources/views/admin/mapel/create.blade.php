@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Mata Pelajaran') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.mapel.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <div class="w-full">
            <x-input-label for="nama" :value="__('Nama Mata Pelajaran')"/>
            <x-text-input id="nama" name="nama" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" required autofocus autocomplete="nama" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>
        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <div class="">
                    <x-input-label for="warna" :value="__('Warna Identitas')" />
                    <x-dropdown-color name="warna_id" :options="$warnas" placeholder="Pilih Warna Identitas" />
                    <x-input-error :messages="$errors->get('warna_id')" class="mt-2" />
                </div>
            </div>
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="tahun_ajaran_id" :value="__('Tahun Ajaran')" />
                    <x-dropdown-custom name="tahun_ajaran_id" :options="$tahunAjaran" selected="{{ old('tahun_ajaran_id') }}" placeholder="Pilih Tahun Ajaran" class="w-full" />
                    <x-input-error :messages="$errors->get('tahun_ajaran_id')" class="mt-2" />
                </div>
            </div>
        </div>

        <div x-data="{
                    subjects: [{ 
                        id: 1, 
                        tingkatan_kelas_id: '', 
                        total_jam_perminggu: '',
                        kurikulum_id: '',
                        tingkatanKelas: [],
                        tingkatan_kelas_ids: []
                    }],
                    addSubject() {
                        this.subjects.push({
                            id: this.subjects.length + 1,
                            tingkatan_kelas_id: '',
                            total_jam_perminggu: '',
                            kurikulum_id: '',
                            tingkatanKelas: [],
                            tingkatan_kelas_ids: []
                        });
                    },
                    removeSubject(index) {
                        this.subjects = this.subjects.filter((_, i) => i !== index);
                    }
                }">
            <!-- Form dengan perulangan -->
            <template x-for="(subject, index) in subjects" :key="subject.id">
                <div class="mb-6">
                    <div class="flex space-x-6 justify-start">
                        <div class="w-full md:w-1/2">
                        <div>
                                <label :for="'tingkatanKelas_' + subject.id" class="block font-medium text-sm text-gray-700 mb-2" x-text="'Tingkatan Kelas'"></label>
                                <div x-data="{ 
                                        open: false
                                    }" 
                                    class="relative w-full">
                                    
                                    <!-- Tombol Dropdown -->
                                    <button @click="open = !open" type="button" class="border border-customColor rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
                                        <span x-show="subject.tingkatanKelas.length === 0">Pilih Tingkatan Kelas</span>
                                        <span x-show="subject.tingkatanKelas.length > 0" x-text="subject.tingkatanKelas.join(', ')"></span>
                                        <span class="material-icons ml-2">
                                            <span x-show="!open">keyboard_arrow_down</span>
                                            <span x-show="open">keyboard_arrow_up</span>
                                        </span>
                                    </button>

                                    <!-- Dropdown Pilihan Kelas -->
                                    <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
                                        <div class="p-2">
                                            @foreach($tingkatanKelas as $id => $nama_tingkatan)
                                                <div class="flex items-center mb-2">
                                                    <input 
                                                        type="checkbox" 
                                                        value="{{ $id }}"
                                                        :name="'tingkatan_kelas_id[' + index + '][]'" 
                                                        @change="
                                                            if($event.target.checked) { 
                                                                if (!subject.tingkatanKelas.includes('{{ $nama_tingkatan }}')) {
                                                                    subject.tingkatanKelas.push('{{ $nama_tingkatan }}');
                                                                    subject.tingkatanKelas_ids.push({{ $id }});
                                                                }
                                                            } else { 
                                                                const idxToRemove = subject.tingkatanKelas.indexOf('{{ $nama_tingkatan }}');
                                                                if (idxToRemove > -1) {
                                                                    subject.tingkatanKelas.splice(idxToRemove, 1);
                                                                    subject.tingkatanKelas_ids.splice(idxToRemove, 1);
                                                                }
                                                            }" 
                                                        :id="'tingkatanKelas_' + {{ $id }} + '_' + subject.id"
                                                        :checked="subject.tingkatanKelas.includes('{{ $nama_tingkatan }}')"
                                                    >
                                                    <label :for="'tingkatanKelas_' + {{ $id }} + '_' + subject.id" class="ml-2">{{ $nama_tingkatan }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @error('tingkatanKelas_id.*')
                                    <p class="mt-2 text-sm text-dangerColor">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <label x-bind:for="'kurikulum_id_' + subject.id" class="block font-medium text-sm text-gray-700 mb-2">Kurikulum</label>
                                <x-dropdown-custom 
                                x-bind:id="'kurikulum_' + subject.id" 
                                x-bind:name="'kurikulum_id[' + index + ']'"
                                :options="$kurikulum" selected="{{ old('kurikulum_id') }}"
                                placeholder="Pilih Kurikulum" 
                                class="mt-1 block w-full" 
                                />
                                @error('kurikulum_id')
                                    <p class="mt-2 text-sm text-dangerColor">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                          
                        <div class="w-full md:w-1/2">
                        <div>
                            <label x-bind:for="'total_jam_perminggu_' + subject.id" class="block font-medium text-sm text-gray-700 mb-2">Jam per Minggu</label>
                            <x-text-input 
                                x-bind:id="'total_jam_perminggu_' + subject.id" 
                                x-bind:name="'total_jam_perminggu[' + index + ']'" 
                                type="text" 
                                placeholder="Ketikkan disini" 
                                class="mt-1 block w-full" 
                            />
                                @error('total_jam_perminggu.*')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Hapus -->
                    <div class="mt-4" x-show="subjects.length > 1">
                        <button @click="removeSubject(index)" type="button" class="bg-dangerColor text-white py-2 px-4 rounded items-center flex space-x-2">
                            <span class="material-icons text-sm">
                            delete
                            </span>
                            <span>
                            Hapus Tingkatan Kelas
                            </span>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Tombol Tambah Mata Pelajaran -->
            <div class="mt-4 mr-4">
                <x-add-button @click="addSubject" type="button" class="bg-primaryColor text-white py-2 rounded md:w-1/2">
                    {{ __('Tambah Tingkatan Kelas') }}
                </x-add-button>
            </div>
        </div>

        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
            <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.mapel.index') }}">
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

@if (session('status') === 'mapel-created')
    <x-success-alert message="Berhasil menambahkan mata pelajaran" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection
