@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-white mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Guru</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.guru.index') }}" method="GET"">
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
    <a href="{{ route('admin.guru.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead>
            <tr class="text-center">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4 w-4/12 text-left">Nama</th>
                <th class="py-2 px-4 w-5/12">Nomor Identitas Pegawai</th>
                <th class="py-2 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gurus as $guru)
            <tr class="border-t">
                <td class="py-2 px-4 text-center">{{ $loop->iteration + ($gurus->currentPage() - 1) * $gurus->perPage() }}</td>
                <td class="py-2 px-4 text-left">{{ $guru->name }}</td>
                <td class="py-2 px-4 text-center">
                    {{ $guru->nip }}
                </td>
                <td class="py-2 px-4">
                    <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('admin.guru.edit', ['id' => $guru->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$guru->id" :action="route('admin.guru.destroy', $guru->id)" message="Apakah Anda yakin ingin menghapus guru ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-6">
        {{ $gurus->links() }}
    </div>
</div>

@if (session('status') === 'guru-created')
    <x-success-alert message="Berhasil menambahkan data guru" icon="success" />
@elseif (session('status') === 'guru-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'guru-deleted')
    <x-success-alert message="Berhasil menghapus data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection