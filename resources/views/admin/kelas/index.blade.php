@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-baseColor mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Kelas</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.kelas.index') }}" method="GET">
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
    <a href="{{ route('admin.kelas.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-baseColor">
        <thead>
            <tr class="text-center">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4 w-5/12">Tingkatan Kelas</th>
                <th class="py-2 px-4 w-4/12">Kelas</th>
                <th class="py-2 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelass as $kelas)
            <tr class="border-t">
                <td class="py-2 px-4 text-center">{{ $loop->iteration + ($kelass->currentPage() - 1) * $kelass->perPage() }}</td>
                <td class="py-2 px-4 text-center">
                    {{$kelas->tingkatanKelas->nama_tingkatan ?? '0'}}
               </td>
                <td class="py-2 px-4 border-b text-center">
                    {{ $kelas->nama_kelas }}
                </td>   
                <td class="py-2 px-4">
                    <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('admin.kelas.edit',['id'=>$kelas->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$kelas->id" :action="route('admin.kelas.destroy', $kelas->id)" message="Apakah Anda yakin ingin menghapus kelas ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-6">
        {{ $kelass->links() }}
    </div>
</div>

@if (session('status') === 'kelas-created')
    <x-success-alert message="Berhasil menambahkan data kelas" icon="success" />
@elseif (session('status') === 'kelas-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'kelas-deleted')
    <x-success-alert message="Berhasil menghapus data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection