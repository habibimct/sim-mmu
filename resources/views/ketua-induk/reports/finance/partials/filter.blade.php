{{-- ==========================================================
FILTER LAPORAN
=========================================================== --}}

<div class="rounded-lg bg-white p-5 shadow-sm">

    <div class="mb-4">
        <h3 class="text-base font-semibold text-gray-800">
            Filter Laporan
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Tentukan periode, unit, dan kategori laporan keuangan.
        </p>
    </div>


    <form method="GET" action="{{ route('ketua-induk.reports.finance.index') }}">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">


            {{-- ======================================================
            Tanggal Mulai
            ======================================================= --}}

            <div>
                <label for="date_from" class="mb-1 block text-sm font-medium text-gray-700">
                    Tanggal Mulai
                </label>

                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}"
                    class="block w-full rounded-md border-gray-300
                           shadow-sm focus:border-indigo-500
                           focus:ring-indigo-500 sm:text-sm">
            </div>


            {{-- ======================================================
            Tanggal Akhir
            ======================================================= --}}

            <div>
                <label for="date_to" class="mb-1 block text-sm font-medium text-gray-700">
                    Tanggal Akhir
                </label>

                <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}"
                    class="block w-full rounded-md border-gray-300
                           shadow-sm focus:border-indigo-500
                           focus:ring-indigo-500 sm:text-sm">
            </div>


            {{-- ======================================================
            Unit
            ======================================================= --}}

            <div>
                <label for="organization_id" class="mb-1 block text-sm font-medium text-gray-700">
                    Unit
                </label>

                <select id="organization_id" name="organization_id"
                    class="block w-full rounded-md border-gray-300
                           shadow-sm focus:border-indigo-500
                           focus:ring-indigo-500 sm:text-sm">

                    <option value="all" @selected($organizationId === 'all')>
                        Semua Unit
                    </option>

                    @foreach ($organizations as $organization)
                        <option value="{{ $organization->id }}" @selected((string) $organizationId === (string) $organization->id)>
                            {{ $organization->type === 'induk' ? 'PMUB' : $organization->name }}
                        </option>
                    @endforeach

                </select>
            </div>


            {{-- ======================================================
            Kategori Laporan
            ======================================================= --}}

            <div>
                <label for="report_type" class="mb-1 block text-sm font-medium text-gray-700">
                    Kategori
                </label>

                <select id="report_type" name="report_type"
                    class="block w-full rounded-md border-gray-300
                           shadow-sm focus:border-indigo-500
                           focus:ring-indigo-500 sm:text-sm">

                    <option value="all" @selected($reportType === 'all')>
                        Semua
                    </option>

                    <option value="income" @selected($reportType === 'income')>
                        Pemasukan
                    </option>

                    <option value="expense" @selected($reportType === 'expense')>
                        Pengeluaran
                    </option>

                    <option value="deposit" @selected($reportType === 'deposit')>
                        Setoran
                    </option>

                </select>
            </div>

        </div>


        {{-- ==========================================================
        Tombol
        =========================================================== --}}

        <div class="mt-5 flex flex-wrap items-center gap-2">

            <button type="submit"
                class="inline-flex items-center rounded-md
                       bg-indigo-600 px-4 py-2 text-sm font-medium
                       text-white shadow-sm hover:bg-indigo-700
                       focus:outline-none focus:ring-2
                       focus:ring-indigo-500 focus:ring-offset-2">

                Tampilkan Laporan

            </button>


            <a href="{{ route('ketua-induk.reports.finance.pdf', request()->query()) }}" target="_blank"
                class="inline-flex items-center rounded-md
                       bg-gray-700 px-4 py-2 text-sm font-medium
                       text-white shadow-sm hover:bg-gray-800
                       focus:outline-none focus:ring-2
                       focus:ring-gray-500 focus:ring-offset-2">

                Cetak PDF

            </a>

        </div>

    </form>

</div>
