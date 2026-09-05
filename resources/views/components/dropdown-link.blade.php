<a
    {{ $attributes->merge([
        'class' => '
            flex w-full items-center
            rounded-lg
            px-3 py-2
            text-start
            text-sm
            font-medium
            leading-5
            text-gray-700

            transition
            duration-150
            ease-in-out

            hover:bg-gray-50
            hover:text-gray-900

            focus:bg-gray-50
            focus:text-gray-900
            focus:outline-none

            active:bg-gray-100
        '
    ]) }}
>
    {{ $slot }}
</a>
