<button 
    {{ $attributes->merge([
        'type' => 'submit', 
        'class' => '
            inline-flex 
            items-center 
            py-3
            w-full
            px-56
            bg-primaryColor
            border-2 border-transparent
            rounded-md 
            text-sm font-semibold 
            text-white 
            tracking-wide 
            shadow-lg 
            hover:shadow-xl 
            focus:outline-none 
            focus:ring-4 
            focus:ring-green-400 
            focus:ring-opacity-50 
            transition 
            ease-in-out 
            duration-200
        ']) }}>
    <span class="material-icons mr-2">
        save
    </span>
    {{ $slot }}
</button>
