<div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200 sm:p-6">

    <form method="GET"
        action="{{ route('kepala-unit.reports.finance.index') }}"
        class="space-y-6">

        {{-- Filter --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            {{-- Dari Tanggal --}}
            <div>
                <label for="date_from"
                    class="mb-2 block text-sm font-semibold text-gray-700">
                    Dari Tanggal
                </label>

                <div class="relative">
                    <input
                        type="date"
                        id="date_from"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        class="block w-full rounded-lg border border-gray-300
                               bg-gray-50 px-3 py-2.5
                               text-sm text-gray-900
                               shadow-sm
                               transition
                               focus:border-indigo-500
                               focus:bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-500/20">
                </div>
            </div>

            {{-- Sampai Tanggal --}}
            <div>
                <label for="date_to"
                    class="mb-2 block text-sm font-semibold text-gray-700">
                    Sampai Tanggal
                </label>

                <div class="relative">
                    <input
                        type="date"
                        id="date_to"
                        name="date_to"
                        value="{{ $dateTo }}"
                        class="block w-full rounded-lg border border-gray-300
                               bg-gray-50 px-3 py-2.5
                               text-sm text-gray-900
                               shadow-sm
                               transition
                               focus:border-indigo-500
                               focus:bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-500/20">
                </div>
            </div>

            {{-- Kategori --}}
            <div>
                <label for="report_type"
                    class="mb-2 block text-sm font-semibold text-gray-700">
                    Kategori
                </label>

                <select
                    id="report_type"
                    name="report_type"
                    class="block w-full rounded-lg border border-gray-300
                           bg-gray-50 px-3 py-2.5
                           text-sm text-gray-900
                           shadow-sm
                           transition
                           focus:border-indigo-500
                           focus:bg-white
                           focus:outline-none
                           focus:ring-2
                           focus:ring-indigo-500/20">

                    <option value="all"
                        @selected($reportType === 'all')>
                        Semua
                    </option>

                    <option value="income"
                        @selected($reportType === 'income')>
                        Pemasukan
                    </option>

                    <option value="expense"
                        @selected($reportType === 'expense')>
                        Pengeluaran
                    </option>

                    <option value="deposit"
                        @selected($reportType === 'deposit')>
                        Setoran
                    </option>

                </select>
            </div>

        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-100"></div>

        {{-- Tombol --}}
        <div class="flex flex-wrap items-center gap-3">

            {{-- Tampilkan Laporan --}}
            <button
                type="submit"
                class="inline-flex items-center justify-center
                       rounded-lg
                       bg-indigo-600
                       px-4 py-2.5
                       text-sm font-semibold
                       text-white
                       shadow-sm
                       transition
                       hover:bg-indigo-700
                       focus:outline-none
                       focus:ring-2
                       focus:ring-indigo-500
                       focus:ring-offset-2
                       active:bg-indigo-800">

                <i class="bi bi-search mr-2"></i>

                Tampilkan Laporan

            </button>

            {{-- Cetak PDF --}}
            <a
                href="{{ route(
                    'kepala-unit.reports.finance.pdf',
                    request()->query()
                ) }}"
                target="_blank"
                class="inline-flex items-center justify-center
                       rounded-lg
                       bg-red-600
                       px-4 py-2.5
                       text-sm font-semibold
                       text-white
                       shadow-sm
                       transition
                       hover:bg-red-700
                       focus:outline-none
                       focus:ring-2
                       focus:ring-red-500
                       focus:ring-offset-2
                       active:bg-red-800">

                <i class="bi bi-file-earmark-pdf mr-2"></i>

                Cetak PDF

            </a>

        </div>

    </form>

</div>
