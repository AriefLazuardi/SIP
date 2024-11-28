@extends('layouts.utama')

@section('content')
<div class="mx-auto flex -mt-5">
    <a class="items-center space-x-4 justify-left flex" href="{{ route('admin.mapel.index') }}">
        <span class="material-icons">arrow_back</span>
        <span class="text-xl">Kembali</span>
    </a>
</div>
<div class="container mx-auto p-4 bg-baseColor mt-5 text-customColor">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Detail Mata Pelajaran</h2>
    </div>
    <div class="space-y-5">
        <div class="space-y-2">
            <p class="text-sm ">Nama Mata Pelajaran</p>
            <p class="font-medium">{{ $mapel->nama }}</p>
            <div class="border-b-2"></div>
        </div>

        @if($mapel->detailMataPelajaran->count() > 0)
            <!-- Tingkatan Kelas berdasarkan Jam per Minggu -->
            <div class="space-y-2">
                @foreach($tingkatanKelasByJam as $jam => $tingkatanKelas)
                <div class="flex">
                    <div class="md:w-1/2">
                        <p class="text-sm mb-2">Tingkatan Kelas</p>
                        <span >{{ implode(', ', $tingkatanKelas) }}</span>
                    </div>
                    <div class="md:w-1/2">
                        <p class="text-sm mb-2">Jam per Minggu</p>
                        <span class="">{{ $jam }}</span>
                    </div>
                </div>
                    <div class="border-b-2"></div>
                @endforeach
            </div>

            <!-- Tahun Ajaran -->
            <div class="space-y-2">
                <p class="text-sm text-gray-600">Tahun Ajaran</p>
                <p class="font-medium">{{ $formatTahunAjaran }}</p>
                <div class="border-b-2"></div>
            </div>

            <!-- Warna Identitas -->
            <div class="space-y-2">
                <p class="text-sm text-gray-600">Warna Identitas</p>
                <div class="border border-customColor p-2 w-72">
                    <div class="w-70 h-8 rounded" style="background-color: {{ $mapel->warna->kode_hex }}"></div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                Belum ada detail mata pelajaran yang ditambahkan.
            </div>
        @endif
    </div>
</div>
@include('components.confirm-alert')

@endsection
