@extends('layouts.utama')

@section('content')
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"> 
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Edit Data Akun') }}
        </h2>
    </header>

    <form method="post" action="{{ route('admin.user.update', $user->id) }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <!-- Horizontal layout for Password Sekarang and vertical stack for Password Baru and Konfirmasi Password -->
        <div class="flex space-x-6 justify-end">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <div>
                    <x-input-label for="username" :value="__('Username')" />
                    <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" required autofocus :value="old('username', $user->username)" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Role')" />
                    <x-dropdown 
                        name="role_id" 
                        :options="$roles->pluck('display_name', 'id')" 
                        :selected="old('role_id') ?? $userRoleId" 
                        class="w-full" 
                    />
                    <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                </div>

            </div>

            <div class="w-full md:w-1/2">
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                     <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="***************" autocomplete="new-password" />
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
@if (session('status') === 'user-updated')
    <x-success-alert message="Berhasil mengubah data ini" icon="success" />
@elseif (session('status') === 'error')
    <x-error-alert title="Gagal!" message="Terjadi kesalahan saat menyimpan data." icon="error" />
@endif
@endsection