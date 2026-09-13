<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', config('app.name', 'SIM-MMU'))
    </title>

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>


<body class="font-sans antialiased bg-slate-50 text-slate-800">

    <div class="min-h-screen flex flex-col">


        {{-- =====================================================
             TOP NAVIGATION
        ====================================================== --}}
        <header class="sticky top-0 z-50
            bg-white/95 backdrop-blur
            border-b border-slate-200">

            <div class="max-w-7xl mx-auto
                px-4 sm:px-6 lg:px-8">

                <div class="h-16
                    flex items-center
                    justify-between">


                    {{-- BRAND --}}
                    <a href="{{ route('dashboard') }}"class="flex items-center gap-3 hover:text-slate-900
                            hover:bg-slate-100
                            transition">
                        <div
                            class="w-10 h-10
                            rounded-xl
                            bg-slate-900
                            flex items-center
                            justify-center">
                            <span class="text-white
                                font-bold text-sm">
                                SM
                            </span>
                        </div>

                        <div class="leading-tight">
                            <div class="font-bold text-slate-900 tracking-tight">
                                SIM-MMU
                            </div>
                            <div
                                class="text-[10px] uppercase tracking-widest
                                text-slate-400">
                                Sistem Informasi
                            </div>
                        </div>

                    </a>


                    {{-- USER --}}
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:block text-right">
                            <div class="text-sm font-semibold text-slate-800">
                                {{ Auth::user()->name }}
                            </div>

                            <div class="text-xs text-slate-400">
                                {{ Auth::user()->email }}
                            </div>
                        </div>

                        <div
                            class="w-9 h-9
        rounded-full
        bg-slate-900
        overflow-hidden
        flex items-center
        justify-center">

                            @if (Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                    alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-semibold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </header>


        {{-- =====================================================
             PAGE HEADER
        ====================================================== --}}
        @isset($header)
            <div class="bg-white
                border-b border-slate-200">

                <div class="max-w-7xl mx-auto
                    px-4 sm:px-6 lg:px-8
                    py-6">

                    {{ $header }}

                </div>

            </div>
        @endisset


        {{-- =====================================================
             CONTENT
        ====================================================== --}}
        <main class="flex-1">

            <div class="max-w-7xl mx-auto
                px-4 sm:px-6 lg:px-8
                py-6 lg:py-8">

                {{ $slot }}

            </div>

        </main>


        {{-- =====================================================
             FOOTER
        ====================================================== --}}
        <footer class="border-t
            border-slate-200
            bg-white">

            <div class="max-w-7xl mx-auto
                px-4 sm:px-6 lg:px-8
                py-4">

                <div
                    class="flex flex-col
                    sm:flex-row
                    justify-between
                    items-center
                    gap-2">

                    <div class="text-xs
                        text-slate-400">
                        © {{ date('Y') }} SIM-MMU

                    </div>

                    <div class="text-xs
                        text-slate-400">
                        Sistem Informasi Manajemen

                    </div>

                </div>

            </div>

        </footer>


    </div>

</body>

</html>
