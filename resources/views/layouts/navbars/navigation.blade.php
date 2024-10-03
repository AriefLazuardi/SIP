<nav class="bg-primaryColor text-white p-6" >
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center">
            <!-- Judul Beranda -->
            <h1 class="text-3xl font-bold ml-40" :class="{'-translate-x-48': !sidebarOpen}" x-data="{ sidebarOpen: open }" @sidebar-toggle.window="sidebarOpen = $event.detail">
                @switch(Route::currentRouteName())
                    @case('profile.edit')
                        Profile
                        @break
                    @case('home')
                        Beranda
                        @break
                    @case('other.route.name')
                        Other Page Title
                        @break
                    @default
                        Beranda
                @endswitch
            </h1>
            
            <!-- Menu user dan pengaturan -->
            <div class="flex items-center space-x-4 translate-x-24">
                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 text-gray-700">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>