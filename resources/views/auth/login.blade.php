<x-guest-layout>
    <div class="w-full">
        <x-auth-session-status class="mb-4" :status="session('status')" />

       <div class=" p-6 rounded-lg w-120">
            <h1 class="text-5xl font-bold text-left mt-8 pt-2 mb-2 text-customColor">Masuk</h1>
            <h4 class="mb-20 font-medium">Silahkan ketikan username dan passwordmu di bawah ini</h4>
            <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="-mt-10 mr-14">
                <x-text-input id="username" class="w-full p-3 border-2 border-green-500 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 mb-6" type="username" name="username" :value="old('username')" required autofocus autocomplete="username" placeholder="Username"/>
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <div class="mr-14">
                <x-text-input id="password" class="w-full p-3 border-2 border-green-500 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 mb-6" type="password" name="password" required autocomplete="current-password" placeholder="Password"/>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="mr-14">
                <button class="w-full bg-green-500 text-white text-center font-semibold py-2 rounded hover:bg-green-600 transition duration-300 mb-20">
                        {{ __('Masuk') }}
                </button>
            </div>
        </form>
       </div>
    </div>
</x-guest-layout>