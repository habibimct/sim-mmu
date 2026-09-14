<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

    <form method="GET"
        action="{{ route('kepala-unit.reports.finance.index') }}"
        class="space-y-5">

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            {{-- Dari Tanggal --}}
            <div>
                <label for="date_from"
                    class="mb-1 block text-sm font-medium text-gray-700">
                    Dari Tanggal
                </label>

                <input
                    type="date"
                    id="date_from"
                    name="date_from"
                    value="{{ $dateFrom }}"
                    class="block w-full rounded-md border-gray-300
                           text-sm shadow-sm
                           focus:border-indigo-500
                           focus:ring-indigo-500">
            </div>

            {{-- Sampai Tanggal --}}
            <div>
                <label for="date_to"
                    class="mb-1 block text-sm font-medium text-gray-700">
                    Sampai Tanggal
                </label>

                <input
                    type="date"
                    id="date_to"
                    name="date_to"
                    value="{{ $dateTo }}"
                    class="block w-full rounded-md border-gray-300
                           text-sm shadow-sm
                           focus:border-indigo-500
                           focus:ring-indigo-500">
            </div>

            {{-- Kategori --}}
            <div>
                <label for="report_type"
                    class="mb-1 block text-sm font-medium text-gray-700">
                    Kategori
                </label>

                <select
                    id="report_type"
                    name="report_type"
                    class="block w-full rounded-md border-gray-300
                           text-sm shadow-sm
                           focus:border-indigo-500
                           focus:ring-indigo-500">

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

        {{-- Tombol --}}
        <div class="flex flex-wrap items-center gap-3">

            <button
                type="submit"
                class="inline-flex items-center rounded-md
                       bg-indigo-600 px-4 py-2
                       text-sm font-medium text-white
                       shadow-sm
                       hover:bg-indigo-700
                       focus:outline-none
                       focus:ring-2
                       focus:ring-indigo-500
                       focus:ring-offset-2">

                <i class="bi bi-search mr-2"></i>

                Tampilkan Laporan

            </button>

            <a
                href="{{ route(
                    'kepala-unit.reports.finance.pdf',
                    request()->query()
                ) }}"
                target="_blank"
                class="inline-flex items-center rounded-md
                       bg-red-600 px-4 py-2
                       text-sm font-medium text-white
                       shadow-sm
                       hover:bg-red-700
                       focus:outline-none
                       focus:ring-2
                       focus:ring-red-500
                       focus:ring-offset-2">

                <i class="bi bi-file-earmark-pdf mr-2"></i>

                Cetak PDF

            </a>

        </div>

    </form>

</div>
