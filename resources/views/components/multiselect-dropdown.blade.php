@props([
    'label' => 'Select Items',
    'items' => [],
    'selectedItems' => [],
    'name' => '',
    'id' => ''
])

<div x-data="initMultiselectDropdown(@json($selectedItems))" 
     x-init="console.log('Initial selected:', selected)"
     class="relative w-full">
    <button type="button" @click="open = !open" class="border border-customColor rounded-md px-4 py-2 w-full text-left flex items-center justify-between">
        <span x-show="selected.length === 0">{{ $label }}</span>
        <span x-show="selected.length > 0" x-text="selected.join(', ')"></span>
        <span class="material-icons ml-2">
            <span x-show="!open">keyboard_arrow_down</span>
            <span x-show="open">keyboard_arrow_up</span>
        </span>
    </button>
    <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg">
        <div class="p-2">
            @foreach($items as $value => $label)
                <div class="flex items-center mb-2">
                    <input 
                        type="checkbox" 
                        :value="'{{ $label }}'"
                        value="{{ $value }}"
                        name="{{ $name }}[]" 
                        @change="toggleSelection('{{ $label }}')" 
                        id="{{ $id }}_{{ $value }}"
                        x-bind:checked="isSelected('{{ $label }}')"
                    >
                    <label for="{{ $id }}_{{ $value }}" class="ml-2">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        <div class="flex justify-end p-2 border-t">
            <button type="button" @click="open = false" class="bg-green-500 text-white px-4 py-2 rounded-md">Simpan</button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function initMultiselectDropdown(selectedItems) {
                return {
                    open: false,
                    selected: selectedItems,
                    toggleSelection(item) {
                        if (this.isSelected(item)) {
                            this.selected = this.selected.filter(i => !this.compareValues(i, item));
                        } else {
                            this.selected.push(item);
                        }
                        console.log('After toggle:', this.selected);
                    },
                    isSelected(item) {
                        return this.selected.some(i => this.compareValues(i, item));
                    },
                    compareValues(val1, val2) {
                        if (typeof val1 === 'string' && typeof val2 === 'string') {
                            return val1.toLowerCase().trim() === val2.toLowerCase().trim();
                        }
                        return val1 == val2;
                    }
                }
            }
        </script>
    @endpush
@endonce