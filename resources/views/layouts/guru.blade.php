<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'PMUB') }} -
        Guru
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-100 text-gray-800">

    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        {{-- ======================================================
        SIDEBAR MOBILE OVERLAY
        ======================================================= --}}

        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false">
        </div>


        {{-- ======================================================
        SIDEBAR GURU
        ======================================================= --}}

        <aside
            x-cloak
            class="fixed inset-y-0 left-0 z-50
                   w-64
                   bg-blue-950
                   border-r border-white/10
                   transform
                   transition-transform
                   duration-200
                   -translate-x-full
                   lg:translate-x-0"
            :class="{
                'translate-x-0': sidebarOpen
            }">

            @include('layouts.guru-sidebar')

        </aside>


        {{-- ======================================================
        MAIN AREA
        ======================================================= --}}

        <div class="lg:pl-64 min-h-screen">


            {{-- ==================================================
            NAVIGATION / TOPBAR
            =================================================== --}}

            @include('layouts.guru-navigation')


            {{-- ==================================================
            PAGE CONTENT
            =================================================== --}}

            <main class="p-4 sm:p-6 lg:p-8">

                @yield('content')

            </main>

        </div>

    </div>

</body>

</html>
