@extends('layouts.utama')

@section('content')

<div class="container mx-auto p-4 bg-white mt-8">
    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-bold mb-4">Data Akun yang Terdaftar</h2>

        <!-- Form pencarian -->
        <div class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.user.index') }}" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Cari" 
                    class="flex-grow border-0 focus:ring-0" 
                    value="{{ request('search') }}"
                >
            </form>
        </div>
    </div>

    <a href="{{ route('admin.user.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead>
            <tr class="text-center">
                <th class="py-2 px-4">No</th>
                <th class="py-2 px-4 text-left">Username</th>
                <th class="py-2 px-4">Role</th>
                <th class="py-2 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-t text-center">
                <td class="py-2 px-4 text-center">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                <td class="py-2 px-4 text-left">{{ $user->username }}</td>
                <td class="py-2 px-4">
                    @foreach($user->userRole as $role)
                        {{ $role->display_name }}
                    @endforeach
                </td>
                <td class="py-2 px-4">
                    <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('admin.user.edit',['id'=>$user->id])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$user->id" :action="route('admin.user.destroy', $user->id)" message="Apakah Anda yakin ingin menghapus data akun ini?">
                            Hapus
                        </x-delete-button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@include('components.delete-alert')
@if (session('status') === 'user-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@elseif (session('status') === 'user-created')
    <x-success-alert message="Berhasil menambahkan akun" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif

@include('components.confirm-alert')
@endsection