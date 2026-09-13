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


        {{-- Overlay tipis --}}

        <div class="absolute inset-0 bg-slate-950/55"></div>


        {{-- Gradient sangat ringan --}}

        <div
            class="absolute inset-0 bg-gradient-to-br
            from-blue-950/40
            via-transparent
            to-slate-950/70">
        </div>


        {{-- =========================================================
        CONTENT
        ========================================================== --}}

        <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8 lg:px-8">

            <div
                class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur-sm lg:grid-cols-2">


                {{-- =================================================
                LEFT PANEL
                ================================================== --}}

                <div
                    class="relative hidden overflow-hidden
                    bg-gradient-to-br from-blue-950/95
                    via-blue-900/90
                    to-blue-700/85
                    text-white
                    lg:flex lg:w-[100%]">


                    {{-- =================================================
                    MOTIF LINGKARAN ATAS
                    ================================================== --}}

                    <div
                        class="pointer-events-none absolute
                        -right-40 -top-40
                        h-[560px] w-[560px]
                        rounded-full
                        border border-blue-300/20">
                    </div>

                    <div
                        class="pointer-events-none absolute
                        -right-32 -top-32
                        h-[460px] w-[460px]
                        rounded-full
                        border border-blue-300/20">
                    </div>

                    <div
                        class="pointer-events-none absolute
                        -right-24 -top-24
                        h-[360px] w-[360px]
                        rounded-full
                        border border-blue-300/20">
                    </div>

                    <div
                        class="pointer-events-none absolute
                        -right-16 -top-16
                        h-[260px] w-[260px]
                        rounded-full
                        border border-blue-300/20">
                    </div>


                    {{-- =================================================
                    MOTIF LINGKARAN BAWAH KIRI
                    ================================================== --}}

                    <div
                        class="pointer-events-none absolute
                        -bottom-56 -left-56
                        h-[620px] w-[620px]
                        rounded-full
                        border border-blue-300/15">
                    </div>

                    <div
                        class="pointer-events-none absolute
                        -bottom-44 -left-44
                        h-[520px] w-[520px]
                        rounded-full
                        border border-blue-300/15">
                    </div>

                    <div
                        class="pointer-events-none absolute
                        -bottom-32 -left-32
                        h-[420px] w-[420px]
                        rounded-full
                        border border-blue-300/15">
                    </div>

                    <div
                        class="pointer-events-none absolute
                        -bottom-20 -left-20
                        h-[320px] w-[320px]
                        rounded-full
                        border border-blue-300/15">
                    </div>


                    {{-- =================================================
                    MOTIF LINGKARAN KANAN BAWAH
                    ================================================== --}}

                    <div
                        class="pointer-events-none absolute
                        -bottom-72 -right-72
                        h-[700px] w-[700px]
                        rounded-full
                        border border-blue-300/10">
                    </div>


                    {{-- =================================================
                    DOT PATTERN KANAN ATAS
                    ================================================== --}}

                    <div
                        class="pointer-events-none absolute
                        right-8 top-36
                        grid grid-cols-5 gap-3
                        opacity-60">

                        @for ($i = 0; $i < 25; $i++)
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-300">
                            </span>
                        @endfor

                    </div>


                    {{-- =================================================
                    DOT PATTERN KANAN BAWAH
                    ================================================== --}}

                    <div
                        class="pointer-events-none absolute
                        bottom-16 right-16
                        grid grid-cols-6 gap-3
                        opacity-30">

                        @for ($i = 0; $i < 24; $i++)
                            <span class="h-1 w-1 rounded-full bg-blue-300">
                            </span>
                        @endfor

                    </div>


                    {{-- =================================================
                    DEKORASI BULAT
                    ================================================== --}}

                    <div
                        class="pointer-events-none absolute
                        right-[38%] top-[38%]
                        h-6 w-6 rounded-full
                        bg-blue-300/10">
                    </div>

                    <div
                        class="pointer-events-none absolute
                        bottom-24 right-[35%]
                        h-24 w-24 rounded-full
                        bg-blue-400/10">
                    </div>


                    {{-- =================================================
                    KONTEN PANEL
                    ================================================== --}}

                    <div
                        class="relative z-10 flex w-full flex-col
                        justify-between
                        p-10 xl:p-14">


                        {{-- BAGIAN ATAS --}}

                        <div>

                            {{-- Logo --}}
                            @php
                                $induk = \App\Models\Organization::where('type', 'induk')->first();
                            @endphp

                            <div
                                class="mb-10 flex h-16 w-16
           items-center justify-center
           rounded-2xl
           border border-blue-300/40
           bg-blue-500/30
           shadow-xl shadow-blue-950/40
           backdrop-blur-md
           overflow-hidden">

                                @if ($induk?->logo_path)
                                    <img src="{{ asset('storage/' . $induk->logo_path) }}" alt="{{ $induk->name }}"
                                        class="h-full w-full object-contain p-2">
                                @else
                                    <span class="text-3xl font-bold text-white">
                                        P
                                    </span>
                                @endif

                            </div>


                            {{-- Label --}}

                            <p
                                class="mb-3 text-sm font-semibold
                                uppercase
                                tracking-[0.3em]
                                text-blue-200">

                                Sistem Informasi

                            </p>


                            {{-- PMUB --}}

                            <h1
                                class="text-5xl font-extrabold
                                tracking-tight
                                drop-shadow-lg
                                xl:text-6xl">

                                PMUB

                            </h1>


                            {{-- Garis aksen --}}

                            <div
                                class="mt-6 h-1.5 w-20
                                rounded-full
                                bg-gradient-to-r
                                from-blue-300
                                to-cyan-300
                                shadow-lg
                                shadow-blue-400/30">
                            </div>


                            {{-- Deskripsi --}}

                            <p
                                class="mt-7 max-w-md
                                text-base
                                leading-7
                                text-white/85
                                xl:text-lg">

                                Sistem informasi manajemen yang
                                terintegrasi untuk mendukung
                                pengelolaan data, akademik,
                                keuangan, dan administrasi PMUB.

                            </p>

                        </div>


                        {{-- FOOTER --}}

                        <div class="flex items-center gap-4
                            text-sm text-white/75">

                            <div
                                class="flex h-11 w-11
                                items-center justify-center
                                rounded-full
                                border border-blue-300/40
                                bg-blue-500/20
                                backdrop-blur-md">

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
                RIGHT PANEL / REGISTER
                ================================================== --}}

                <div class="bg-white px-5 py-6 sm:px-8 sm:py-8 lg:px-12 lg:py-10">


                    {{-- =================================================
                    LOGO MOBILE
                    ================================================== --}}

                    <div class="mb-4 flex items-center gap-3 lg:hidden">

                        <div
                            class="flex h-11 w-11
                            items-center justify-center
                            rounded-xl
                            bg-blue-600
                            shadow-md">

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


                    {{-- =================================================
                    HEADING
                    ================================================== --}}

                    <div class="mb-3">

                        <p class="text-sm font-medium text-blue-600">
                            Selamat datang
                        </p>

                        <h2 class="mt-1 text-2xl font-bold
                            tracking-tight text-slate-800">

                            Buat akun baru

                        </h2>

                        <p class="mt-2 text-sm text-slate-500">

                            Silakan lengkapi data untuk
                            membuat akun PMUB.

                        </p>

                    </div>


                    {{-- =================================================
                    FORM
                    ================================================== --}}

                    <form method="POST" action="{{ route('register') }}">

                        @csrf


                        {{-- =================================================
                        NAME
                        ================================================== --}}

                        <div>

                            <x-input-label for="name" value="Nama Lengkap"
                                class="text-sm font-medium text-slate-700" />

                            <div class="relative mt-2">

                                <div
                                    class="pointer-events-none
                                    absolute inset-y-0 left-0
                                    flex items-center pl-3">

                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />

                                    </svg>

                                </div>


                                <x-text-input id="name" name="name" type="text" :value="old('name')" required
                                    autofocus autocomplete="name" placeholder="Nama lengkap"
                                    class="block w-full rounded-xl
                                    border-slate-300
                                    py-3 pl-10
                                    text-sm shadow-sm transition
                                    focus:border-blue-500
                                    focus:ring-blue-500" />

                            </div>


                            <x-input-error :messages="$errors->get('name')" class="mt-2" />

                        </div>


                        {{-- =================================================
                        EMAIL
                        ================================================== --}}

                        <div class="mt-3">

                            <x-input-label for="email" value="Email" class="text-sm font-medium text-slate-700" />

                            <div class="relative mt-2">

                                <div
                                    class="pointer-events-none
                                    absolute inset-y-0 left-0
                                    flex items-center pl-3">

                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0119.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615A2.25 2.25 0 012.25 6.993V6.75" />

                                    </svg>

                                </div>


                                <x-text-input id="email" name="email" type="email" :value="old('email')" required
                                    autocomplete="username" placeholder="nama@email.com"
                                    class="block w-full rounded-xl
                                    border-slate-300
                                    py-3 pl-10
                                    text-sm shadow-sm transition
                                    focus:border-blue-500
                                    focus:ring-blue-500" />

                            </div>


                            <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        </div>


                        {{-- =================================================
                        PASSWORD
                        ================================================== --}}

                        <div class="mt-3">

                            <x-input-label for="password" value="Password"
                                class="text-sm font-medium text-slate-700" />

                            <div class="relative mt-2">

                                <div
                                    class="pointer-events-none
                                    absolute inset-y-0 left-0
                                    flex items-center pl-3">

                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h10.5a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25H7.5a2.25 2.25 0 01-2.25-2.25v-6A2.25 2.25 0 017.5 10.5z" />

                                    </svg>

                                </div>


                                <x-text-input id="password" name="password" type="password" required
                                    autocomplete="new-password" placeholder="Masukkan password"
                                    class="block w-full rounded-xl
                                    border-slate-300
                                    py-3 pl-10
                                    text-sm shadow-sm transition
                                    focus:border-blue-500
                                    focus:ring-blue-500" />

                            </div>


                            <x-input-error :messages="$errors->get('password')" class="mt-2" />

                        </div>


                        {{-- =================================================
                        CONFIRM PASSWORD
                        ================================================== --}}

                        <div class="mt-3">

                            <x-input-label for="password_confirmation" value="Konfirmasi Password"
                                class="text-sm font-medium text-slate-700" />

                            <div class="relative mt-2">

                                <div
                                    class="pointer-events-none
                                    absolute inset-y-0 left-0
                                    flex items-center pl-3">

                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75" />

                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 10.5h9a2.25 2.25 0 012.25 2.25v6A2.25 2.25 0 0116.5 21h-9a2.25 2.25 0 01-2.25-2.25v-6A2.25 2.25 0 017.5 10.5z" />

                                    </svg>

                                </div>


                                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                    required autocomplete="new-password" placeholder="Ulangi password"
                                    class="block w-full rounded-xl
                                    border-slate-300
                                    py-3 pl-10
                                    text-sm shadow-sm transition
                                    focus:border-blue-500
                                    focus:ring-blue-500" />

                            </div>


                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

                        </div>


                        {{-- =================================================
                        REGISTER
                        ================================================== --}}

                        <button type="submit"
                            class="
                                mt-4
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
                                duration-200
                                hover:bg-blue-700
                                hover:shadow-xl
                                focus:outline-none
                                focus:ring-2
                                focus:ring-blue-500
                                focus:ring-offset-2
                                active:scale-[0.99]
                            ">

                            Daftar Akun

                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L19.5 10.5M19.5 10.5L13.5 16.5M19.5 10.5H4.5" />

                            </svg>

                        </button>

                    </form>


                    {{-- =================================================
                    LOGIN LINK
                    ================================================== --}}

                    <p
                        class="
                            mt-4
                            text-center
                            text-sm
                            text-slate-500
                        ">

                        Sudah memiliki akun?

                        <a href="{{ route('login') }}"
                            class="
                                font-medium
                                text-blue-600
                                transition
                                hover:text-blue-800
                            ">
                            Masuk ke akun
                        </a>

                    </p>


                    {{-- =================================================
                    FOOTER MOBILE
                    ================================================== --}}

                    <p
                        class="
                            mt-5
                            text-center
                            text-xs
                            text-slate-400
                            lg:hidden
                        ">

                        &copy; {{ date('Y') }} PMUB.
                        Semua hak dilindungi.

                    </p>

                </div>

            </div>

        </div>

    </div>

</x-guest-layout>
