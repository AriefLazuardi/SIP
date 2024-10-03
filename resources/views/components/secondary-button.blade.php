<button 
    {{ $attributes->merge([
        'type' => 'button', 
        'class' => '
            inline-flex 
            items-center 
            py-3
            px-20
            bg-white
            border-2 border-customColor
            rounded-md 
            text-sm font-semibold 
            text-customColor 
            tracking-wide 
            focus:outline-none 
            focus:ring-4 
            focus:ring-green-400 
            focus:ring-opacity-50 
            transition 
            ease-in-out 
            duration-200
        ']) }} >
    {{ $slot }}
</button>