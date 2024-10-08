@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-white mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Guru</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('wakilkurikulum.guru.index') }}" method="GET"">
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
    <a href="{{ route('wakilkurikulum.guru.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead>
            <tr class="text-center">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4">Nama</th>
                <th class="py-2 px-4">Nomor Identitas Pegawai</th>
                <th class="py-2 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gurus as $guru)
            <tr class="border-t">
                <td class="py-2 px-4 text-center">{{ $loop->iteration }}</td>
                <td class="py-2 px-4 text-left">{{ $guru->name }}</td>
                <td class="py-2 px-4 text-center">
                    {{ $guru->nip }}
                </td>
                <td class="py-2 px-4">
                    <div class="flex justify-center space-x-2">
                        <a href="{{route('wakilkurikulum.guru.edit',['id'=>$guru->id])}}" class="bg-yellowColor text-baseColor px-6 py-1 rounded-md">Edit</a>
                        <form id="delete-form-{{ $guru->id}}" action="{{ route('wakilkurikulum.guru.destroy', $guru->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-{{ $guru->id }}')" class="bg-dangerColor text-baseColor px-3 py-1 rounded-md">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@include('components.confirm-alert')

@if (session('status') === 'guru-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection