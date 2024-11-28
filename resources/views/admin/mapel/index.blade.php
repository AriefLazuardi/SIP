@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-baseColor mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Mata Pelajaran</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.mapel.index') }}" method="GET">
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
    <a href="{{ route('admin.mapel.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-baseColor">
        <thead>
            <tr class="text-center">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4 w-4/12">Nama</th>
                <th class="py-2 px-4 w-4/12">Warna</th>
                <th class="py-2 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mapels as $mapel)
            <tr class="border-t">
                <td class="py-2 px-4 text-center">{{ $loop->iteration + ($mapels->currentPage() - 1) * $mapels->perPage() }}</td>
                <td class="py-2 px-4 text-left">{{ $mapel->nama }}</td>
                <td class="py-2 px-4 border-b text-center">
                        @php
                            $color = $mapel->warna->kode_hex ?? '#000000';
                        @endphp
                        <span class="inline-block w-44 h-4 border rounded" style="background-color: {{ $color }};"></span>
                </td>
                <td class="py-2 px-4">
                    <div class="flex justify-center space-x-2">
                        <x-detail-button :url="route('admin.mapel.detail',['id'=>$mapel->id])">
                            Detail
                        </x-detail-button>
                        <x-edit-button :url="route('admin.mapel.edit',['id'=>$mapel->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$mapel->id" :action="route('admin.mapel.destroy', $mapel->id)" message="Apakah Anda yakin ingin menghapus mata pelajaran ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-6">
        {{ $mapels->links() }}
    </div>
</div>

@if (session('status') === 'mapel-created')
    <x-success-alert message="Berhasil Menambahkan Data Mata Pelajaran" icon="success" />
@elseif (session('status') === 'mapel-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'mapel-deleted')
    <x-success-alert message="Berhasil menghapus data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection