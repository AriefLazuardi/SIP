<section>
    <header>
        <h2 class="text-2xl font-bold text-customColor">
            {{ __('Data Profil') }}
        </h2>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6">
        @csrf
        @method('patch')
        <div>
            <x-input-label for="username" :value="__('Username')"/>
            <x-text-input id="username" username="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autofocus autocomplete="username" disabled />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>
        <div class="flex items-center">
            @if (session('status') === 'profile-updated')
                <x-success-alert message="Profil berhasil diperbarui" icon="success" />
            @endif
        </div>
    </form>
</section>
