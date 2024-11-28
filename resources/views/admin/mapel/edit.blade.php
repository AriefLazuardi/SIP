@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-baseColor shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Mata Pelajaran') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.mapel.update', $mapel->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="w-full">
            <x-input-label for="nama" :value="__('Nama Mata Pelajaran')"/>
            <x-text-input id="nama" name="nama" type="text" value="{{ old('nama', $mapel->nama) }}" placeholder="Ketikkan disini" class="mt-1 block w-full" required autofocus autocomplete="nama" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <div class="flex space-x-6 justify-start">
            <div class="w-full md:w-1/2">
                <x-input-label for="warna" :value="__('Warna Identitas')" />
                <x-dropdown-color name="warna_id" :options="$warnas" :selected="$mapel->warna_id" placeholder="Pilih Warna Identitas" />
                <x-input-error :messages="$errors->get('warna_id')" class="mt-2" />
            </div>
            <div class="w-full md:w-1/2">
                <x-input-label for="tahun_ajaran_id" :value="__('Tahun Ajaran')" />
                <x-dropdown-custom name="tahun_ajaran_id" :options="$tahunAjaran" :selected="old('tahun_ajaran_id', $mapel->detailMataPelajaran->first()?->tahun_ajaran_id)" placeholder="Pilih Tahun Ajaran" class="w-full" />
                <x-input-error :messages="$errors->get('tahun_ajaran_id')" class="mt-2" />
            </div>
        </div>

        <div x-data="{
            groups: {{ Illuminate\Support\Js::from($existingSubjects) }},
            addGroup() {
                this.groups.push({
                    id: Date.now(),
                    total_jam_perminggu: '',
                    tingkatanKelas: [],
                    tingkatan_kelas_ids: []
                });
            },
            removeGroup(index) {
                if (this.groups.length > 1) {
                    this.groups.splice(index, 1);
                }
            }
        }">
            <template x-for="(group, index) in groups" :key="group.id">
                <div class="mb-6">
                    <div class="flex space-x-6 justify-start">
                        <div class="w-full md:w-1/2">
                            <x-input-label x-bind:for="'tingkatan_kelas_' + index" :value="__('Tingkatan Kelas')" />
                            <div x-data="{ open: false }" class="relative w-full">
                                <button 
                                    @click="open = !open" 
                                    type="button" 
                                    class="border border-customColor rounded-md px-4 py-2 w-full text-left flex items-center justify-between"
                                >
                                    <span x-show="group.tingkatanKelas.length === 0">Pilih Tingkatan Kelas</span>
                                    <span x-show="group.tingkatanKelas.length > 0" x-text="group.tingkatanKelas.join(', ')"></span>
                                    <span class="material-icons ml-2">arrow_drop_down</span>
                                </button>
                                <div 
                                    x-show="open" 
                                    @click.outside="open = false" 
                                    class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg"
                                >
                                    <div class="p-2">
                                        @foreach($tingkatanKelas as $id => $nama_tingkatan)
                                            <div class="flex items-center mb-2">
                                                <input 
                                                    type="checkbox" 
                                                    x-bind:name="'tingkatan_kelas_ids[' + index + '][]'"
                                                    value="{{ $id }}"
                                                    x-bind:id="'tingkatan_kelas_{{ $id }}_' + index"
                                                    :checked="group.tingkatan_kelas_ids.includes({{ $id }})"
                                                    @change="
                                                        if($event.target.checked) {
                                                            // Cek apakah ID sudah ada di group lain
                                                            let existsInOtherGroup = false;
                                                            groups.forEach((otherGroup, otherIndex) => {
                                                                if(otherIndex !== index && otherGroup.tingkatan_kelas_ids.includes({{ $id }})) {
                                                                    existsInOtherGroup = true;
                                                                    otherGroup.tingkatan_kelas_ids = otherGroup.tingkatan_kelas_ids.filter(id => id !== {{ $id }});
                                                                    otherGroup.tingkatanKelas = otherGroup.tingkatanKelas.filter(nama => nama !== '{{ $nama_tingkatan }}');
                                                                }
                                                            });
                                                            
                                                            if (!group.tingkatan_kelas_ids.includes({{ $id }})) {
                                                                group.tingkatanKelas.push('{{ $nama_tingkatan }}');
                                                                group.tingkatan_kelas_ids.push({{ $id }});
                                                            }
                                                        } else { 
                                                            const idxToRemove = group.tingkatan_kelas_ids.indexOf({{ $id }});
                                                            if (idxToRemove > -1) {
                                                                group.tingkatanKelas.splice(idxToRemove, 1);
                                                                group.tingkatan_kelas_ids.splice(idxToRemove, 1);
                                                            }
                                                        }"
                                                >
                                                <label x-bind:for="'tingkatan_kelas_{{ $id }}_' + index" class="ml-2">{{ $nama_tingkatan }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full md:w-1/2">
                            <x-input-label x-bind:for="'total_jam_perminggu_' + index" :value="__('Jam per Minggu')" />
                            <x-text-input 
                                x-bind:id="'total_jam_perminggu_' + index"
                                x-bind:name="'total_jam_perminggu[' + index + ']'"
                                type="number"
                                x-model="group.total_jam_perminggu"
                                placeholder="Ketikkan disini"
                                class="mt-1 block w-full"
                            />
                        </div>
                    </div>

                    <div class="mt-4" x-show="groups.length > 1">
                        <button @click="removeGroup(index)" type="button" class="bg-dangerColor text-white py-2 px-4 rounded items-center flex space-x-2">
                            <span class="material-icons text-sm">delete</span>
                            <span>Hapus Tingkatan</span>
                        </button>
                    </div>
                </div>
            </template>

            <div class="mt-4 md:w-1/2">
                <x-add-button type="button" @click="addGroup">
                    {{ __('Tambah Tingkatan') }}
                </x-add-button>
            </div>
        </div>

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

@if (session('status') === 'mapel-updated')
    <x-success-alert message="Berhasil mengubah mata pelajaran" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection