<button 
    {{ $attributes->merge([
        'type' => 'submit', 
        'class' => '
            inline-flex 
            items-center 
            py-3
            w-full
            px-60
            bg-whiteColor
            border-dangerColor
            border-2
            rounded-md 
            text-sm font-semibold 
            text-dangerColor 
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
            cancel
        </span>
    {{ $slot }}
</button>
