@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-baseColor mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Tabel Data Kurikulum</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.kurikulum.index') }}" method="GET">
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
    <a href="{{ route('admin.kurikulum.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-baseColor">
        <thead>
            <tr class="text-left">
                <th class="py-2 w-1/12 text-center">No.</th>
                <th class="py-2">Nama Kurikulum</th>
                <th class="py-2 text-center w-1/12 pr-10">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kurikulums as $kurikulum)
            <tr class="border-t text-left">
                <td class="py-2 text-center">{{ $loop->iteration + ($kurikulums->currentPage() - 1) * $kurikulums->perPage() }}</td>
                <td class="py-2">
                    {{ $kurikulum->nama_kurikulum }}
                </td>   
                <td class="py-2 text-center pr-10">
                    <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('admin.kurikulum.edit',['id'=>$kurikulum->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$kurikulum->id" :action="route('admin.kurikulum.destroy', $kurikulum->id)" message="Apakah Anda yakin ingin menghapus kurikulum ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-6">
        {{ $kurikulums->links() }}
    </div>
</div>

@if (session('status') === 'kurikulum-created')
    <x-success-alert message="Berhasil menambahkan data kurikulum" icon="success" />
@elseif (session('status') === 'kurikulum-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'kurikulum-deleted')
    <x-success-alert message="Berhasil menghapus data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection