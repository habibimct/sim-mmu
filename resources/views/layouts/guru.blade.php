<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="simmmu-loading">

<head>

    {{-- ======================================================
    CRITICAL CSS / FIRST PAINT PROTECTION
    ======================================================= --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        /*
        |--------------------------------------------------------------------------
        | Critical Tailwind Utility
        |--------------------------------------------------------------------------
        | Mencegah elemen .hidden muncul sebelum Tailwind selesai dimuat.
        */

        [class~="hidden"] {
            display: none !important;
        }

        /*
        |--------------------------------------------------------------------------
        | First Paint Protection
        |--------------------------------------------------------------------------
        */

        html {
            background: #f3f4f6;
        }

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

<script>
    window.simMmuOfflineIdentity = {
        user_id: {{ auth()->id() }},
        teacher_id: {{ auth()->user()->teacher->id }},
        name: "{{ auth()->user()->name }}",
        email: "{{ auth()->user()->email }}",
        role: "guru"
    };
</script>

<script>
    window.addEventListener('load', async function () {
        try {
            if (window.SimMmuOfflineAuth) {
                await window.SimMmuOfflineAuth.saveCurrentUserOffline();
                console.log('[SIM-MMU Offline Auth] Identitas Guru tersimpan.');
            }
        } catch (error) {
            console.error(
                '[SIM-MMU Offline Auth] Gagal menyimpan identitas:',
                error
            );
        }
    });
</script>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0d6efd">


    {{-- ======================================================
    LOGO / FAVICON INDUK
    ======================================================= --}}

    @php
        $induk = \App\Models\Organization::where('type', 'induk')->first();
    @endphp

    @if ($induk?->logo_path)
        <link rel="icon" type="image/webp"
            href="{{ asset('storage/' . $induk->logo_path) }}?v={{ $induk->updated_at?->timestamp }}">

        <link rel="shortcut icon" type="image/webp"
            href="{{ asset('storage/' . $induk->logo_path) }}?v={{ $induk->updated_at?->timestamp }}">
    @endif


    {{-- ======================================================
    TITLE
    ======================================================= --}}

    <title>
        {{ config('app.name', 'PMUB') }} -
        Guru
    </title>


    {{-- ======================================================
    VITE
    ======================================================= --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    {{-- ======================================================
    PAGE READY
    ======================================================= --}}

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

    @include('components.pwa-install-script')

</head>

<body class="bg-gray-100 text-gray-800">

    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        {{-- ======================================================
        SIDEBAR MOBILE OVERLAY
        ======================================================= --}}

        <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false">
        </div>


        {{-- ======================================================
        SIDEBAR GURU
        ======================================================= --}}

        <aside x-cloak
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
