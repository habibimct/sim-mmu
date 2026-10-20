<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#F8FAFC">

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

    <title>@yield('title', 'Beranda') | SIM-MMU</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>


<body class="min-h-screen bg-white text-slate-800 antialiased sm:bg-slate-100">

    <div class="mx-auto min-h-screen max-w-lg px-3 py-3 sm:px-5 sm:py-5">

        <div
            class="relative min-h-[calc(100vh-24px)] overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:min-h-[calc(100vh-40px)]">

            {{-- Konten halaman --}}
            <main class="px-4 pb-28 pt-4 sm:px-5">
                @yield('content')
            </main>

            {{-- Navigasi bawah: tetap terlihat saat halaman di-scroll --}}

            <nav aria-label="Navigasi utama"
                class="fixed bottom-0 left-1/2 z-50 w-full max-w-[440px] -translate-x-1/2 border-t border-slate-100 bg-slate-30/95 shadow-lg backdrop-blur sm:bottom-5 sm:rounded-b-2xl">

                <div class="grid grid-cols-3 px-3 py-3">

                    {{-- Beranda --}}
                    <a href="{{ Route::has('wali.dashboard') ? route('wali.dashboard') : '#' }}"
                        @if (request()->routeIs('wali.dashboard')) aria-current="page" @endif
                        class="flex min-h-12 flex-col items-center justify-center gap-1 rounded-xl text-xs transition
                        {{ request()->routeIs('wali.dashboard') ? 'font-medium text-emerald-700' : 'text-slate-500 hover:bg-slate-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-5 w-5" aria-hidden="true">
                            <path d="m3 10 9-7 9 7" />
                            <path d="M5 9v12h14V9" />
                            <path d="M9 21v-7h6v7" />
                        </svg>
                        <span>Beranda</span>
                    </a>

                    {{-- Anak --}}
                    <a href="{{ Route::has('wali.children.index') ? route('wali.children.index') : '#' }}"
                        @if (request()->routeIs('wali.children.*')) aria-current="page" @endif
                        class="flex min-h-12 flex-col items-center justify-center gap-1 rounded-xl text-xs transition
                        {{ request()->routeIs('wali.children.*') ? 'font-medium text-emerald-700' : 'text-slate-500 hover:bg-slate-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-5 w-5" aria-hidden="true">
                            <circle cx="9" cy="7" r="3" />
                            <path d="M3 20v-2a6 6 0 0 1 12 0v2" />
                            <path d="M16 4.5a3 3 0 0 1 0 5.8" />
                            <path d="M18 14a5 5 0 0 1 3 4.5V20" />
                        </svg>
                        <span>Anak</span>
                    </a>

                    {{-- Profil --}}
                    <a href="{{ Route::has('wali.profile') ? route('wali.profile') : '#' }}"
                        @if (request()->routeIs('wali.profile')) aria-current="page" @endif
                        class="flex min-h-12 flex-col items-center justify-center gap-1 rounded-xl text-xs transition
                        {{ request()->routeIs('wali.profile') ? 'font-medium text-emerald-700' : 'text-slate-500 hover:bg-slate-100' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-5 w-5" aria-hidden="true">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21v-2a8 8 0 0 1 16 0v2" />
                        </svg>
                        <span>Profil</span>
                    </a>

                </div>
            </nav>

        </div>
    </div>

    @stack('scripts')
</body>


</html>
