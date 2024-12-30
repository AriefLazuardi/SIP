<div x-data="{ sidebarOpen: true }" class="relative">
    <!-- Hamburger button -->
    <button 
        @click="sidebarOpen = !sidebarOpen; $dispatch('sidebar-toggle', sidebarOpen)" 
        class="fixed top-4 left-4 z-50 transition-transform duration-200"
        :class="{'translate-x-48': sidebarOpen, 'translate-y-4': sidebarOpen, 'translate-x-1': !sidebarOpen, 'pt-5': !sidebarOpen}"
    >
    <span class="material-icons -mt-5">dehaze</span>
    </button>

    <!-- Sidebar -->
    <aside 
    class="fixed inset-y-0 left-0 transform transition-transform duration-300 ease-in-out z-10 bg-baseColor shadow-md"
    :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}">

        <!-- Logo and Title -->
        <div class="p-4 flex items-center space-x-2 bg-baseColor" :class="{'p-4': sidebarOpen}">
            <img src="{{ Vite::asset('resources/images/logo-kementrianagama.png') }}" class="w-12 h-8"
                :class="{'w-12': sidebarOpen, 'pl-2.5': !sidebarOpen, 'mt-16': !sidebarOpen, 'mb-2': !sidebarOpen}">
            <span class="text-sm font-semibold" x-show="sidebarOpen">Sistem<br> Penjadwalan</span>
        </div>
        
        <nav>
            <!-- Beranda Menu -->
            <a href="{{ route(getDashboardRoute()) }}" class="pt-2 block px-5 py-2 rounded-md {{ setActiveClass('beranda') }}" :class="{'pt-10': !sidebarOpen}">
                <div :class="{'flex items-center justify-center space-x-5': !sidebarOpen, 'flex items-center space-x-5': sidebarOpen}">
                    <span class="material-icons text-2xl">dashboard</span>
                    <span x-show="sidebarOpen">Beranda</span>
                </div>
            </a>
            @role('wakakurikulum')
            <!-- Data Akademik (Dropdown) -->
            <div x-data="{ open: false }" :class="{'-mb-5': !sidebarOpen}">
                <button @click="open = !open" class="w-full px-5 pt-4 py-2 text-left flex items-center justify-between" :class="{'-translate-y-4': !sidebarOpen}">
                    <div class="flex items-center space-x-4 rounded-md">
                        <span class="material-icons text-2xl" >dataset</span>
                        <span x-show="sidebarOpen" class="pl-1">Data Akademik</span>
                        <span x-show="!open" class="material-icons" :class="{'pl-5': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-10': !sidebarOpen}">keyboard_arrow_down</span>
                        <span x-show="open" class="material-icons" :class="{'pl-5': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-10': !sidebarOpen}">keyboard_arrow_up</span>
                    </div>
                </button>
                <!-- Submenu (collapsed/expanded) -->
                <div x-show="open" class="w-full" :class="sidebarOpen ? 'pl-5' : 'pl-0'">
                    <a href="{{route('wakilkurikulum.tugasmengajar.index')}}" class="block px-5 py-2 rounded-md {{setActiveClass('tugasmengajar')}}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">view_timeline</span>
                            <span x-show="sidebarOpen">Data Tugas Mengajar</span>
                        </div>
                    </a>
                </div>
            </div>
            @endrole
            @role('administrator')
            <a href="{{route('admin.user.index')}}" class="block py-2 pl-5 px-0.5 rounded-md {{ setActiveClass('kelola_akun') }}">
                <div class="flex items-center space-x-5">
                <span class="material-icons text-2xl" :class="{'-translate-x-0': !sidebarOpen, 'pt-2': !sidebarOpen}">person_add_alt_1</span>
                <span x-show="sidebarOpen">Kelola Akun</span>
                </div>
            </a>
            <div x-data="{ open: false }" :class="{'-mb-5': !sidebarOpen}">
                <button @click="open = !open" class="w-full px-5 pt-4 py-2 text-left flex items-center justify-between" :class="{'-translate-y-4': !sidebarOpen}">
                    <div class="flex items-center space-x-4 rounded-md">
                        <span class="material-icons text-2xl" >dataset</span>
                        <span x-show="sidebarOpen" class="pl-1">Data Akademik</span>
                        <span x-show="!open" class="material-icons" :class="{'pl-5': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-10': !sidebarOpen}">keyboard_arrow_down</span>
                        <span x-show="open" class="material-icons" :class="{'pl-5': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-10': !sidebarOpen}">keyboard_arrow_up</span>
                    </div>
                </button>
                <!-- Submenu (collapsed/expanded) -->
                <div x-show="open" class="w-full" :class="sidebarOpen ? 'pl-5' : 'pl-0'">
                    <a href="{{route('admin.guru.index')}}" 
                        class="w-full block px-5 py-2 rounded-md {{ setActiveClass('guru') }}">
                        <div class="flex items-center space-x-4 flex-grow">
                            <span class="material-icons text-2xl" >groups</span>
                            <span x-show="sidebarOpen">Data Guru</span>
                            </div>
                    </a>
                    <a href="{{route('admin.kurikulum.index')}}" 
                        class="w-full block px-5 py-2 rounded-md {{ setActiveClass('kurikulum') }}">
                        <div class="flex items-center space-x-4 flex-grow">
                            <span class="material-icons text-2xl" >map</span>
                            <span x-show="sidebarOpen">Data Kurikulum</span>
                            </div>
                    </a>
                    <a href="{{route('admin.mapel.index')}}" class="block px-5 py-2 rounded-md {{ setActiveClass('mapel')}}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">menu_book</span>
                            <span x-show="sidebarOpen">Data Mata Pelajaran</span>
                        </div>
                    </a>
                    <a href="{{route('admin.kelas.index')}}" class="block px-5 py-2 rounded-md {{ setActiveClass('kelas') }}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">school</span>
                            <span x-show="sidebarOpen">Data Kelas</span>
                        </div>
                    </a>
                    <a href="{{route('admin.walikelas.index')}}" class="block px-5 py-2 rounded-md {{ setActiveClass('walikelas') }}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">supervisor_account</span>
                            <span x-show="sidebarOpen">Data Wali Kelas</span>
                        </div>
                    </a>
                    <a href="{{route('admin.slotwaktu.index')}}" class="block px-5 py-2 rounded-md {{ setActiveClass('slotwaktu') }}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">schedule</span>
                            <span x-show="sidebarOpen">Data Slot Waktu</span>
                        </div>
                    </a>
                    <a href="{{route('admin.tahunajaran.index')}}" class="block px-5 py-2 rounded-md {{setActiveClass('tahunajaran')}}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">timeline</span>
                            <span x-show="sidebarOpen">Data Tahun Ajaran</span>
                        </div>
                    </a>
                    <!-- <a href="{{route('admin.ruangan.index')}}" class="block px-5 py-2 rounded-md {{ setActiveClass('ruangan') }}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">meeting_room</span>
                            <span x-show="sidebarOpen">Data Ruangan</span>
                        </div>
                    </a> -->
                    <!-- <a href="{{route('admin.hari.index')}}" class="block px-5 py-2 rounded-md {{ setActiveClass('hari') }}">
                        <div class="flex items-center space-x-4">
                            <span class="material-icons text-2xl">today</span>
                            <span x-show="sidebarOpen">Data Hari</span>
                        </div>
                    </a> -->
                </div>
            </div>
            <a href="{{route('admin.jadwal.index')}}" class="block py-2 pb-2 pl-5 px-0.5 rounded-md {{ setActiveClass('jadwal') }}" :class="{'mt-5': !sidebarOpen}">
                <div class="flex items-center space-x-5">
                <span class="material-icons text-2xl" :class="{'-translate-x-0': !sidebarOpen, 'pt-2': !sidebarOpen}">table_chart</span>
                <span x-show="sidebarOpen">Jadwal</span>
                </div>
            </a>
            @endrole
            @role('wakakurikulum')
            <a href="{{route('wakilkurikulum.penjadwalan.index')}}" class="block py-2 pb-2 pl-5 px-0.5 rounded-md {{ setActiveClass('penjadwalan') }}" :class="{'mt-5': !sidebarOpen}">
                <div class="flex items-center space-x-5">
                    <span class="material-icons text-2xl" :class="{'-translate-x-0': !sidebarOpen, 'pt-2': !sidebarOpen}">table_chart</span>
                    <span x-show="sidebarOpen">Penjadwalan</span>
                </div>
            </a>
            @endrole
            @role('guru')
            <div x-data="{ open: false }" :class="{'-mb-5': !sidebarOpen}">
                <button @click="open = !open" class="w-full px-5 pt-4 py-2 text-left flex items-center justify-between" :class="{'-translate-y-4': !sidebarOpen}">
                    <div class="flex items-center space-x-4 rounded-md">
                        <span class="material-icons text-2xl" >backup_table</span>
                        <span x-show="sidebarOpen" class="pl-1">Jadwal</span>
                        <span x-show="!open" class="material-icons" :class="{'pl-16': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-12': !sidebarOpen}">keyboard_arrow_down</span>
                        <span x-show="open" class="material-icons" :class="{'pl-16': sidebarOpen, 'translate-y-3': !sidebarOpen, '-translate-x-12': !sidebarOpen, 'pl-2': !sidebarOpen, 'pt-12': !sidebarOpen}">keyboard_arrow_up</span>
                    </div>
                </button>
                <!-- Submenu (collapsed/expanded) -->
                <div x-show="open" class="w-full" :class="sidebarOpen ? 'pl-5' : 'pl-0'">
                    <a href="{{route('guru.jadwal.index')}}" 
                        class="w-full block px-5 py-2 rounded-md {{ setActiveClass('jadwalKeseluruhan') }}">
                        <div class="flex items-center space-x-4 flex-grow">
                            <span class="material-icons text-2xl" >grid_on</span>
                            <span x-show="sidebarOpen">Jadwal Keseluruhan</span>
                        </div>
                    </a>
                    <a href="{{route('guru.jadwal.pribadi')}}" 
                        class="w-full block px-5 py-2 rounded-md {{ setActiveClass('jadwalPribadi') }}">
                        <div class="flex items-center space-x-4 flex-grow">
                            <span class="material-icons text-2xl" >switch_account</span>
                            <span x-show="sidebarOpen">Jadwal Pribadi</span>
                        </div>
                    </a>
                </div>
            </div>
            @endrole
        </nav>
    </aside>
</div>
