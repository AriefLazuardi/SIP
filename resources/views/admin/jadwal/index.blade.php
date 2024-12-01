@extends('layouts.utama')

@section('content')
<div class="container mx-auto p-4 bg-white mt-8">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Jadwal Mata Pelajaran Sekarang</h2>
        <div class="flex items-center gap-2">
            <div x-data="{ search: '{{ request('search') }}' }" class="flex items-center border border-customColor rounded-xl p-1">
                <span class="material-icons text-primaryColor ml-2">search</span>
                <form action="{{ route('admin.jadwal.index') }}" method="GET">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Cari" 
                        class="flex-grow border-0 focus:ring-0" 
                        x-model="search"
                    >
                </form>
            </div>
            <div class="w-12 h-12 items-center flex justify-center rounded-md border-primaryColor border-2">
                <a href="{{ route('admin.cetak.jadwal') }}" class="w-12 h-12 items-center flex justify-center rounded-md">
                    <i class="material-icons items-center text-primaryColor">print</i>
                </a>
            </div>
        </div>
    </div>

  
    <form action="{{ route('admin.jadwal.index') }}" method="GET" class="mb-4">
    <input type="hidden" name="hari_id" value="{{ $selectedHari }}">
    
    <x-dropdown-custom
        name="tahun_ajaran_id"
        :options="$tahunAjaran"
        :selected="$selectedTahunAjaran"
        placeholder="Pilih Tahun Ajaran"
        onchange="this.form.submit()"
        class="w-80 mb-4 -mt-4"/>

    <!-- Pilih Tingkatan Kelas -->
    <div class="flex space-x-2 mb-2">
        @foreach (range(1, 6) as $tingkat)
            <button type="submit" 
                name="tingkatan_kelas_id" 
                value="{{ $tingkat }}"
                class="px-4 py-2 text-sm font-semibold border-2 w-20 rounded-md border-primaryColor {{ $selectedTingkatanKelas == $tingkat ? 'bg-primaryColor text-whiteColor' : 'bg-white text-primaryColor' }} hover:bg-primaryColor hover:text-whiteColor">
                {{ $tingkat }}
            </button>
        @endforeach
    </div>
    
    <!-- Form terpisah untuk pilihan hari -->
    </form>
    <form action="{{ route('admin.jadwal.index') }}" method="GET" class="mb-4">
        <input type="hidden" name="tingkatan_kelas_id" value="{{ $selectedTingkatanKelas }}">
        
        <div class="flex justify-between items-center mb-4">
            <!-- Pilih Hari -->
            <div class="flex space-x-2">
                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $index => $hari)
                    <button type="submit" 
                        name="hari_id" 
                        value="{{ $index + 1 }}"
                        class="px-4 py-2 text-sm font-semibold border-2 border-primaryColor w-20 rounded-md {{ $selectedHari == $index + 1 ? 'bg-primaryColor text-whiteColor' : 'bg-white text-primaryColor' }} hover:bg-primaryColor hover:text-whiteColor">
                        {{ $hari }}
                    </button>
                @endforeach
            </div>
            
            <!-- Sesi di ujung kanan -->
            <div class="bg-primaryColor w-28 h-12 border-2 rounded-md flex justify-center items-center">
                <span class="font-semibold text-whiteColor text-center">
                    Sesi {{ array_values($jadwalMatrix)[0]['sesi_belajar'] }}
                </span>
            </div>
        </div>
    </form>
  
    <div class="container mx-auto p-4">
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b-2 border-gray-200 border-r-4 border-r-primaryColor px-4 py-2 w-40 text-left">Waktu/Kelas</th>
                        @foreach($kelas as $kelasNama)
                            <th class="border-b-2 border-gray-200 px-4 py-2 text-center">{{$selectedTingkatanKelas}}{{ $kelasNama }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalMatrix as $waktu => $data)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-2 text-sm border-r-4 border-r-primaryColor">
                                {{ $waktu }}
                            </td>
                            @if($data['is_khusus'])
                                <td colspan="{{ count($kelas) }}" class="px-4 py-3 border-y-primaryColor border-y-2">
                                    <div class="text-center text-green-600 h-10 justify-center flex items-center font-bold text-2xl">
                                        {{ $data['nama_kegiatan'] ?? 'SLOT KHUSUS' }}
                                    </div>
                                </td>
                            @elseif($data['is_istirahat'])
                                <td colspan="{{ count($kelas) }}" class="px-4 py-3 border-y-primaryColor border-y-2">
                                    <div class="text-center text-green-600 h-10 justify-center flex items-center font-bold text-2xl">
                                        ISTIRAHAT
                                    </div>
                                </td>
                            @else
                                @foreach($kelas as $kelasNama)
                                    <td class="px-2 py-2">
                                        @if($data['kelas'][$kelasNama]['mata_pelajaran'] != '-')
                                            <div class="flex items-center">
                                                <div class="w-3 h-12 mr-2" style="background-color: {{ $data['kelas'][$kelasNama]['warna'] }}"></div>
                                                <div>
                                                    <div class="font-semibold text-sm">
                                                        {{ $data['kelas'][$kelasNama]['mata_pelajaran'] }}
                                                    </div>
                                                    <div class="text-xs text-gray-600">
                                                        {{ $data['kelas'][$kelasNama]['guru'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('components.confirm-alert')

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