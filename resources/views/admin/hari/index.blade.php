@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-baseColor mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Hari</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.hari.index') }}" method="GET">
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
    <a href="{{ route('admin.hari.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-baseColor">
        <thead>
            <tr class="text-center">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-80 ">Nama Hari</th>
                <th class="py-2 px-10">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($haris as $hari)
            <tr class="border-t">
                <td class="py-2 px-4 text-center">{{ $loop->iteration }}</td>
                <td class="py-2 px-56 text-center">
                    {{ $hari->nama_hari }}
               </td>
                <td class="py-2 px-10">
                    <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('admin.hari.edit',['id'=>$hari->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$hari->id" :action="route('admin.hari.destroy', $hari->id)" message="Apakah Anda yakin ingin menghapus hari ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if (session('status') === 'hari-created')
    <x-success-alert message="Berhasil menambahkan data Hari" icon="success" />
    {{ session()->forget('status') }}
@elseif (session('status') === 'hari-updated')
    <x-success-alert  message="Berhasil mengubah data ini" icon="success" />
    {{ session()->forget('status') }}
@elseif (session('status') === 'hari-deleted')
    <x-success-alert  message="Berhasil menghapus data ini" icon="success" />
    {{ session()->forget('status') }}
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
    {{ session()->forget('status') }}
@endif
@endsection