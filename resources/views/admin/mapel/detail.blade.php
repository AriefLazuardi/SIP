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
            <div class="space-y-2">
                @foreach($detailMapelGrouped as $detail)
                    <div class="flex">
                        <div class="md:w-1/2">
                            <p class="text-sm mb-2">Tingkatan Kelas</p>
                            <span class="font-medium">{{ $detail['tingkatanKelas'] }}</span>
                        </div>
                        <div class="md:w-1/2">
                            <p class="text-sm mb-2">Jam per Minggu</p>
                            <span class="font-medium">{{ $detail['jamPerMinggu'] }}</span>
                        </div>
                        <div class="md:w-1/2">
                            <p class="text-sm mb-2">Kurikulum</p>
                            <span class="font-medium">{{ $detail['kurikulum'] }}</span>
                        </div>
                        <div class="md:w-1/2">
                            <p class="text-sm mb-2">Tahun Ajaran</p>
                            <span class="font-medium">{{ $detail['tahunAjaran'] }}</span>
                        </div>
                    </div>
                    <div class="border-b-2"></div>
                @endforeach
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
