<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ config('app.name', 'PMUB') }} -
        {{ $pageTitle ?? 'Unit' }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>

<body class="bg-gray-100 text-gray-800">

    <div
        x-data="{ sidebarOpen: false }"
        class="min-h-screen"
    >

        {{-- ======================================================
        SIDEBAR MOBILE OVERLAY
        ======================================================= --}}

        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40
                   bg-black/50
                   lg:hidden"
            @click="sidebarOpen = false"
        ></div>


        {{-- ======================================================
        SIDEBAR
        ======================================================= --}}

        <aside
            x-cloak
            class="fixed inset-y-0 left-0 z-50
                   w-64
                   bg-white
                   border-r border-gray-200
                   transform
                   transition-transform
                   duration-200
                   -translate-x-full
                   lg:translate-x-0"
            :class="{
                'translate-x-0': sidebarOpen
            }"
        >

            {{-- Tahap 2 nanti kita sambungkan
                 ke unit-sidebar --}}

            @include('layouts.unit-sidebar')

        </aside>


        {{-- ======================================================
        MAIN AREA
        ======================================================= --}}

        <div class="lg:pl-64 min-h-screen">

            {{-- ==================================================
            TOPBAR
            =================================================== --}}

            <header
                class="h-16
                       bg-white
                       border-b border-gray-200
                       flex items-center
                       justify-between
                       px-4 sm:px-6"
            >

                {{-- Mobile menu --}}

                <button
                    type="button"
                    @click="sidebarOpen = true"
                    class="lg:hidden
                           inline-flex
                           items-center
                           justify-center
                           p-2
                           rounded-lg
                           text-gray-500
                           hover:bg-gray-100"
                >

                    <svg
                        class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                    </svg>

                </button>


                {{-- Judul --}}

                <div class="hidden sm:block">

                    <h1
                        class="text-lg
                               font-semibold
                               text-gray-800"
                    >
                        {{ $pageTitle ?? 'Unit' }}
                    </h1>

                </div>


                {{-- User --}}

                <div
                    class="flex items-center
                           gap-3
                           ml-auto"
                >

                    <div class="text-right">

                        <p
                            class="text-sm
                                   font-medium
                                   text-gray-800"
                        >
                            {{ Auth::user()->name }}
                        </p>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            {{ $userRoleLabel ?? 'Unit' }}
                        </p>

                    </div>


                    <div
                        class="w-9 h-9
                               rounded-full
                               bg-gray-200
                               flex items-center
                               justify-center"
                    >

                        <span
                            class="text-sm
                                   font-semibold
                                   text-gray-600"
                        >
                            {{ strtoupper(
                                substr(
                                    Auth::user()->name,
                                    0,
                                    1
                                )
                            ) }}
                        </span>

                    </div>

                </div>

            </header>


            {{-- ==================================================
            PAGE CONTENT
            =================================================== --}}

            <main
                class="p-4
                       sm:p-6
                       lg:p-8"
            >

                @yield('content')

            </main>

        </div>

    </div>

</body>

</html>
