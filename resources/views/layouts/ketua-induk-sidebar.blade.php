<div x-data="{
    openFinance: {{ request()->routeIs('ketua-induk.finance.*') ? 'true' : 'false' }},
    openOrganization: {{ request()->routeIs('ketua-induk.organization.*') ? 'true' : 'false' }},
    openReports: {{ request()->routeIs('ketua-induk.reports.*') ? 'true' : 'false' }}
}"
    class="h-full flex flex-col
           bg-blue-950
           text-white
           shadow-2xl">

    {{-- =========================================================
    BRAND
    ========================================================== --}}

    <div class="h-16 px-5
               border-b border-white/20
               flex items-center">

        <a href="{{ route('ketua-induk.dashboard') }}" class="flex items-center gap-3 w-full">

            {{-- Logo --}}

            @php
                $induk = Auth::user()->organizations->firstWhere('type', 'induk');
            @endphp

            <div
                class="w-10 h-10
           rounded-xl
           bg-white/20
           border border-white/30
           backdrop-blur-sm
           flex items-center justify-center
           shadow-sm
           overflow-hidden">

                @if ($induk?->logo_path)
                    <img src="{{ asset('storage/' . $induk->logo_path) }}" alt="{{ $induk->name }}"
                        class="w-full h-full object-contain p-1">
                @else
                    <span class="text-white text-lg font-bold">
                        {{ strtoupper(substr($induk?->name ?? 'P', 0, 1)) }}
                    </span>
                @endif

            </div>


            {{-- Nama aplikasi --}}

            <div class="leading-tight">

                <div class="font-bold
                           text-white
                           tracking-tight">
                    PMUB
                </div>

                <div class="text-[11px]
           text-white/70">

                    @if (Auth::user()->hasRole('ketua_induk'))
                        Ketua
                    @elseif (Auth::user()->hasRole('pengurus_induk'))
                        Pengurus
                    @endif

                </div>

            </div>

        </a>

    </div>


    {{-- =========================================================
    USER PROFILE
    ========================================================== --}}

    <div class="px-4 pt-5 pb-3">

        <div
            class="flex items-center gap-3
                   p-3
                   rounded-2xl
                   bg-white/10
                   border border-white/10
                   backdrop-blur-sm">

            {{-- Avatar --}}

            <div
                class="w-10 h-10
                       shrink-0
                       rounded-full
                       bg-white/20
                       border border-white/30
                       flex items-center justify-center">

                <span class="text-sm
                           font-bold
                           text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>

            </div>


            {{-- Nama --}}

            <div class="min-w-0">
                <p
                    class="text-sm
                           font-semibold
                           text-white
                           truncate">
                    {{ Auth::user()->name }}
                </p>
            </div>

        </div>

    </div>


    {{-- =========================================================
    NAVIGATION
    ========================================================== --}}

    <nav class="flex-1
               px-3
               pb-4
               overflow-y-auto">

        {{-- =====================================================
        UTAMA
        ====================================================== --}}

        <p
            class="px-3
                   mb-2
                   mt-2
                   text-[10px]
                   font-bold
                   uppercase
                   tracking-[0.18em]
                   text-white/50">
            Utama
        </p>


        {{-- Dashboard --}}

        <a href="{{ route('ketua-induk.dashboard') }}"
            class="group
                   flex items-center gap-3
                   px-3 py-2.5
                   mb-1
                   rounded-xl
                   transition-all duration-200
                   {{ request()->routeIs('ketua-induk.dashboard')
                       ? 'bg-white/20 text-white shadow-lg ring-1 ring-white/20'
                       : 'text-white/80 hover:bg-white/10 hover:text-white' }}">

            <span
                class="w-9 h-9
                       shrink-0
                       rounded-lg
                       flex items-center justify-center
                       transition
                       {{ request()->routeIs('ketua-induk.dashboard')
                           ? 'bg-white/20 text-white'
                           : 'bg-white/10 text-white/80 group-hover:bg-white/20' }}">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6" />

                </svg>

            </span>


            <span class="text-sm font-medium">
                Dashboard
            </span>

        </a>


        {{-- =====================================================
        NOTIFIKASI
        ====================================================== --}}

        @php

            $unreadCount = Auth::user()->unreadNotifications()->count();

        @endphp


        <a href="{{ route('ketua-induk.notifications.index') }}"
            class="group
                   flex items-center justify-between
                   px-3 py-2.5
                   mb-1
                   rounded-xl
                   transition-all duration-200
                   {{ request()->routeIs('ketua-induk.notifications.*')
                       ? 'bg-white/20 text-white shadow-lg ring-1 ring-white/20'
                       : 'text-white/80 hover:bg-white/10 hover:text-white' }}">

            <div class="flex items-center gap-3">

                <span
                    class="w-9 h-9
                           shrink-0
                           rounded-lg
                           flex items-center justify-center
                           transition
                           {{ request()->routeIs('ketua-induk.notifications.*')
                               ? 'bg-white/20 text-white'
                               : 'bg-white/10 text-white/80 group-hover:bg-white/20' }}">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M15 17h5l-1.5-1.8a2 2 0 0 1-.5-1.3V10a6 6 0 0 0-12 0v3.9a2 2 0 0 1-.5 1.3L4 17h5m6 0a3 3 0 0 1-6 0m6 0H9" />

                    </svg>

                </span>


                <span class="text-sm font-medium">
                    Notifikasi
                </span>

            </div>


            @if ($unreadCount > 0)
                <span
                    class="min-w-5 h-5
                           px-1.5
                           inline-flex
                           items-center
                           justify-center
                           rounded-full
                           text-[10px]
                           font-bold
                           bg-red-500
                           text-white
                           shadow-sm">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif

        </a>


        {{-- =====================================================
        KEUANGAN
        ====================================================== --}}

        <p
            class="px-3
                   mb-2
                   mt-6
                   text-[10px]
                   font-bold
                   uppercase
                   tracking-[0.18em]
                   text-white/50">
            Keuangan
        </p>


        <button type="button" @click="openFinance = !openFinance"
            class="w-full
                   group
                   flex items-center justify-between
                   px-3 py-2.5
                   rounded-xl
                   text-white/80
                   hover:bg-white/10
                   hover:text-white
                   transition-all duration-200">

            <div class="flex items-center gap-3">

                <span
                    class="w-9 h-9
                           shrink-0
                           rounded-lg
                           bg-white/10
                           text-white/80
                           flex items-center justify-center
                           group-hover:bg-white/20">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M3 7h18M5 7v12h14V7M8 11h8M8 15h5M7 4h10" />

                    </svg>

                </span>


                <span class="text-sm font-medium">
                    Keuangan
                </span>

            </div>


            <svg class="w-4 h-4
                       transition-transform duration-200"
                :class="{
                    'rotate-180': openFinance
                }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6" />

            </svg>

        </button>


        {{-- Submenu Keuangan --}}

        <div x-show="openFinance"
            class="mt-1
                   ml-5
                   pl-7
                   border-l border-white/20
                   space-y-1">

            {{-- Keuangan --}}

            <a href="{{ route('ketua-induk.finance.index') }}"
                class="flex items-center gap-2
                       px-3 py-2
                       rounded-lg
                       text-xs
                       transition
                       {{ request()->routeIs('ketua-induk.finance.*')
                           ? 'bg-white/15 text-white'
                           : 'text-white/60 hover:bg-white/10 hover:text-white' }}">

                <span
                    class="w-1.5 h-1.5
                           rounded-full
                           {{ request()->routeIs('ketua-induk.finance.*') ? 'bg-white' : 'bg-white/40' }}"></span>

                Ringkasan Keuangan

            </a>

            {{-- Laporan Keuangan --}}

            <a href="{{ route('ketua-induk.reports.finance.index') }}"
                class="flex items-center gap-2
                px-3 py-2
                rounded-lg
                text-xs
                transition
                {{ request()->routeIs('ketua-induk.reports.*')
                    ? 'bg-white/10 text-white'
                    : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <span
                    class="w-1.5 h-1.5
                    rounded-full
                    {{ request()->routeIs('ketua-induk.reports.*') ? 'bg-white' : 'bg-white/40' }}"></span>

                Laporan Keuangan
            </a>

        </div>


        {{-- =====================================================
        ORGANISASI
        ====================================================== --}}

        <p
            class="px-3
                   mb-2
                   mt-6
                   text-[10px]
                   font-bold
                   uppercase
                   tracking-[0.18em]
                   text-white/50">
            Organisasi
        </p>


        <button type="button" @click="openOrganization = !openOrganization"
            class="w-full
                   group
                   flex items-center justify-between
                   px-3 py-2.5
                   rounded-xl
                   text-white/80
                   hover:bg-white/10
                   hover:text-white
                   transition-all duration-200">

            <div class="flex items-center gap-3">

                <span
                    class="w-9 h-9
                           shrink-0
                           rounded-lg
                           bg-white/10
                           text-white/80
                           flex items-center justify-center
                           group-hover:bg-white/20">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M4 21V5l8-3 8 3v16M8 21v-4h8v4M8 8h2M14 8h2M8 12h2M14 12h2" />

                    </svg>

                </span>


                <span class="text-sm font-medium">
                    Organisasi
                </span>

            </div>


            <svg class="w-4 h-4
                       transition-transform duration-200"
                :class="{
                    'rotate-180': openOrganization
                }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6" />

            </svg>

        </button>


        {{-- Submenu Organisasi --}}

        <div x-show="openOrganization"
            class="mt-1
                   ml-5
                   pl-7
                   border-l border-white/20
                   space-y-1">

            {{-- Guru --}}

            <a href="{{ route('ketua-induk.teachers.index') }}"
                class="flex items-center gap-2
                px-3 py-2
                rounded-lg
                text-sm
                transition
                {{ request()->routeIs('ketua-induk.teachers.*')
                    ? 'bg-white/10 text-white'
                    : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <span
                    class="w-1.5 h-1.5
                    rounded-full
                    {{ request()->routeIs('ketua-induk.teachers.*') ? 'bg-white' : 'bg-white/40' }}"></span>

                Guru
            </a>


            {{-- Siswa --}}

            <a href="{{ route('ketua-induk.students.index') }}"
                class="flex items-center gap-2
                px-3 py-2
                rounded-lg
                text-sm
                transition
                {{ request()->routeIs('ketua-induk.students.*')
                    ? 'bg-white/10 text-white'
                    : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <span
                    class="w-1.5 h-1.5
                    rounded-full
                    {{ request()->routeIs('ketua-induk.students.*') ? 'bg-white' : 'bg-white/40' }}"></span>

                Siswa
            </a>
        </div>


        {{-- =====================================================
        LAPORAN
        ====================================================== --}}

        {{-- <p
            class="px-3
                   mb-2
                   mt-6
                   text-[10px]
                   font-bold
                   uppercase
                   tracking-[0.18em]
                   text-white/50">
            Laporan
        </p>


        <button type="button" @click="openReports = !openReports"
            class="w-full
                   group
                   flex items-center justify-between
                   px-3 py-2.5
                   rounded-xl
                   text-white/80
                   hover:bg-white/10
                   hover:text-white
                   transition-all duration-200">

            <div class="flex items-center gap-3">

                <span
                    class="w-9 h-9
                           shrink-0
                           rounded-lg
                           bg-white/10
                           text-white/80
                           flex items-center justify-center
                           group-hover:bg-white/20">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M4 19V5M4 19h16M8 16v-5M12 16V7M16 16v-3" />

                    </svg>

                </span>


                <span class="text-sm font-medium">
                    Laporan
                </span>

            </div>


            <svg class="w-4 h-4
                       transition-transform duration-200"
                :class="{
                    'rotate-180': openReports
                }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6" />

            </svg>

        </button> --}}


        {{-- Submenu Laporan --}}

        {{-- <div x-show="openReports"
            class="mt-1
                   ml-5
                   pl-7
                   border-l border-white/20
                   space-y-1"> --}}

            {{-- Data Siswa --}}

            {{-- <div
                class="flex items-center gap-2
                       px-3 py-2
                       rounded-lg
                       text-xs
                       text-white/60">

                <span
                    class="w-1.5 h-1.5
                           rounded-full
                           bg-white/40"></span>

                Data Siswa

            </div> --}}


            {{-- Absensi --}}

            {{-- <div
                class="flex items-center gap-2
                       px-3 py-2
                       rounded-lg
                       text-xs
                       text-white/60">

                <span
                    class="w-1.5 h-1.5
                           rounded-full
                           bg-white/40"></span>

                Absensi

            </div> --}}


        {{-- </div> --}}

    </nav>


    {{-- =========================================================
    BOTTOM
    ========================================================== --}}

    <div class="border-t
               border-white/20
               p-3">

        {{-- Profil --}}

        <a href="{{ route('profile.edit') }}"
            class="group
                   flex items-center gap-3
                   px-3 py-2.5
                   rounded-xl
                   text-sm
                   text-white/80
                   hover:bg-white/10
                   hover:text-white
                   transition-all duration-200">

            <span
                class="w-9 h-9
                       rounded-lg
                       bg-white/10
                       text-white/80
                       flex items-center justify-center
                       group-hover:bg-white/20">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M20 21a8 8 0 0 0-16 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />

                </svg>

            </span>


            <span class="font-medium">
                Profil
            </span>

        </a>


        {{-- Logout --}}

        <form method="POST" action="{{ route('logout') }}" class="mt-1">

            @csrf

            <button type="submit"
                class="group
                       w-full
                       flex items-center gap-3
                       px-3 py-2.5
                       rounded-xl
                       text-sm
                       text-white/70
                       hover:bg-red-500/20
                       hover:text-red-100
                       transition-all duration-200">

                <span
                    class="w-9 h-9
                           rounded-lg
                           bg-white/10
                           text-white/70
                           flex items-center justify-center
                           group-hover:bg-red-500/20
                           group-hover:text-red-100">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M10 17l5-5-5-5M15 12H3M21 19V5a2 2 0 0 0-2-2h-5" />

                    </svg>

                </span>


                <span class="font-medium">
                    Keluar
                </span>

            </button>

        </form>

    </div>

</div>
