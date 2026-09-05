<x-guest-layout>

    <div class="relative min-h-screen overflow-hidden bg-slate-950">

        {{-- =========================================================
        BACKGROUND FOTO
        ========================================================== --}}

        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="
                background-image:
                    url('https://interieurbouwenschrijnwerk.be/wp-content/uploads/2020/10/sonacoustic-pl.jpg');
            "
        ></div>

        <div class="absolute inset-0 bg-slate-950/55"></div>

        <div
            class="absolute inset-0 bg-gradient-to-br
            from-blue-950/40
            via-transparent
            to-slate-950/70">
        </div>


        {{-- =========================================================
        CONTENT
        ========================================================== --}}

        <div
            class="
                relative
                z-10
                flex
                min-h-screen
                items-center
                justify-center
                px-4
                py-8
                lg:px-8
            "
        >

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
                "
            >

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
                    "
                >

                    {{-- Lingkaran atas --}}

                    <div
                        class="
                            pointer-events-none absolute
                            -right-40 -top-40
                            h-[560px] w-[560px]
                            rounded-full
                            border border-blue-300/20
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            -right-32 -top-32
                            h-[460px] w-[460px]
                            rounded-full
                            border border-blue-300/20
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            -right-24 -top-24
                            h-[360px] w-[360px]
                            rounded-full
                            border border-blue-300/20
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            -right-16 -top-16
                            h-[260px] w-[260px]
                            rounded-full
                            border border-blue-300/20
                        "
                    ></div>


                    {{-- Lingkaran bawah kiri --}}

                    <div
                        class="
                            pointer-events-none absolute
                            -bottom-56 -left-56
                            h-[620px] w-[620px]
                            rounded-full
                            border border-blue-300/15
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            -bottom-44 -left-44
                            h-[520px] w-[520px]
                            rounded-full
                            border border-blue-300/15
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            -bottom-32 -left-32
                            h-[420px] w-[420px]
                            rounded-full
                            border border-blue-300/15
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            -bottom-20 -left-20
                            h-[320px] w-[320px]
                            rounded-full
                            border border-blue-300/15
                        "
                    ></div>


                    {{-- Lingkaran kanan bawah --}}

                    <div
                        class="
                            pointer-events-none absolute
                            -bottom-72 -right-72
                            h-[700px] w-[700px]
                            rounded-full
                            border border-blue-300/10
                        "
                    ></div>


                    {{-- Dot pattern --}}

                    <div
                        class="
                            pointer-events-none absolute
                            right-8 top-36
                            grid grid-cols-5 gap-3
                            opacity-60
                        "
                    >

                        @for ($i = 0; $i < 25; $i++)

                            <span
                                class="h-1.5 w-1.5 rounded-full bg-blue-300"
                            ></span>

                        @endfor

                    </div>


                    <div
                        class="
                            pointer-events-none absolute
                            bottom-16 right-16
                            grid grid-cols-6 gap-3
                            opacity-30
                        "
                    >

                        @for ($i = 0; $i < 24; $i++)

                            <span
                                class="h-1 w-1 rounded-full bg-blue-300"
                            ></span>

                        @endfor

                    </div>


                    {{-- Dekorasi --}}

                    <div
                        class="
                            pointer-events-none absolute
                            right-[38%] top-[38%]
                            h-6 w-6 rounded-full
                            bg-blue-300/10
                        "
                    ></div>

                    <div
                        class="
                            pointer-events-none absolute
                            bottom-24 right-[35%]
                            h-24 w-24 rounded-full
                            bg-blue-400/10
                        "
                    ></div>


                    {{-- =================================================
                    PANEL CONTENT
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
                        "
                    >

                        <div>

                            {{-- Logo --}}

                            <div
                                class="
                                    mb-10
                                    flex h-16 w-16
                                    items-center justify-center
                                    rounded-2xl
                                    border border-blue-300/40
                                    bg-blue-500/30
                                    shadow-xl
                                    shadow-blue-950/40
                                    backdrop-blur-md
                                "
                            >

                                <span class="text-3xl font-bold">
                                    P
                                </span>

                            </div>


                            <p
                                class="
                                    mb-3
                                    text-sm
                                    font-semibold
                                    uppercase
                                    tracking-[0.3em]
                                    text-blue-200
                                "
                            >
                                Sistem Informasi
                            </p>


                            <h1
                                class="
                                    text-5xl
                                    font-extrabold
                                    tracking-tight
                                    drop-shadow-lg
                                    xl:text-6xl
                                "
                            >
                                PMUB
                            </h1>


                            <div
                                class="
                                    mt-6
                                    h-1.5
                                    w-20
                                    rounded-full
                                    bg-gradient-to-r
                                    from-blue-300
                                    to-cyan-300
                                    shadow-lg
                                    shadow-blue-400/30
                                "
                            ></div>


                            <p
                                class="
                                    mt-7
                                    max-w-md
                                    text-base
                                    leading-7
                                    text-white/85
                                    xl:text-lg
                                "
                            >
                                Sistem informasi manajemen yang
                                terintegrasi untuk mendukung
                                pengelolaan data, akademik,
                                keuangan, dan administrasi PMUB.
                            </p>

                        </div>


                        {{-- Footer --}}

                        <div
                            class="
                                flex
                                items-center
                                gap-4
                                text-sm
                                text-white/75
                            "
                        >

                            <div
                                class="
                                    flex h-11 w-11
                                    items-center justify-center
                                    rounded-full
                                    border border-blue-300/40
                                    bg-blue-500/20
                                    backdrop-blur-md
                                "
                            >

                                <svg
                                    class="h-5 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 21V9.75L12 3l7.5 6.75V21M8.25 21v-6.75h7.5V21"
                                    />

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
                RIGHT PANEL / VERIFY EMAIL
                ================================================== --}}

                <div
                    class="
                        bg-white
                        px-5
                        py-7
                        sm:px-8
                        sm:py-9
                        lg:px-12
                        lg:py-12
                    "
                >

                    {{-- =================================================
                    MOBILE LOGO
                    ================================================== --}}

                    <div
                        class="
                            mb-6
                            flex
                            items-center
                            gap-3
                            lg:hidden
                        "
                    >

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
                            "
                        >

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
                    ICON VERIFIKASI
                    ================================================== --}}

                    <div
                        class="
                            mb-5
                            flex
                            h-14
                            w-14
                            items-center
                            justify-center
                            rounded-2xl
                            bg-blue-50
                            text-blue-600
                        "
                    >

                        <svg
                            class="h-7 w-7"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 004.5 4.5h15a2.25 2.25 0 012.25 2.25m0 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615A2.25 2.25 0 012.25 6.993V6.75"
                            />

                        </svg>

                    </div>


                    {{-- =================================================
                    HEADING
                    ================================================== --}}

                    <div class="mb-6">

                        <p
                            class="
                                text-sm
                                font-medium
                                text-blue-600
                            "
                        >
                            Verifikasi akun
                        </p>


                        <h2
                            class="
                                mt-1
                                text-2xl
                                font-bold
                                tracking-tight
                                text-slate-800
                            "
                        >
                            Verifikasi email Anda
                        </h2>


                        <p
                            class="
                                mt-2
                                text-sm
                                leading-6
                                text-slate-500
                            "
                        >
                            Terima kasih telah mendaftar.
                            Sebelum melanjutkan, silakan
                            verifikasi alamat email Anda
                            melalui tautan yang telah kami
                            kirimkan.
                        </p>

                    </div>


                    {{-- =================================================
                    SUCCESS MESSAGE
                    ================================================== --}}

                    @if (session('status') == 'verification-link-sent')

                        <div
                            class="
                                mb-5
                                rounded-xl
                                border
                                border-green-200
                                bg-green-50
                                px-4
                                py-3
                                text-sm
                                text-green-700
                            "
                        >

                            <div class="flex gap-2">

                                <svg
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />

                                </svg>

                                <span>
                                    Tautan verifikasi baru telah
                                    dikirim ke alamat email Anda.
                                </span>

                            </div>

                        </div>

                    @endif


                    {{-- =================================================
                    ACTIONS
                    ================================================== --}}

                    <div class="space-y-3">


                        {{-- Kirim ulang --}}

                        <form
                            method="POST"
                            action="{{ route('verification.send') }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
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
                                "
                            >

                                Kirim Ulang Email Verifikasi

                                <svg
                                    class="ml-2 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 12l18-9-9 18-2.5-6.5L3 12z"
                                    />

                                </svg>

                            </button>

                        </form>


                        {{-- Logout --}}

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    flex
                                    w-full
                                    items-center
                                    justify-center
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-white
                                    px-4
                                    py-3
                                    text-sm
                                    font-medium
                                    text-slate-600
                                    transition
                                    hover:border-slate-300
                                    hover:bg-slate-50
                                    hover:text-slate-800
                                    focus:outline-none
                                    focus:ring-2
                                    focus:ring-blue-500
                                    focus:ring-offset-2
                                "
                            >

                                Keluar dari Akun

                                <svg
                                    class="ml-2 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 15l3-3m0 0l-3-3m3 3H3"
                                    />

                                </svg>

                            </button>

                        </form>

                    </div>


                    {{-- =================================================
                    INFO
                    ================================================== --}}

                    <div
                        class="
                            mt-6
                            rounded-xl
                            border
                            border-blue-100
                            bg-blue-50/70
                            px-4
                            py-3
                            text-xs
                            leading-5
                            text-blue-700
                        "
                    >

                        <div class="flex gap-2">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.68v2.34m0 3h.008v.008h-.008v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />

                            </svg>

                            <span>
                                Jika email belum terlihat, periksa
                                folder spam atau junk pada kotak masuk
                                Anda.
                            </span>

                        </div>

                    </div>


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
                        "
                    >

                        &copy; {{ date('Y') }} PMUB.
                        Semua hak dilindungi.

                    </p>

                </div>

            </div>

        </div>

    </div>

</x-guest-layout>
