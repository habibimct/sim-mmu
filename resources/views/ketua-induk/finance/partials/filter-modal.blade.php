{{-- ==========================================================
MODAL FILTER TRANSAKSI
=========================================================== --}}

<div
    x-data="{ open: false }"
    x-on:open-finance-filter.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
>

    {{-- Overlay --}}

    <div
        class="fixed inset-0 bg-black/40"
        x-on:click="open = false"
    ></div>


    {{-- Modal --}}

    <div
        class="relative flex min-h-screen
               items-center justify-center p-4"
    >

        <div
            class="relative w-full max-w-2xl
                   rounded-xl bg-white shadow-xl"
            x-on:click.stop
        >

            {{-- Header --}}

            <div
                class="flex items-center justify-between
                       border-b border-gray-200 px-6 py-4"
            >

                <div>

                    <h3 class="text-lg font-semibold text-gray-800">
                        Filter Transaksi
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Tentukan kriteria transaksi yang ingin ditampilkan.
                    </p>

                </div>


                <button
                    type="button"
                    x-on:click="open = false"
                    class="rounded-md p-2
                           text-gray-400
                           hover:bg-gray-100
                           hover:text-gray-600"
                >
                    ✕
                </button>

            </div>


            {{-- Form --}}

            <form
                method="GET"
                action="{{ route('ketua-induk.finance.index') }}"
            >

                <div class="space-y-5 px-6 py-5">

                    {{-- Tahun + Bulan --}}

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        {{-- Tahun --}}

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Tahun
                            </label>

                            <select
                                name="year"
                                class="w-full rounded-md
                                       border-gray-300
                                       shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                                <option value="">
                                    Semua Tahun
                                </option>

                                @foreach ($availableYears as $availableYear)

                                    <option
                                        value="{{ $availableYear }}"
                                        @selected(
                                            (int) request('year')
                                            === (int) $availableYear
                                        )
                                    >
                                        {{ $availableYear }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Bulan --}}

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Bulan
                            </label>

                            <select
                                name="month"
                                class="w-full rounded-md
                                       border-gray-300
                                       shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                                <option value="">
                                    Semua Bulan
                                </option>

                                @php

                                    $months = [
                                        1 => 'Januari',
                                        2 => 'Februari',
                                        3 => 'Maret',
                                        4 => 'April',
                                        5 => 'Mei',
                                        6 => 'Juni',
                                        7 => 'Juli',
                                        8 => 'Agustus',
                                        9 => 'September',
                                        10 => 'Oktober',
                                        11 => 'November',
                                        12 => 'Desember',
                                    ];

                                @endphp

                                @foreach ($months as $number => $name)

                                    <option
                                        value="{{ $number }}"
                                        @selected(
                                            (int) request('month')
                                            === $number
                                        )
                                    >
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- Periode --}}

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Dari Tanggal
                            </label>

                            <input
                                type="date"
                                name="date_from"
                                value="{{ request('date_from') }}"
                                class="w-full rounded-md
                                       border-gray-300
                                       shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                        </div>


                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Sampai Tanggal
                            </label>

                            <input
                                type="date"
                                name="date_to"
                                value="{{ request('date_to') }}"
                                class="w-full rounded-md
                                       border-gray-300
                                       shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                        </div>

                    </div>


                    {{-- Organisasi --}}

                    <div>

                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Organisasi
                        </label>

                        <select
                            name="organization_id"
                            class="w-full rounded-md
                                   border-gray-300
                                   shadow-sm
                                   focus:border-indigo-500
                                   focus:ring-indigo-500"
                        >

                            <option value="">
                                Semua Organisasi
                            </option>

                            @foreach ($organizations as $organization)

                                <option
                                    value="{{ $organization->id }}"
                                    @selected(
                                        (int) request('organization_id')
                                        === $organization->id
                                    )
                                >
                                    {{ $organization->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Jenis + Status --}}

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Jenis Transaksi
                            </label>

                            <select
                                name="type"
                                class="w-full rounded-md
                                       border-gray-300
                                       shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                                <option value="">
                                    Semua Jenis
                                </option>

                                <option
                                    value="income"
                                    @selected(request('type') === 'income')
                                >
                                    Pemasukan
                                </option>

                                <option
                                    value="expense"
                                    @selected(request('type') === 'expense')
                                >
                                    Pengeluaran
                                </option>

                            </select>

                        </div>


                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Status
                            </label>

                            <select
                                name="status"
                                class="w-full rounded-md
                                       border-gray-300
                                       shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            >

                                <option value="">
                                    Semua Status
                                </option>

                                <option
                                    value="pending"
                                    @selected(request('status') === 'pending')
                                >
                                    Menunggu
                                </option>

                                <option
                                    value="confirmed"
                                    @selected(request('status') === 'confirmed')
                                >
                                    Dikonfirmasi
                                </option>

                                <option
                                    value="rejected"
                                    @selected(request('status') === 'rejected')
                                >
                                    Ditolak
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- Metode pembayaran --}}

                    <div>

                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Metode Pembayaran
                        </label>

                        <select
                            name="payment_method"
                            class="w-full rounded-md
                                   border-gray-300
                                   shadow-sm
                                   focus:border-indigo-500
                                   focus:ring-indigo-500"
                        >

                            <option value="">
                                Semua Metode
                            </option>

                            <option
                                value="cash"
                                @selected(request('payment_method') === 'cash')
                            >
                                Tunai / Offline
                            </option>

                            <option
                                value="bank_transfer"
                                @selected(
                                    request('payment_method')
                                    === 'bank_transfer'
                                )
                            >
                                Transfer Bank
                            </option>

                            <option
                                value="online"
                                @selected(request('payment_method') === 'online')
                            >
                                Online
                            </option>

                        </select>

                    </div>


                    {{-- Pencarian --}}

                    <div>

                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                        >
                            Pencarian
                        </label>

                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Kategori atau keterangan..."
                            class="w-full rounded-md
                                   border-gray-300
                                   shadow-sm
                                   focus:border-indigo-500
                                   focus:ring-indigo-500"
                        >

                    </div>

                </div>


                {{-- Footer --}}

                <div
                    class="flex items-center justify-between
                           border-t border-gray-200
                           px-6 py-4"
                >

                    <a
                        href="{{ route('ketua-induk.finance.index') }}"
                        class="rounded-md bg-gray-100
                               px-4 py-2
                               text-sm font-medium
                               text-gray-700
                               hover:bg-gray-200"
                    >
                        Reset
                    </a>


                    <div class="flex gap-2">

                        <button
                            type="button"
                            x-on:click="open = false"
                            class="rounded-md bg-gray-100
                                   px-4 py-2
                                   text-sm font-medium
                                   text-gray-700
                                   hover:bg-gray-200"
                        >
                            Batal
                        </button>


                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600
                                   px-4 py-2
                                   text-sm font-medium
                                   text-white
                                   hover:bg-indigo-700"
                        >
                            Terapkan Filter
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>
