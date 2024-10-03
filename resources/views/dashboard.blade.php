@extends('layouts.utama')

@section('content')
<div class="flex justify-center items-center w-full mt-8 p-4 bg-white rounded-xl space-x-44">
    <!-- Akun Section -->
    <div class="flex items-center space-x-4">
        <div class="flex items-center justify-center w-44 h-20 bg-dangerColor rounded-lg">
            <span class="material-icons text-white" style="font-size: 36px;">
                account_circle
            </span>
        </div>
        <div class="text-center">
            <p class="text-gray-500">Akun</p>
            <p class="text-5xl text-customColor">52</p>
        </div>
    </div>

    <!-- Role Section -->
    <div class="flex items-center space-x-4">
        <div class="flex items-center justify-center w-44 h-20 bg-orange-500 rounded-lg">
            <span class="material-icons text-white" style="font-size: 36px;">
                accessibility
            </span>
        </div>
        <div class="text-center">
            <p class="text-gray-500">Role</p>
            <p class="text-5xl text-customColor">3</p>
        </div>
    </div>
</div>
@endsection