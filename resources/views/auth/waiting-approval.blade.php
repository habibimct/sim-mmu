<x-guest-layout>

    {{-- =================================================
        BACKGROUND
    ================================================= --}}
    <div class="relative min-h-screen overflow-hidden bg-slate-900">

        {{-- Background dekorasi --}}
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-950 to-slate-800"></div>

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-slate-950/30"></div>

        {{-- Dekorasi lingkaran --}}
        <div class="absolute -right-32 -bottom-32 h-96 w-96 rounded-full border border-white/10"></div>
        <div class="absolute -right-20 -bottom-20 h-72 w-72 rounded-full border border-white/5"></div>

        {{-- Titik dekorasi --}}
        <div class="absolute bottom-12 right-12 hidden opacity-30 sm:block">
            <div class="grid grid-cols-5 gap-3">
                @for ($i = 0; $i < 25; $i++)
                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                @endfor
            </div>
        </div>


        {{-- =================================================
            CONTENT
        ================================================= --}}
        <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">

            <div class="w-full max-w-lg">

                {{-- =================================================
                    CARD
                ================================================= --}}
                <div class="overflow-hidden rounded-3xl bg-white shadow-2xl shadow-black/30">

                    <div class="px-6 py-8 sm:px-9 sm:py-10">


                        {{-- =================================================
                            ICON
                        ================================================= --}}
                        <div class="flex justify-center">

                            <div class="relative flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-50 ring-8 ring-blue-50/50">

                                <svg
                                    class="h-10 w-10 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="9"
                                        stroke-width="1.8"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M12 7v5l3 2"
                                    />
                                </svg>

                            </div>

                        </div>


                        {{-- =================================================
                            TITLE
                        ================================================= --}}
                        <div class="mt-7 text-center">

                            <h1 class="text-2xl font-bold tracking-tight text-slate-800 sm:text-3xl">
                                Akun Berhasil Dibuat
                            </h1>

                            <p class="mt-2 text-sm font-semibold text-blue-600 sm:text-base">
                                Menunggu Konfirmasi Administrator
                            </p>

                            <div class="mx-auto mt-4 h-1 w-12 rounded-full bg-blue-600"></div>

                        </div>


                        {{-- =================================================
                            INFORMATION
                        ================================================= --}}
                        <div class="mt-7 rounded-2xl border border-blue-100 bg-blue-50/70 p-5">

                            <div class="flex items-start gap-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100">

                                    <svg
                                        class="h-5 w-5 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="9"
                                            stroke-width="1.8"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-width="1.8"
                                            d="M12 11v5"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-width="2"
                                            d="M12 8h.01"
                                        />
                                    </svg>

                                </div>


                                <div>

                                    <h3 class="text-sm font-bold text-slate-800">
                                        Akun Anda sedang diproses
                                    </h3>

                                    <p class="mt-1.5 text-sm leading-6 text-slate-600">
                                        Pendaftaran akun Anda telah berhasil.
                                        Namun, akun belum dapat digunakan untuk
                                        mengakses sistem sampai administrator
                                        melakukan konfirmasi.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            STATUS PENDAFTARAN
                        ================================================= --}}
                        <div class="mt-5 rounded-2xl border border-slate-100 bg-slate-50 p-5">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status Pendaftaran
                            </p>


                            <div class="mt-5">


                                {{-- STEP 1 --}}
                                <div class="flex items-center gap-4">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100">

                                        <svg
                                            class="h-5 w-5 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>

                                    </div>


                                    <div class="flex-1">

                                        <p class="text-sm font-semibold text-slate-800">
                                            Pendaftaran akun
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            Berhasil
                                        </p>

                                    </div>


                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        Selesai
                                    </span>

                                </div>


                                {{-- CONNECTOR --}}
                                <div class="ml-[17px] h-6 border-l border-dashed border-slate-300"></div>


                                {{-- STEP 2 --}}
                                <div class="flex items-center gap-4">

                                    <div class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100">

                                        <span class="h-3 w-3 animate-pulse rounded-full bg-blue-600"></span>

                                    </div>


                                    <div class="flex-1">

                                        <p class="text-sm font-semibold text-slate-800">
                                            Konfirmasi administrator
                                        </p>

                                        <p class="mt-0.5 text-xs text-blue-600">
                                            Sedang menunggu
                                        </p>

                                    </div>


                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Diproses
                                    </span>

                                </div>


                                {{-- CONNECTOR --}}
                                <div class="ml-[17px] h-6 border-l border-dashed border-slate-300"></div>


                                {{-- STEP 3 --}}
                                <div class="flex items-center gap-4">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100">

                                        <svg
                                            class="h-5 w-5 text-slate-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.8"
                                                d="M12 15v2m-4 4h8a2 2 0 002-2v-7a2 2 0 00-2-2H8a2 2 0 00-2 2v7a2 2 0 002 2zm8-9V7a4 4 0 00-8 0v3"
                                            />
                                        </svg>

                                    </div>


                                    <div class="flex-1">

                                        <p class="text-sm font-semibold text-slate-500">
                                            Akses sistem
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            Menunggu konfirmasi
                                        </p>

                                    </div>


                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                        Belum aktif
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            LOGOUT
                        ================================================= --}}
                        <div class="mt-7">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button
                                    type="submit"
                                    class="group flex w-full items-center justify-center gap-2.5
                                           rounded-xl bg-slate-800 px-4 py-3.5
                                           text-sm font-semibold text-white
                                           shadow-lg shadow-slate-900/20
                                           transition duration-200
                                           hover:bg-slate-900
                                           hover:shadow-xl
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-slate-400
                                           focus:ring-offset-2"
                                >

                                    <svg
                                        class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-0.5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M15 17l5-5m0 0l-5-5m5 5H9"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-width="1.8"
                                            d="M5 5v14"
                                        />
                                    </svg>

                                    Logout

                                </button>

                            </form>

                        </div>


                        {{-- =================================================
                            FOOTER NOTE
                        ================================================= --}}
                        <div class="mt-6 flex items-start justify-center gap-2 text-center">

                            <svg
                                class="mt-0.5 h-4 w-4 shrink-0 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke-width="1.8"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-width="1.8"
                                    d="M12 11v5"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-width="2"
                                    d="M12 8h.01"
                                />
                            </svg>

                            <p class="text-xs leading-5 text-slate-400">
                                Setelah akun dikonfirmasi oleh administrator,
                                Anda dapat login kembali menggunakan akun
                                yang telah didaftarkan.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    OUTSIDE CARD
                ================================================= --}}
                <p class="mt-6 text-center text-xs text-white/50">
                    Sistem Informasi Manajemen PMUB
                </p>

            </div>

        </div>

    </div>

</x-guest-layout>
