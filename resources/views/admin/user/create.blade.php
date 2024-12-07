@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Tambahkan Data Akun') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.user.store') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <!-- Horizontal layout for Password Sekarang and vertical stack for Password Baru and Konfirmasi Password -->
        <div class="flex space-x-6 justify-end">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="username" :value="__('Username')"/>
                    <x-text-input id="username" name="username" type="text" placeholder="Ketikkan disini" class="mt-1 block w-full" required autofocus autocomplete="nip" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="role" :value="__('Role')" />
                    <x-dropdown-custom name="role_id" :options="$roles" selected="{{ old('role_id') }}" placeholder="Pilih role" class="w-full" />
                    <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                </div>

            </div>

            <div class="w-full md:w-1/2">
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                     <x-text-input id="password" name="password" type="password" placeholder="Ketikkan disini" class="mt-1 block w-full" autocomplete="new-password" />
                     <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </div>
        </div>


        <!-- Submit button -->
        <div class="flex space-x-6 justify-end">
            <div class="justify-center mb-4 md:w-1/2">
                <x-cancel-button class="bg-baseColor py-2 px-4 rounded" href="{{ route('admin.user.index') }}">
                    {{ __('Batal') }}
                </x-cancel-button>
            </div>
            <div class="justify-center mb-4 md:w-1/2">
                <x-save-button class="bg-primaryColor text-white py-2 px-4 rounded">
                    {{ __('Simpan') }}
                </x-save-button>
            </div>
        </div>
    </form>
</div>
@if (session('status') === 'user-created')
    <x-success-alert message="Berhasil menambahkan akun" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection