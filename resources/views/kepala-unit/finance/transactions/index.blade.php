@extends('layouts.kepala-unit')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- =========================================================
        HEADER
        ========================================================= --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            {{-- Kiri --}}
            <div>

                <h1 class="text-2xl font-bold text-gray-800">
                    Transaksi Keuangan
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Monitoring transaksi keuangan unit.
                </p>

            </div>


            {{-- Kanan --}}
            <div class="flex items-center gap-2">

                {{-- Status --}}
                <div
                    class="inline-flex items-center gap-2
                   px-3 py-2
                   rounded-lg
                   bg-blue-50
                   text-blue-700
                   text-sm font-medium
                   whitespace-nowrap">

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12h6m-6 4h4m2-13H9a2 2 0 0 0-2 2v14l5-3 5 3V5a2 2 0 0 0-2-2Z" />

                    </svg>

                    Monitoring

                </div>


                {{-- Filter --}}
                @include('kepala-unit.finance.transactions.partials.filter')

            </div>

        </div>


        {{-- =========================================================
        RINGKASAN
        ========================================================== --}}

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Pemasukan --}}
            <div
                class="bg-white rounded-2xl
                        border border-gray-100
                        shadow-sm p-5">

                <p class="text-sm text-gray-500">
                    Total Pemasukan
                </p>

                <p class="mt-2 text-xl font-bold text-emerald-600">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </p>

            </div>


            {{-- Pengeluaran --}}
            <div
                class="bg-white rounded-2xl
                        border border-gray-100
                        shadow-sm p-5">

                <p class="text-sm text-gray-500">
                    Total Pengeluaran
                </p>

                <p class="mt-2 text-xl font-bold text-red-600">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </p>

            </div>


            {{-- Bersih --}}
            <div
                class="bg-white rounded-2xl
                        border border-gray-100
                        shadow-sm p-5">

                <p class="text-sm text-gray-500">
                    Selisih Bersih
                </p>

                <p
                    class="mt-2 text-xl font-bold
                    {{ $netAmount >= 0 ? 'text-blue-600' : 'text-red-600' }}">

                    Rp {{ number_format($netAmount, 0, ',', '.') }}

                </p>

            </div>

        </div>


        {{-- =========================================================
        FILTER
        ========================================================== --}}




        {{-- =========================================================
        TABEL TRANSAKSI
        ========================================================== --}}

        @include('kepala-unit.finance.transactions.partials.table')

    </div>
@endsection
