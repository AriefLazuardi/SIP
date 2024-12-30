<section>
    <header>
        <h2 class="text-xl font-semibold text-customColor">
            {{ __('Perbarui Password') }}
        </h2>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-2 space-y-6">
        @csrf
        @method('put')

        <!-- Horizontal layout for Password Sekarang and vertical stack for Password Baru and Konfirmasi Password -->
        <div class="flex space-x-6 justify-end">
            <!-- Password Sekarang on the left -->
            <div class="w-full md:w-1/2">
                <x-input-label for="update_password_current_password" :value="__('Password Sekarang')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <!-- Password Baru and Konfirmasi Password stacked vertically on the right -->
            <div class="w-full md:w-1/2">
                <div class="mb-4">
                    <x-input-label for="update_password_password" :value="__('Password Baru')" />
                    <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" />
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Submit button -->
        <div class="flex justify-center mt-4 md:w-1/2">
            <x-save-button class="bg-primaryColor text-white py-2 px-4 rounded">
                {{ __('Simpan') }}
            </x-save-button>

            @if (session('status') === 'password-updated')
                <x-success-alert message="Berhasil mengganti password" />
            @endif
        </div>
    </form>
</section>
