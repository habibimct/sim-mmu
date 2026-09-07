<x-guest-layout>

    <div class="relative min-h-screen overflow-hidden bg-slate-950">

        {{-- =========================================================
        BACKGROUND FOTO
        ========================================================== --}}

        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="
                background-image:
                    url('https://interieurbouwenschrijnwerk.be/wp-content/uploads/2020/10/sonacoustic-pl.jpg');
            ">
        </div>


        {{-- =========================================================
        OVERLAY
        ========================================================== --}}

        <div class="absolute inset-0 bg-slate-950/55"></div>

        <div class="absolute inset-0 bg-gradient-to-br from-blue-950/40 via-transparent to-slate-950/70"></div>


        {{-- =========================================================
        CONTENT
        ========================================================== --}}

        <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8 lg:px-8">

            {{-- =====================================================
            CARD
            ====================================================== --}}

            <div
                class="
                    grid
                    w-full
                    max-w-5xl
                    overflow-hidden
                    rounded-3xl
                    border
                    border-white/10
                    bg-white/5
                    shadow-2xl
                    backdrop-blur-sm
                    lg:grid-cols-2
                ">


                {{-- =================================================
                LEFT PANEL
                ================================================== --}}

                <div
                    class="
                        relative
                        hidden
                        overflow-hidden
                        bg-gradient-to-br
                        from-blue-950/95
                        via-blue-900/90
                        to-blue-700/85
                        text-white
                        lg:flex
                    ">

                    {{-- =================================================
                    MOTIF
                    ================================================== --}}

                    <div
                        class="
                            pointer-events-none
                            absolute
                            -right-40
                            -top-40
                            h-[560px]
                            w-[560px]
                            rounded-full
                            border
                            border-blue-300/20
                        ">
                    </div>

                    <div
                        class="
                            pointer-events-none
                            absolute
                            -right-32
                            -top-32
                            h-[460px]
                            w-[460px]
                            rounded-full
                            border
                            border-blue-300/20
                        ">
                    </div>

                    <div
                        class="
                            pointer-events-none
                            absolute
                            -right-24
                            -top-24
                            h-[360px]
                            w-[360px]
                            rounded-full
                            border
                            border-blue-300/20
                        ">
                    </div>

                    <div
                        class="
                            pointer-events-none
                            absolute
                            -bottom-56
                            -left-56
                            h-[620px]
                            w-[620px]
                            rounded-full
                            border
                            border-blue-300/15
                        ">
                    </div>

                    <div
                        class="
                            pointer-events-none
                            absolute
                            -bottom-44
                            -left-44
                            h-[520px]
                            w-[520px]
                            rounded-full
                            border
                            border-blue-300/15
                        ">
                    </div>


                    {{-- =================================================
                    DOT PATTERN
                    ================================================== --}}

                    <div
                        class="
                            pointer-events-none
                            absolute
                            right-8
                            top-36
                            grid
                            grid-cols-5
                            gap-3
                            opacity-60
                        ">

                        @for ($i = 0; $i < 25; $i++)
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-300"></span>
                        @endfor

                    </div>


                    {{-- =================================================
                    CONTENT PANEL
                    ================================================== --}}

                    <div
                        class="
                            relative
                            z-10
                            flex
                            w-full
                            flex-col
                            justify-between
                            p-10
                            xl:p-14
                        ">

                        {{-- TOP --}}

                        <div>

                            {{-- Logo --}}

                            <div
                                class="
                                    mb-10
                                    flex
                                    h-16
                                    w-16
                                    items-center
                                    justify-center
                                    rounded-2xl
                                    border
                                    border-blue-300/40
                                    bg-blue-500/30
                                    shadow-xl
                                    shadow-blue-950/40
                                    backdrop-blur-md
                                ">

                                <span class="text-3xl font-bold">
                                    P
                                </span>

                            </div>


                            {{-- Label --}}

                            <p
                                class="
                                    mb-3
                                    text-sm
                                    font-semibold
                                    uppercase
                                    tracking-[0.3em]
                                    text-blue-200
                                ">
                                Sistem Informasi
                            </p>


                            {{-- Nama --}}

                            <h1
                                class="
                                    text-5xl
                                    font-extrabold
                                    tracking-tight
                                    drop-shadow-lg
                                    xl:text-6xl
                                ">
                                PMUB
                            </h1>


                            {{-- Aksen --}}

                            <div
                                class="
                                    mt-6
                                    h-1.5
                                    w-20
                                    rounded-full
                                    bg-gradient-to-r
                                    from-blue-300
                                    to-cyan-300
                                ">
                            </div>


                            {{-- Deskripsi --}}

                            <p
                                class="
                                    mt-7
                                    max-w-md
                                    text-base
                                    leading-7
                                    text-white/85
                                    xl:text-lg
                                ">
                                Sistem informasi manajemen yang
                                terintegrasi untuk mendukung
                                pengelolaan data, akademik,
                                keuangan, dan administrasi PMUB.
                            </p>

                        </div>


                        {{-- FOOTER --}}

                        <div
                            class="
                                flex
                                items-center
                                gap-4
                                text-sm
                                text-white/75
                            ">

                            <div
                                class="
                                    flex
                                    h-11
                                    w-11
                                    items-center
                                    justify-center
                                    rounded-full
                                    border
                                    border-blue-300/40
                                    bg-blue-500/20
                                ">

                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 21V9.75L12 3l7.5 6.75V21M8.25 21v-6.75h7.5V21" />
                                </svg>

                            </div>

                            <div>

                                <p class="font-medium text-white">
                                    &copy; {{ date('Y') }} PMUB
                                </p>

                                <p class="mt-1">
                                    Sistem Informasi Manajemen
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                RIGHT PANEL
                ================================================== --}}

                <div
                    class="
                        flex
                        flex-col
                        justify-center
                        bg-white
                        px-6
                        py-10
                        sm:px-10
                        lg:px-12
                        lg:py-12
                    ">

                    {{-- Logo mobile --}}

                    <div class="mb-8 flex items-center gap-3 lg:hidden">

                        <div
                            class="
                                flex
                                h-11
                                w-11
                                items-center
                                justify-center
                                rounded-xl
                                bg-blue-600
                                shadow-md
                            ">

                            <span class="font-bold text-white">
                                P
                            </span>

                        </div>

                        <div>

                            <h1 class="font-bold text-slate-800">
                                PMUB
                            </h1>

                            <p class="text-xs text-slate-500">
                                Sistem Informasi Manajemen
                            </p>

                        </div>

                    </div>


                    {{-- Heading --}}

                    <div>

                        <p
                            class="
                                text-sm
                                font-medium
                                text-blue-600
                            ">
                            Selamat datang
                        </p>

                        <h2
                            class="
                                mt-2
                                text-3xl
                                font-bold
                                tracking-tight
                                text-slate-800
                            ">
                            Kelola PMUB
                            <br>
                            dengan lebih mudah.
                        </h2>

                        <p
                            class="
                                mt-3
                                max-w-lg
                                text-sm
                                leading-6
                                text-slate-500
                            ">
                            Satu sistem untuk membantu mengelola
                            berbagai kebutuhan administrasi,
                            akademik, keuangan, dan data PMUB
                            secara terintegrasi.
                        </p>

                    </div>


                    {{-- =================================================
                    FEATURES
                    ================================================== --}}

                    <div class="mt-6 mb-3 space-y-5">

                        {{-- Feature 1 --}}

                        <div class="flex items-center gap-4">

                            <div
                                class="
                                    flex
                                    h-11
                                    w-11
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-blue-50
                                    text-blue-600
                                ">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-700">
                                    Data terintegrasi
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Data tersimpan dalam satu sistem.
                                </p>

                            </div>

                        </div>


                        {{-- Feature 2 --}}

                        <div class="flex items-center gap-4">

                            <div
                                class="
                                    flex
                                    h-11
                                    w-11
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-blue-50
                                    text-blue-600
                                ">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.7">
                                    <circle cx="12" cy="12" r="8.5" />

                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                                </svg>
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-700">
                                    Efisien dan terorganisir
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Membantu pekerjaan menjadi lebih terarah.
                                </p>

                            </div>

                        </div>


                        {{-- Feature 3 --}}

                        <div class="flex items-center gap-4">

                            <div
                                class="
                                    flex
                                    h-11
                                    w-11
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-blue-50
                                    text-blue-600
                                ">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z" />

                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4" />
                                </svg>
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-700">
                                    Aman dan terkontrol
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Akses disesuaikan dengan peran pengguna.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                    BUTTON LOGIN
                    ================================================== --}}

                    <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                        class="
                            mt-9
                            flex
                            w-full
                            items-center
                            justify-center
                            rounded-xl
                            bg-blue-600
                            px-4
                            py-3
                            text-sm
                            font-semibold
                            text-white
                            shadow-lg
                            shadow-blue-600/20
                            transition
                            hover:bg-blue-700
                            hover:shadow-xl
                        ">
                        Masuk ke PMUB

                        <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L19.5 10.5M19.5 10.5L13.5 16.5M19.5 10.5H4.5" />
                        </svg>
                    </a>

                </div>

            </div>

        </div>

    </div>

</x-guest-layout>
