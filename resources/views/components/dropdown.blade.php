@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'top' => 'origin-top',
        default => 'ltr:origin-top-right rtl:origin-top-left end-0',
    };

    $width = match ($width) {
        '48' => 'w-48',
        default => $width,
    };
@endphp

<div
    class="relative"
    x-data="{ open: false }"
    @click.outside="open = false"
    @close.stop="open = false"
>

    {{-- Trigger --}}
    <div @click="open = !open">
        {{ $trigger }}
    </div>


    {{-- Dropdown --}}
    <div
        x-show="open"
        x-cloak

        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"

        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"

        class="
            absolute
            z-50
            mt-2
            {{ $width }}
            {{ $alignmentClasses }}

            overflow-hidden
            rounded-xl
            border
            border-gray-200
            bg-white

            shadow-lg
            shadow-gray-200/60

            ring-1
            ring-black/5
        "

        style="display: none;"

        @click="open = false"
    >

        <div class="{{ $contentClasses }}">
            {{ $content }}
        </div>

    </div>

</div>
