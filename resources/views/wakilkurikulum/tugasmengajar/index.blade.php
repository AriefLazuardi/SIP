@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-baseColor mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Tugas Mengajar</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('wakilkurikulum.tugasmengajar.index') }}" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Cari" 
                    class="flex-grow border-0 focus:ring-0" 
                    x-model="search"
                >
            </form>
        </div>
    </div>

    <!-- Tombol Tambah Data -->
    <a href="{{ route('wakilkurikulum.tugasmengajar.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-baseColor">
        <thead>
            <tr class="text-left">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4">Nama Guru</th>
                <th class="py-2 px-4 w-3/12">Mata Pelajaran</th>
                <th class="py-2 px-4 w-1/6">Kelas</th>
                <th class="py-2 px-4 text-center">Slot Mengajar</th>
                <th class="py-2 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tugasMengajars as $tugasMengajar)
            <tr class="border-t">
                <td class="py-2 px-4 text-center">{{ $loop->iteration + ($tugasMengajars->currentPage() - 1) * $tugasMengajars->perPage() }}</td>
                <td class="py-2 px-4 text-left">{{ $tugasMengajar->guru->name }}</td>
                <td class="py-2 px-4 text-left">{{ $tugasMengajar->mataPelajaran->nama }}</td>
                <td class="py-2 px-4 text-left">
                    @foreach($tugasMengajar->kelas as $kelas)
                        {{ $kelas->tingkatanKelas->nama_tingkatan }}{{ $kelas->nama_kelas }}
                        @if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="py-2 px-4 text-center">{{ $tugasMengajar->guru->total_jam_perminggu }}</td>
                <td class="py-2 px-4">
                    <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('wakilkurikulum.tugasmengajar.edit',['id'=>$tugasMengajar->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$tugasMengajar->id" :action="route('wakilkurikulum.tugasmengajar.destroy', $tugasMengajar->id)" message="Apakah Anda yakin ingin menghapus data tugas mengajar ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-6">
        {{ $tugasMengajars->links() }}
    </div>
</div>

@if (session('status') === 'tugasmengajar-created')
    <x-success-alert message="Berhasil Menambahkan Data Tugas Mengajar" icon="success" />
@elseif (session('status') === 'tugasmengajar-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'tugasmengajar-deleted')
    <x-success-alert message="Berhasil menghapus data ini" icon="success" />
@elseif (session('status') === 'error')
<x-error-alert message="{{ session('message') }}" icon="error" />
    @if ($errors->any())
        <ul class="mt-2 text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@endif
@endsection