<div x-data="{ sidebarOpen: true }" class="relative">
    <!-- Hamburger button -->
    <button 
        @click="sidebarOpen = !sidebarOpen; $dispatch('sidebar-toggle', sidebarOpen)" 
        class="fixed top-4 left-4 z-50 transition-transform duration-300"
        :class="{'translate-x-48': sidebarOpen, 'translate-y-4': sidebarOpen, 'translate-x-1': !sidebarOpen, 'pt-5': !sidebarOpen}"
    >
    <span class="material-icons -mt-5">dehaze</span>
    </button>

    <!-- Sidebar -->
    <aside 
    class="fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out z-10 bg-white shadow-md"
    :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}">

        <!-- Logo and Title -->
        <div class="p-4 flex items-center space-x-2 bg-white" :class="{'p-4': sidebarOpen}">
            <img src="{{ Vite::asset('resources/images/logo-kementrianagama.png') }}" class="w-12 h-8"
                :class="{'w-12': sidebarOpen, 'pl-2.5': !sidebarOpen, 'mt-16': !sidebarOpen, 'mb-2': !sidebarOpen}">
            <span class="text-sm font-semibold" x-show="sidebarOpen">Sistem<br> Penjadwalan</span>
        </div>
        
        <nav>
            <!-- Beranda Menu -->
            <a href="dashboard" class="pt-2 block px-5 py-2 bg-primaryColor text-white divide-y" :class="{'pt-10': !sidebarOpen}">
                <div :class="{'flex items-center justify-center space-x-5': !sidebarOpen, 'flex items-center space-x-5': sidebarOpen}">
                    <span class="material-icons text-2xl">dashboard</span>
                    <span x-show="sidebarOpen">Beranda</span>
                </div>
            </a>
            
            @role('wakilkurikulum')
            <!-- Data Akademik (Dropdown) -->
            <div x-data="{ open: false }" :class="{'-mb-5': !sidebarOpen}">
                <button @click="open = !open" class="w-full px-5 pt-4 py-2 text-left flex items-center justify-between" :class="{'-translate-y-4': !sidebarOpen}">
                    <div class="flex items-center space-x-4">
                        <span class="material-icons text-2xl" >dataset</span>
                        <span x-show="sidebarOpen" class="pl-1">Data Akademik</span>
                        <span x-show="!open" class="material-icons" :class="{'pl-5': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-10': !sidebarOpen}">keyboard_arrow_down</span>
                        <span x-show="open" class="material-icons" :class="{'pl-5': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-10': !sidebarOpen}">keyboard_arrow_up</span>
                    </div>
                </button>
                <!-- Submenu (collapsed/expanded) -->
                <div x-show="open" class="pl-10">
                    <a href="#" class="block py-2" >
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">groups</span>
                            <span x-show="sidebarOpen">Data Guru</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">menu_book</span>
                            <span x-show="sidebarOpen">Data Mata Pelajaran</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">school</span>
                            <span x-show="sidebarOpen">Data Kelas</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">meeting_room</span>
                            <span x-show="sidebarOpen">Data Ruangan</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">today</span>
                            <span x-show="sidebarOpen">Data Hari</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">schedule</span>
                            <span x-show="sidebarOpen">Data Waktu</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">view_module</span>
                            <span x-show="sidebarOpen">Data Slot</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">timeline</span>
                            <span x-show="sidebarOpen">Data Tahun Ajaran</span>
                        </div>
                    </a>
                    <a href="#" class="block py-2">
                        <div class="flex items-center space-x-2">
                            <span class="material-icons text-2xl" :class="{'-translate-x-5': !sidebarOpen}">view_timeline</span>
                            <span x-show="sidebarOpen">Data Tugas Mengajar</span>
                        </div>
                    </a>
                </div>
            </div>

            <a href="#" class="block py-2 pt-4 pl-5 px-0.5">
                <div class="flex items-center space-x-5">
                <span class="material-icons text-2xl" :class="{'-translate-x-0': !sidebarOpen, 'pt-2': !sidebarOpen}">table_chart</span>
                <span x-show="sidebarOpen">Penjadwalan</span>
                </div>
            </a>
            @endrole
            @role('administrator')
            <a href="#" class="block py-2 pt-4 pl-5 px-0.5">
                <div class="flex items-center space-x-5">
                <span class="material-icons text-2xl" :class="{'-translate-x-0': !sidebarOpen, 'pt-2': !sidebarOpen}">person_add_alt_1</span>
                <span x-show="sidebarOpen">Tambah Akun</span>
                </div>
            </a>
            @endrole
        </nav>
    </aside>
</div>
