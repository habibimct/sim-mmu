@extends('layouts.ketua-induk')


@section('content')
    <div class="flex items-center justify-between">

        <div>

            <h2 class="text-xl font-semibold text-gray-800">
                Ringkasan Keuangan
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                {{ $induk->name }}
            </p>

        </div>


        <a href="{{ route('ketua-induk.dashboard') }}"
            class="rounded-md bg-gray-100 px-4 py-2 text-sm
                       font-medium text-gray-700 hover:bg-gray-200">
            ← Dashboard
        </a>

    </div>


    <div class="py-6">

        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">


            {{-- ==========================================================
            RINGKASAN
            =========================================================== --}}

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">


                {{-- Pemasukan --}}

                <div class="rounded-lg bg-white p-5 shadow-sm">

                    <div class="text-sm font-medium text-gray-500">
                        Total Pemasukan
                    </div>

                    <div class="mt-2 text-2xl font-bold text-green-600">

                        Rp
                        {{ number_format($totalIncome, 0, ',', '.') }}

                    </div>

                </div>


                {{-- Pengeluaran --}}

                <div class="rounded-lg bg-white p-5 shadow-sm">

                    <div class="text-sm font-medium text-gray-500">
                        Total Pengeluaran
                    </div>

                    <div class="mt-2 text-2xl font-bold text-red-600">

                        Rp
                        {{ number_format($totalExpense, 0, ',', '.') }}

                    </div>

                </div>


                {{-- Saldo --}}

                <div class="rounded-lg bg-white p-5 shadow-sm">

                    <div class="text-sm font-medium text-gray-500">
                        Saldo
                    </div>

                    <div class="mt-2 text-2xl font-bold text-gray-800">

                        Rp
                        {{ number_format($balance, 0, ',', '.') }}

                    </div>

                </div>

            </div>


            {{-- ==========================================================
            REKAP PER ORGANISASI
            =========================================================== --}}

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">

                <div class="border-b border-gray-200 px-5 py-4">

                    <h3 class="font-semibold text-gray-800">
                        Rekap Keuangan per Organisasi
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Rekap transaksi yang telah dikonfirmasi.
                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    class="px-5 py-3 text-left text-xs
                                           font-medium uppercase text-gray-500">
                                    Organisasi
                                </th>

                                <th
                                    class="px-5 py-3 text-right text-xs
                                           font-medium uppercase text-gray-500">
                                    Pemasukan
                                </th>

                                <th
                                    class="px-5 py-3 text-right text-xs
                                           font-medium uppercase text-gray-500">
                                    Pengeluaran
                                </th>

                                <th
                                    class="px-5 py-3 text-right text-xs
                                           font-medium uppercase text-gray-500">
                                    Saldo
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse ($organizationSummary as $organization)
                                <tr>

                                    <td class="px-5 py-4 text-sm font-medium text-gray-800">

                                        {{ $organization->name }}

                                        @if ($organization->id === $induk->id)
                                            <span
                                                class="ml-2 rounded-full
                                                       bg-indigo-100 px-2 py-1
                                                       text-xs font-medium
                                                       text-indigo-700">
                                                INDUK
                                            </span>
                                        @endif

                                    </td>


                                    <td class="px-5 py-4 text-right text-sm text-green-600">

                                        Rp
                                        {{ number_format($organization->income_total, 0, ',', '.') }}

                                    </td>


                                    <td class="px-5 py-4 text-right text-sm text-red-600">

                                        Rp
                                        {{ number_format($organization->expense_total, 0, ',', '.') }}

                                    </td>


                                    <td class="px-5 py-4 text-right text-sm font-semibold text-gray-800">

                                        Rp
                                        {{ number_format($organization->balance, 0, ',', '.') }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500">
                                        Belum ada data keuangan.
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- ==========================================================
            ARUS KEUANGAN
            =========================================================== --}}

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">

                {{-- Header --}}

                <div class="border-b border-gray-200 px-5 py-4">

                    <div
                        class="flex flex-col gap-1
                   sm:flex-row
                   sm:items-center
                   sm:justify-between">

                        <div>

                            <h3 class="font-semibold text-gray-800">
                                Arus Keuangan
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $chartPeriodLabel }}
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Chart --}}

                <div class="p-5">

                    <div class="relative h-80">

                        <canvas id="financeChart"></canvas>

                    </div>

                </div>

            </div>


            {{-- ==========================================================
            HEADER TRANSAKSI + TOMBOL FILTER
            =========================================================== --}}

            <div class="flex items-center justify-between">

                <div>

                    <h3 class="font-semibold text-gray-800">
                        Transaksi Keuangan
                    </h3>

                    @if (request()->filled('year') ||
                            request()->filled('month') ||
                            request()->filled('date_from') ||
                            request()->filled('date_to') ||
                            request()->filled('organization_id') ||
                            request()->filled('type') ||
                            request()->filled('status') ||
                            request()->filled('payment_method') ||
                            request()->filled('search'))
                        <p class="mt-1 text-sm text-gray-500">
                            Filter sedang diterapkan.
                        </p>
                    @endif

                </div>


                {{-- Tombol popup --}}

                <button type="button" x-data x-on:click="$dispatch('open-finance-filter')"
                    class="inline-flex items-center rounded-md
                           bg-indigo-600 px-4 py-2.5 text-sm
                           font-medium text-white shadow-sm
                           hover:bg-indigo-700">

                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 12v5a1 1 0 01-.553.894l-4 2A1 1 0 019 19v-7L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>

                    Filter Transaksi

                </button>

            </div>


            {{-- ==========================================================
            TABEL TRANSAKSI
            =========================================================== --}}

            @include('ketua-induk.finance.partials.transactions-table')
        </div>

    </div>


    {{-- ==========================================================
MODAL FILTER
========================================================== --}}

    @include('ketua-induk.finance.partials.filter-modal')


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const canvas =
                    document.getElementById(
                        'financeChart'
                    );

                if (!canvas) {
                    return;
                }

                if (typeof Chart === 'undefined') {
                    console.error(
                        'Chart.js belum dimuat.'
                    );

                    return;
                }

                new Chart(
                    canvas.getContext('2d'), {
                        type: 'line',

                        data: {
                            labels: @json($chartLabels),

                            datasets: [{
                                    label: 'Pemasukan',

                                    data: @json($chartIncome),

                                    borderColor: 'rgb(22, 163, 74)',

                                    backgroundColor: 'rgba(22, 163, 74, 0.08)',

                                    borderWidth: 2,

                                    tension: 0.35,

                                    pointRadius: 3,

                                    pointHoverRadius: 5,

                                    fill: false
                                },

                                {
                                    label: 'Pengeluaran',

                                    data: @json($chartExpense),

                                    borderColor: 'rgb(220, 38, 38)',

                                    backgroundColor: 'rgba(220, 38, 38, 0.08)',

                                    borderWidth: 2,

                                    tension: 0.35,

                                    pointRadius: 3,

                                    pointHoverRadius: 5,

                                    fill: false
                                }
                            ]
                        },

                        options: {
                            responsive: true,

                            maintainAspectRatio: false,

                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },

                            plugins: {
                                legend: {
                                    position: 'top'
                                },

                                tooltip: {
                                    callbacks: {
                                        label: function(context) {

                                            const amount =
                                                new Intl.NumberFormat(
                                                    'id-ID'
                                                ).format(
                                                    context.parsed.y
                                                );

                                            return context
                                                .dataset
                                                .label +
                                                ': Rp ' +
                                                amount;
                                        }
                                    }
                                }
                            },

                            scales: {
                                y: {
                                    beginAtZero: true,

                                    ticks: {
                                        callback: function(value) {

                                            return 'Rp ' +
                                                new Intl
                                                .NumberFormat(
                                                    'id-ID'
                                                )
                                                .format(
                                                    value
                                                );
                                        }
                                    }
                                }
                            }
                        }
                    }
                );
            }
        );
    </script>
@endsection
