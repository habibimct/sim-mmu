<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="simmmu-loading">

<head>

    {{-- ======================================================
    CSS AWAL
    Harus inline agar aktif sebelum Vite/Tailwind selesai
    ======================================================= --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        /*
    |--------------------------------------------------------------------------
    | Critical Tailwind utilities
    |--------------------------------------------------------------------------
    | Mencegah elemen class "hidden" muncul sebelum Tailwind selesai dimuat.
    */

        [class~="hidden"] {
            display: none !important;
        }

        /*
    |--------------------------------------------------------------------------
    | First paint protection
    |--------------------------------------------------------------------------
    */

        html.simmmu-loading body {
            visibility: hidden;
        }

        html.simmmu-loading::before {
            content: "Loading...";
            position: fixed;
            inset: 0;
            z-index: 999999;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #f3f4f6;

            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #6b7280;
        }

        html.simmmu-ready body {
            visibility: visible;
        }

        html.simmmu-ready::before {
            display: none;
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0d6efd">

    @php
        $induk = \App\Models\Organization::where('type', 'induk')->first();
    @endphp

    @if ($induk?->logo_path)
        <link rel="icon" type="image/webp"
            href="{{ asset('storage/' . $induk->logo_path) }}?v={{ $induk->updated_at?->timestamp }}">
    @endif


    <title>
        {{ config('app.name', 'PMUB') }} -
        Kepala Unit
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

@include('components.pwa-install-script')
</head>

<body class="bg-gray-100 text-gray-800">


    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        {{-- ======================================================
        SIDEBAR MOBILE OVERLAY
        ======================================================= --}}

        <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"></div>


        {{-- ======================================================
        SIDEBAR
        ======================================================= --}}

        <aside x-cloak
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 -translate-x-full lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }">
            @include('layouts.kepala-unit-sidebar')
        </aside>


        {{-- ======================================================
        MAIN AREA
        ======================================================= --}}

        <div class="lg:pl-64 min-h-screen">

            {{-- ==================================================
            NAVIGATION / TOPBAR
            =================================================== --}}

            @include('layouts.kepala-unit-navigation')

            {{-- ==================================================
            PAGE CONTENT
            =================================================== --}}

            <main class="p-4 sm:p-6 lg:p-8">

                @yield('content')

            </main>

        </div>
    </div>


    <script>
        window.addEventListener('load', function() {

            document.documentElement.classList.remove(
                'simmmu-loading'
            );

            document.documentElement.classList.add(
                'simmmu-ready'
            );

        });
    </script>

</body>

</html>
