<a 
    {{ $attributes->merge([
        'type' => 'submit', 
        'class' => '
            inline-flex 
            items-center 
            py-3
            w-full
            px-56
            bg-baseColor
            border-dangerColor
            border-2
            rounded-md 
            text-sm font-semibold 
            text-dangerColor 
            tracking-wide 
            shadow-lg 
            hover:shadow-xl 
            transition 
            ease-in-out 
            duration-200
        ']) }}>
        <span class="material-icons mr-2">
            cancel
        </span>
    {{ $slot }}
    </a>
