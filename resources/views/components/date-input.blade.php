@props(['disabled' => false])

<div class="relative flex items-center">
    <!-- Input Field -->
    <input 
        {{ $disabled ? 'disabled' : '' }} 
        {!! $attributes->merge([
            'class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pr-10'
        ]) !!}
    >
    <span class="material-icons absolute right-3 text-primaryColor">date_range</span>
</div>
