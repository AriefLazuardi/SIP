
@php
    use App\Helpers\RouteTitleHelper;

    $currentRouteName = Route::currentRouteName();
    $title = RouteTitleHelper::getTitle($currentRouteName);
@endphp

<nav class="bg-primaryColor text-white p-6 fixed top-0 right-0 transition-all duration-300 z-50 h-20"
     :class="{'left-64': sidebarOpen, 'left-16': !sidebarOpen}"
     x-data="{ sidebarOpen: true }" 
     @sidebar-toggle.window="sidebarOpen = $event.detail">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center">

        <div class="flex items-center" x-data="{ sidebarOpen: true }" @sidebar-toggle.window="sidebarOpen = $event.detail">
            <div class="w-8 h-8 mr-4"></div>
                <!-- Judul Beranda -->
                <h1 class="text-3xl font-bold transition-transform duration-300" :class="{'-translate-x-10': sidebarOpen, '-translate-x-28': !sidebarOpen}" x-data="{ sidebarOpen: true }" @sidebar-toggle.window="sidebarOpen = $event.detail">
               {{ $title }}
            </h1>
        </div>

            
            <!-- Menu user dan pengaturan -->
            <div class="flex items-center space-x-4 fixed top-6 right-10">
                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-2 mt-2 w-32 bg-white rounded-md shadow-lg py-1 text-customColor font-semibold">
                        <a href="{{ route('profile.edit') }}" class="block mr-2 py-2 text-sm hover:bg-gray-100">
                            <div class="flex justify-center space-x-4">
                                <span class="material-icons y-10">person</span>
                                <span class="translate-y-1">Akun</span>
                            </div>
                        </a>
                        <!-- x-data -->
                        <!-- @submit.prevent="confirmLogout" -->
                        <form 
                            method="POST" 
                            action="{{ route('logout') }}"
                        >
                            @csrf
                            <button type="submit" class="block w-full py-2 text-sm hover:bg-gray-100">
                                <div class="flex justify-center space-x-3 mr-2">
                                    <span class="material-icons ml-2">logout</span>
                                    <span class="translate-y-0.5 translate-x-0.5">Keluar</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- <script>
    function confirmLogout(event) {
        event.preventDefault();
        
        Swal.fire({
            text: 'Apakah Anda yakin ingin keluar dari aplikasi?',
            icon: 'warning',
            showDenyButton: true,
            confirmButtonText: 'Ya',
            denyButtonText: 'Batal',
            customClass: {
                denyButton: 'bg-primaryColor text-white px-24 py-2 rounded-md ml-2',
                confirmButton: 'bg-white border-2 border-dangerColor text-dangerColor px-24 py-2 rounded-md mr-2',
                popup: 'flex flex-col items-center'
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form secara manual setelah konfirmasi
                event.target.submit();
            }
        });
    }
</script> -->