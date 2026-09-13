{{-- ==========================================================
RINGKASAN LAPORAN
=========================================================== --}}

<div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">


    {{-- ======================================================
    PEMASUKAN
    ======================================================= --}}

    <div class="rounded-lg bg-white p-5 shadow-sm">

        <div class="text-sm font-medium text-gray-500">
            Total Pemasukan
        </div>

        <div class="mt-2 text-2xl font-bold text-green-600">
            Rp {{ number_format($totalIncome, 0, ',', '.') }}
        </div>

        <div class="mt-1 text-xs text-gray-400">
            Transaksi pemasukan
        </div>

    </div>


    {{-- ======================================================
    PENGELUARAN
    ======================================================= --}}

    <div class="rounded-lg bg-white p-5 shadow-sm">

        <div class="text-sm font-medium text-gray-500">
            Total Pengeluaran
        </div>

        <div class="mt-2 text-2xl font-bold text-red-600">
            Rp {{ number_format($totalExpense, 0, ',', '.') }}
        </div>

        <div class="mt-1 text-xs text-gray-400">
            Transaksi pengeluaran
        </div>

    </div>

{{-- ======================================================
SETORAN
======================================================= --}}

<div class="rounded-lg bg-white p-5 shadow-sm">

    <div class="text-sm font-medium text-gray-500">
        Total Setoran
    </div>

    <div class="mt-2 text-2xl font-bold text-blue-600">
        Rp {{ number_format($totalDeposit, 0, ',', '.') }}
    </div>

    <div class="mt-1 text-xs text-gray-400">
        Transfer Unit ↔ PMUB
    </div>

</div>


    {{-- ======================================================
    SALDO
    ======================================================= --}}

    <div class="rounded-lg bg-white p-5 shadow-sm">

        <div class="text-sm font-medium text-gray-500">
            Saldo
        </div>

        <div class="mt-2 text-2xl font-bold
            {{ $netBalance >= 0 ? 'text-gray-800' : 'text-red-600' }}">

            Rp {{ number_format($netBalance, 0, ',', '.') }}

        </div>

        <div class="mt-1 text-xs text-gray-400">
            Pemasukan − Pengeluaran
        </div>

    </div>

</div>
