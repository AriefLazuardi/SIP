@extends('layouts.utama')

@section('content')
<div class="mx-auto flex -mt-5">
    <a class="items-center space-x-4 justify-left flex" href="{{ route('admin.guru.index') }}">
        <span class="material-icons">arrow_back</span>
        <span class="text-xl">Kembali</span>
    </a>
</div>
<div class="container mx-auto p-4 bg-baseColor mt-5 text-customColor">
    <!-- Form pencarian -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Detail Guru</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col border-b py-2">
                <span class="font-semibold">Nama</span>
                <span>{{$guru->name}}</span>
            </div>
            <div class="flex flex-col border-b py-2">
                <span class="font-semibold">NIP</span>
                <span>{{$guru->nip}}</span>
            </div>
            <div class="flex flex-col border-b py-2">
                <span class="font-semibold">Tempat Lahir</span>
                <span>{{$guru->tempat_lahir}}</span>
            </div>
            <div class="flex flex-col border-b py-2">
                <span class="font-semibold">Tanggal Lahir</span>
                <span>{{$guru->tanggal_lahir}}</span>
            </div>
            <div class="flex flex-col border-b py-2">
                <span class="font-semibold">Golongan</span>
                <span>{{$guru->golongan}}</span>
            </div>
            <div class="flex flex-col border-b py-2">
                <span class="font-semibold">Jabatan</span>
                <span>{{$guru->jabatan}}</span>
            </div>
        </div>
</div>
@include('components.confirm-alert')
@endsection