@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-baseColor mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Tahun Ajaran</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.tahunajaran.index') }}" method="GET">
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
    <a href="{{ route('admin.tahunajaran.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-baseColor">
        <thead>
            <tr class="text-center">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4">Mulai</th>
                <th class="py-2 px-4">Sampai</th>
                <th class="py-2 px-4">Berakhir</th>
                <th class="py-2 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tahunAjarans as $tahunAjaran)
            <tr class="border-t">
                <td class="py-2 px-4 text-center">{{ $loop->iteration }}</td>
                <td class="py-2 px-4 text-center">
                    {{ \Carbon\Carbon::parse($tahunAjaran->mulai)->year }}
               </td>
                <td class="py-2 px-4 border-b text-center">
                   -
                </td>
                <td class="py-2 px-4 border-b text-center">
                    {{ \Carbon\Carbon::parse($tahunAjaran->selesai)->year }}
                </td>
                <td class="py-2 px-4">
                    <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('admin.tahunajaran.edit',['id'=>$tahunAjaran->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$tahunAjaran->id" :action="route('admin.tahunajaran.destroy', $tahunAjaran->id)" message="Apakah Anda yakin ingin menghapus tahun ajaran ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if (session('status') === 'tahunAjaran-created')
    <x-success-alert message="Berhasil menambahkan data Tahun Ajaran" icon="success" />
@elseif (session('status') === 'tahunAjaran-updated')
    <x-success-alert  message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'tahunAjaran-deleted')
    <x-success-alert  message="Berhasil menghapus data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection