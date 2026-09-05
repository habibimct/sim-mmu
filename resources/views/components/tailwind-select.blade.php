@props([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Pilih',
    'label' => null,
])

@php
    // Support array maupun Collection
    $options = $options instanceof \Illuminate\Support\Collection
        ? $options->toArray()
        : $options;

    $selected = (string) $selected;

    $selectedLabel = $options[$selected] ?? $placeholder;
@endphp

<div
    x-data="{
        open: false,
        value: @js($selected),
        selectedLabel: @js($selectedLabel),

        options: @js($options),

        select(value, label) {
            this.value = String(value);
            this.selectedLabel = label;
            this.open = false;
        }
    }"
    x-on:keydown.escape.window="open = false"
    @click.outside="open = false"
    class="relative w-full"
>

    {{-- Label --}}
    @if ($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif


    {{-- Nilai sebenarnya yang dikirim ke form --}}
    <input
        type="hidden"
        name="{{ $name }}"
        x-model="value"
    >


    {{-- Tombol Dropdown --}}
    <button
        type="button"
        @click="open = !open"
        class="flex w-full items-center justify-between
               rounded-lg border border-gray-300
               bg-white px-3 py-2.5
               text-left text-sm text-gray-700
               shadow-sm
               transition
               hover:border-gray-400
               focus:outline-none
               focus:ring-2 focus:ring-blue-500/20"
    >

        <span
            class="truncate"
            :class="value === '' ? 'text-gray-400' : 'text-gray-700'"
            x-text="selectedLabel"
        ></span>

        <svg
            class="ml-2 h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
            />
        </svg>

    </button>


    {{-- Dropdown --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute left-0 right-0 z-50 mt-1
               overflow-hidden
               rounded-lg
               border border-gray-200
               bg-white
               shadow-lg"
    >

        <div class="max-h-60 overflow-y-auto py-1">

            {{-- Placeholder / Semua --}}
            <button
                type="button"
                @click="select('', @js($placeholder))"
                class="flex w-full items-center
                       px-3 py-2.5
                       text-left text-sm
                       transition
                       hover:bg-gray-50"
                :class="value === ''
                    ? 'bg-blue-50 text-blue-700 font-medium'
                    : 'text-gray-700'"
            >

                <span class="flex-1">
                    {{ $placeholder }}
                </span>

                <svg
                    x-show="value === ''"
                    class="h-4 w-4 text-blue-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>

            </button>


            {{-- Daftar pilihan --}}
            @foreach ($options as $value => $label)
                <button
                    type="button"
                    @click="select(@js((string) $value), @js($label))"
                    class="flex w-full items-center
                           px-3 py-2.5
                           text-left text-sm
                           transition
                           hover:bg-gray-50"
                    :class="value === @js((string) $value)
                        ? 'bg-blue-50 text-blue-700 font-medium'
                        : 'text-gray-700'"
                >

                    <span class="flex-1 truncate">
                        {{ $label }}
                    </span>

                    <svg
                        x-show="value === @js((string) $value)"
                        class="h-4 w-4 shrink-0 text-blue-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>

                </button>
            @endforeach

        </div>

    </div>

</div>
