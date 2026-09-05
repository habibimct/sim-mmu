<div
    x-data="{ openFilter: false }"
>

    {{-- =========================================================
    HEADER FILTER
    ========================================================== --}}

    <div class="flex justify-end">

        <button
            type="button"
            @click="openFilter = true"
            class="inline-flex items-center
                   justify-center
                   gap-2
                   px-3.5 py-2
                   rounded-lg
                   bg-blue-600
                   text-white
                   text-sm font-medium
                   shadow-sm
                   hover:bg-blue-700
                   transition"
        >

            <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M3 5h18M6 12h12m-9 7h6"
                />

            </svg>

            Filter

            {{-- Indikator filter aktif --}}
            @if (
                request()->hasAny([
                    'date_from',
                    'date_to',
                    'type',
                    'payment_method',
                    'category',
                    'search'
                ])
            )

                <span
                    class="w-2 h-2
                           rounded-full
                           bg-red-300"
                ></span>

            @endif

        </button>

    </div>


    {{-- =========================================================
    MODAL FILTER
    ========================================================== --}}

    <div
        x-show="openFilter"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >

        {{-- Overlay --}}
        <div
            class="fixed inset-0
                   bg-black/50
                   backdrop-blur-sm"
            @click="openFilter = false"
        ></div>


        {{-- Posisi modal --}}
        <div
            class="relative min-h-screen
                   flex items-center justify-center
                   p-4"
        >

            {{-- Modal --}}
            <div
                x-show="openFilter"
                x-transition
                @click.stop
                class="relative w-full max-w-2xl
                       bg-white
                       rounded-2xl
                       shadow-2xl
                       overflow-hidden"
            >

                {{-- Header --}}
                <div
                    class="px-6 py-4
                           border-b border-gray-100
                           flex items-center justify-between"
                >

                    <div>

                        <h2
                            class="text-lg font-bold text-gray-800"
                        >
                            Filter Transaksi
                        </h2>

                        <p
                            class="mt-1 text-xs text-gray-500"
                        >
                            Tentukan kriteria transaksi yang ingin ditampilkan.
                        </p>

                    </div>


                    <button
                        type="button"
                        @click="openFilter = false"
                        class="w-9 h-9
                               rounded-lg
                               text-gray-400
                               hover:bg-gray-100
                               hover:text-gray-600
                               flex items-center
                               justify-center"
                    >

                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M6 6l12 12M18 6 6 18"
                            />

                        </svg>

                    </button>

                </div>


                {{-- =================================================
                FORM
                ================================================== --}}

                <form
                    method="GET"
                    action="{{ route('kepala-unit.finance.transactions.index') }}"
                >

                    <div class="p-6 space-y-5">

                        {{-- Tanggal --}}
                        <div
                            class="grid grid-cols-1
                                   sm:grid-cols-2
                                   gap-4"
                        >

                            <div>

                                <label
                                    class="block mb-1.5
                                           text-xs font-semibold
                                           text-gray-600"
                                >
                                    Dari Tanggal
                                </label>

                                <input
                                    type="date"
                                    name="date_from"
                                    value="{{ $dateFrom }}"
                                    class="w-full
                                           rounded-xl
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500"
                                >

                            </div>


                            <div>

                                <label
                                    class="block mb-1.5
                                           text-xs font-semibold
                                           text-gray-600"
                                >
                                    Sampai Tanggal
                                </label>

                                <input
                                    type="date"
                                    name="date_to"
                                    value="{{ $dateTo }}"
                                    class="w-full
                                           rounded-xl
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500"
                                >

                            </div>

                        </div>


                        {{-- Jenis + Metode --}}
                        <div
                            class="grid grid-cols-1
                                   sm:grid-cols-2
                                   gap-4"
                        >

                            <div>

                                <label
                                    class="block mb-1.5
                                           text-xs font-semibold
                                           text-gray-600"
                                >
                                    Jenis Transaksi
                                </label>

                                <select
                                    name="type"
                                    class="w-full
                                           rounded-xl
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500"
                                >

                                    <option value="">
                                        Semua Jenis
                                    </option>

                                    <option
                                        value="income"
                                        @selected($type === 'income')
                                    >
                                        Pemasukan
                                    </option>

                                    <option
                                        value="expense"
                                        @selected($type === 'expense')
                                    >
                                        Pengeluaran
                                    </option>

                                </select>

                            </div>


                            <div>

                                <label
                                    class="block mb-1.5
                                           text-xs font-semibold
                                           text-gray-600"
                                >
                                    Metode Pembayaran
                                </label>

                                <select
                                    name="payment_method"
                                    class="w-full
                                           rounded-xl
                                           border-gray-300
                                           text-sm
                                           focus:border-blue-500
                                           focus:ring-blue-500"
                                >

                                    <option value="">
                                        Semua Metode
                                    </option>

                                    <option
                                        value="cash"
                                        @selected($paymentMethod === 'cash')
                                    >
                                        Tunai
                                    </option>

                                    <option
                                        value="bank_transfer"
                                        @selected($paymentMethod === 'bank_transfer')
                                    >
                                        Transfer Bank
                                    </option>

                                    <option
                                        value="online"
                                        @selected($paymentMethod === 'online')
                                    >
                                        Online
                                    </option>

                                </select>

                            </div>

                        </div>


                        {{-- Kategori --}}
                        <div>

                            <label
                                class="block mb-1.5
                                       text-xs font-semibold
                                       text-gray-600"
                            >
                                Kategori
                            </label>

                            <select
                                name="category"
                                class="w-full
                                       rounded-xl
                                       border-gray-300
                                       text-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                                <option value="">
                                    Semua Kategori
                                </option>

                                @foreach ($categories as $item)

                                    <option
                                        value="{{ $item }}"
                                        @selected($category === $item)
                                    >
                                        {{ $item }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Pencarian --}}
                        <div>

                            <label
                                class="block mb-1.5
                                       text-xs font-semibold
                                       text-gray-600"
                            >
                                Pencarian
                            </label>

                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari kategori atau keterangan..."
                                class="w-full
                                       rounded-xl
                                       border-gray-300
                                       text-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div
                        class="px-6 py-4
                               bg-gray-50
                               border-t border-gray-100
                               flex justify-end gap-2"
                    >

                        <a
                            href="{{ route('kepala-unit.finance.transactions.index') }}"
                            class="px-4 py-2.5
                                   rounded-xl
                                   bg-white
                                   border border-gray-300
                                   text-gray-700
                                   text-sm font-medium
                                   hover:bg-gray-100"
                        >
                            Reset
                        </a>


                        <button
                            type="button"
                            @click="openFilter = false"
                            class="px-4 py-2.5
                                   rounded-xl
                                   bg-gray-200
                                   text-gray-700
                                   text-sm font-medium
                                   hover:bg-gray-300"
                        >
                            Batal
                        </button>


                        <button
                            type="submit"
                            class="px-4 py-2.5
                                   rounded-xl
                                   bg-blue-600
                                   text-white
                                   text-sm font-medium
                                   hover:bg-blue-700"
                        >
                            Terapkan Filter
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>
