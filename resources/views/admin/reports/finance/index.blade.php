@extends('adminlte::page')

@section('title', 'Laporan Keuangan')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-1">Laporan Keuangan</h1>
            <p class="text-muted mb-0">
                Rekap transaksi keuangan berdasarkan periode dan organisasi.
            </p>
        </div>
    </div>
@stop

@section('content')

    {{-- Filter --}}
    @include('admin.reports.finance.partials.filter')

    {{-- Ringkasan --}}
    @include('admin.reports.finance.partials.summary')

    {{-- Grafik --}}
    @include('admin.reports.finance.partials.charts')

    {{-- Rekap per Unit --}}
    @include('admin.reports.finance.partials.organization-summary')

    {{-- Detail transaksi --}}
    @include('admin.reports.finance.partials.table')

@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | Grafik pemasukan dan pengeluaran
            |--------------------------------------------------------------------------
            */

            const financeChartElement =
                document.getElementById('financeTrendChart');

            if (financeChartElement) {

                new Chart(
                    financeChartElement,
                    {
                        type: 'line',

                        data: {
                            labels: @json($chartLabels),

                            datasets: [
                                {
                                    label: 'Pemasukan',

                                    data:
                                        @json($chartIncome),

                                    tension: 0.3,

                                    fill: false
                                },

                                {
                                    label: 'Pengeluaran',

                                    data:
                                        @json($chartExpense),

                                    tension: 0.3,

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

                                        label: function (context) {

                                            const value =
                                                Number(
                                                    context.raw || 0
                                                );

                                            return (
                                                context.dataset.label +
                                                ': Rp ' +
                                                value.toLocaleString(
                                                    'id-ID'
                                                )
                                            );
                                        }
                                    }
                                }
                            },

                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        callback:
                                            function (value) {

                                                return 'Rp ' +
                                                    Number(value)
                                                        .toLocaleString(
                                                            'id-ID'
                                                        );
                                            }
                                    }
                                }
                            }
                        }
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Grafik per Unit
            |--------------------------------------------------------------------------
            */

            const organizationChartElement =
                document.getElementById(
                    'financeOrganizationChart'
                );

            if (organizationChartElement) {

                const organizationData =
                    @json($organizationChart);

                new Chart(
                    organizationChartElement,
                    {
                        type: 'bar',

                        data: {

                            labels:
                                organizationData.map(
                                    item => item.name
                                ),

                            datasets: [

                                {
                                    label: 'Pemasukan',

                                    data:
                                        organizationData.map(
                                            item => item.income
                                        )
                                },

                                {
                                    label: 'Pengeluaran',

                                    data:
                                        organizationData.map(
                                            item => item.expense
                                        )
                                }
                            ]
                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            plugins: {

                                tooltip: {

                                    callbacks: {

                                        label: function (context) {

                                            const value =
                                                Number(
                                                    context.raw || 0
                                                );

                                            return (
                                                context.dataset.label +
                                                ': Rp ' +
                                                value.toLocaleString(
                                                    'id-ID'
                                                )
                                            );
                                        }
                                    }
                                }
                            },

                            scales: {

                                y: {

                                    beginAtZero: true,

                                    ticks: {

                                        callback:
                                            function (value) {

                                                return 'Rp ' +
                                                    Number(value)
                                                        .toLocaleString(
                                                            'id-ID'
                                                        );
                                            }
                                    }
                                }
                            }
                        }
                    }
                );
            }

        });
    </script>
@endpush
