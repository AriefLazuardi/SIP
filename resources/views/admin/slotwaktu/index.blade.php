@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-baseColor mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Data Slot Waktu</h2>

        <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
            <span class="material-icons text-primaryColor mr-2">search</span>
            <form action="{{ route('admin.slotwaktu.index') }}" method="GET">
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
    <a href="{{ route('admin.slotwaktu.create') }}" class="w-44 bg-primaryColor text-whiteColor p-3 rounded-md flex space-x-4 items-center mb-4 ml-auto">
        <span class="material-icons px-2">add</span>
        Tambah Data
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-baseColor">
            <thead>
                <tr class="text-center">
                    <th class="py-2 px-4">No</th>
                    <th class="py-2 px-4">Jam Mulai</th>
                    <th class="py-2 px-4">-</th>
                    <th class="py-2 px-4">Jam Berakhir</th>
                    <th class="py-2 px-4">Tingkatan Kelas</th>
                    <th class="py-2 px-4">Hari</th>
                    <th class="py-2 px-4">Sesi</th>
                    <th class="py-2 px-4">Istirahat</th>
                    <th class="py-2 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($slotWaktus as $slotWaktu)
                <tr class="border-t">
                    <td class="py-2 px-4 text-center">{{ $loop->iteration + ($slotWaktus->currentPage() - 1) * $slotWaktus->perPage() }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        {{ $slotWaktu['jam_mulai'] }}
                    </td>
                    <td class="py-2 px-4 border-b text-center">-</td>
                    <td class="py-2 px-4 border-b text-center">
                        {{ $slotWaktu['jam_selesai'] }}
                    </td>
                    <td class="py-2 px-4 text-center">
                        @if(isset($slotWaktu['tingkatan_kelas']) && is_array($slotWaktu['tingkatan_kelas']))
                            @foreach($slotWaktu['tingkatan_kelas'] as $tk)
                                {{ $tk['nama'] }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center">
                        {{ !empty($slotWaktu['tingkatan_kelas']) ? $slotWaktu['tingkatan_kelas'][0]['hari'] : '-' }}
                    </td>
                    <td class="py-2 px-4 text-center">
                        @if(!empty($slotWaktu['tingkatan_kelas']))
                            {{ $slotWaktu['tingkatan_kelas'][0]['sesi_belajar'] ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b text-center font-extrabold">
                        @if ($slotWaktu['is_istirahat'])
                            <span class="material-icons text-2xl text-primaryColor">check_box</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        <div class="flex justify-center space-x-2">
                        <x-edit-button :url="route('admin.slotwaktu.edit',['id'=>$slotWaktu['id']])">
                            Edit
                        </x-edit-button>
                        <x-delete-button :id="$slotWaktu['id']" :action="route('admin.slotwaktu.destroy', $slotWaktu['id'])" message="Apakah Anda yakin ingin menghapus slot waktu ini?">
                            Hapus
                        </x-delete-button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $slotWaktus->links() }}
    </div>
</div>


@if (session('status') === 'slot-waktu-created')
    <x-success-alert message="Berhasil menambahkan data slot waktu" icon="success" />
@elseif (session('status') === 'slot-waktu-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'slot-waktu-deleted')
    <x-success-alert message="Berhasil menghapus data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection