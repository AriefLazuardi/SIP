@props(['name', 'options', 'selected' => null, 'placeholder' => ''])

<div x-data="{
    open: false,
    selectedColor: '{{ $selected ? $options[$selected] : '' }}',
    selectedId: '{{ $selected }}',
    toggle() { this.open = !this.open },
    close() { this.open = false }
}" class="relative w-full">
    <!-- Tombol Dropdown -->
    <div 
        @click="toggle()"
        class="bg-white border border-customColor rounded-md flex items-center cursor-pointer w-full h-10"
    >
        <!-- Bagian untuk menampilkan warna terpilih -->
        <div class="flex-grow h-full p-2 " :style="selectedColor ? `background-color: ${selectedColor};` : ''">
            <span x-show="!selectedColor" class="text-customColor" x-text="'{{ $placeholder }}'"></span>
            <span x-show="selectedColor" class="block w-full h-full">&nbsp;</span>
        </div>
        
        <!-- Bagian untuk ikon panah yang tetap di luar warna -->
        <div class="flex items-center px-2">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <!-- Menu Dropdown dengan Scroll dan Jarak Antar Warna -->
    <div 
        x-show="open" 
        @click.away="close()"
        class="absolute z-10 w-full bg-white border border-gray-300 mt-1 shadow-lg max-h-56 overflow-y-auto"
    >
        @foreach ($options as $id => $kode_hex)
            <div 
                class="cursor-pointer h-8 my-3 mx-5"
                :class="{ 'hover:bg-gray-200': selectedId !== '{{ $id }}' }"
                style="background-color: {{ $kode_hex }};"
                @click="selectedColor = '{{ $kode_hex }}'; selectedId = '{{ $id }}'; open = false"
            >
                &nbsp;
            </div>
        @endforeach
    </div>

    <!-- Input Tersembunyi untuk Nilai Terpilih -->
    <input type="hidden" name="{{ $name }}" x-bind:value="selectedId">
</div>
