<div x-data="{}"
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

        <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 w-full">

            {{-- Logo --}}
            @php
                $induk = \App\Models\Organization::where('type', 'induk')->first();
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
                        P
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
                    Guru
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

                <p class="text-xs
                           text-white/60
                           truncate">
                    Guru
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
        <a href="{{ route('guru.dashboard') }}"
            class="group
                   flex items-center gap-3
                   px-3 py-2.5
                   mb-1
                   rounded-xl
                   transition-all duration-200
                   {{ request()->routeIs('guru.dashboard')
                       ? 'bg-white/20 text-white shadow-lg ring-1 ring-white/20'
                       : 'text-white/80 hover:bg-white/10 hover:text-white' }}">

            <span
                class="w-9 h-9
                       shrink-0
                       rounded-lg
                       flex items-center justify-center
                       transition
                       {{ request()->routeIs('guru.dashboard')
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
        ABSENSI
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
            Absensi
        </p>


        {{-- Absensi Siswa --}}
        <a href="{{ route('guru.attendance.index') }}"
            class="group
                   flex items-center gap-3
                   px-3 py-2.5
                   mb-1
                   rounded-xl
                   text-white/80
                   hover:bg-white/10
                   hover:text-white
                   transition-all duration-200">

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
                        d="M9 11h6M9 15h4M7 3h10v4H7zM5 5H4a1 1 0 0 0-1 1v14h18V6a1 1 0 0 0-1-1h-1" />
                </svg>
            </span>

            <span class="text-sm font-medium">
                Absensi Siswa
            </span>

        </a>


        {{-- Absensi Guru --}}
        <a href="{{ route('guru.teacher-attendance.index') }}"
            class="group
                   flex items-center gap-3
                   px-3 py-2.5
                   mb-1
                   rounded-xl
                   text-white/80
                   hover:bg-white/10
                   hover:text-white
                   transition-all duration-200">

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
                        d="M9 11h6M9 15h4M7 3h10v4H7zM5 5H4a1 1 0 0 0-1 1v14h18V6a1 1 0 0 0-1-1h-1" />
                </svg>
            </span>

            <span class="text-sm font-medium">
                Absensi Guru
            </span>

        </a>


        {{-- Riwayat Absensi --}}
        {{-- <a href="#"
            class="group
                   flex items-center gap-3
                   px-3 py-2.5
                   mb-1
                   rounded-xl
                   text-white/80
                   hover:bg-white/10
                   hover:text-white
                   transition-all duration-200">

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
                        d="M12 8v4l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </span>

            <span class="text-sm font-medium">
                Riwayat Absensi
            </span>

        </a> --}}

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
