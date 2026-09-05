@extends('layouts.kepala-unit')

@section('content')
    <div x-data="{ openFilter: false }" class="max-w-7xl mx-auto space-y-6">

        {{-- =========================================================
    HEADER
    ========================================================== --}}

        <div
            class="flex flex-col
               sm:flex-row
               sm:items-center
               sm:justify-between
               gap-4">

            <div>

                <h1 class="text-2xl font-bold text-gray-800">
                    Ringkasan Keuangan
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Kondisi keuangan unit berdasarkan periode.
                </p>

            </div>


            {{-- Filter --}}
            <button type="button" @click="openFilter = true"
                class="inline-flex items-center
                   justify-center
                   gap-2
                   px-4 py-2.5
                   rounded-xl
                   bg-blue-600
                   text-white
                   text-sm font-medium
                   shadow-sm
                   hover:bg-blue-700
                   transition">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5h18M6 12h12m-9 7h6" />
                </svg>

                Filter

                @if ($month || request()->has('year'))
                    <span
                        class="w-2 h-2
                           rounded-full
                           bg-red-300"></span>
                @endif

            </button>

        </div>


        {{-- =========================================================
    PERIODE AKTIF
    ========================================================== --}}

        <div class="flex items-center gap-2
               text-sm text-gray-500">

            <span>
                Periode:
            </span>

            <span class="font-semibold
                   text-gray-700">
                {{ $periodLabel }}
            </span>

        </div>


        {{-- =========================================================
    SUMMARY CARDS
    ========================================================== --}}

        <div class="grid grid-cols-1
               md:grid-cols-3
               gap-4">

            {{-- PEMASUKAN --}}
            <div
                class="bg-white
                   rounded-2xl
                   border border-gray-100
                   shadow-sm
                   p-5">

                <div class="flex items-start
                       justify-between">

                    <div>

                        <p class="text-sm
                               text-gray-500">
                            Pemasukan
                        </p>

                        <p
                            class="mt-2
                               text-2xl
                               font-bold
                               text-emerald-600">
                            Rp
                            {{ number_format($totalIncome, 0, ',', '.') }}
                        </p>

                    </div>


                    <div
                        class="w-11 h-11
                           rounded-xl
                           bg-emerald-50
                           text-emerald-600
                           flex items-center
                           justify-center">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 19V5m0 0-5 5m5-5 5 5" />
                        </svg>

                    </div>

                </div>

            </div>


            {{-- PENGELUARAN --}}
            <div
                class="bg-white
                   rounded-2xl
                   border border-gray-100
                   shadow-sm
                   p-5">

                <div class="flex items-start
                       justify-between">

                    <div>

                        <p class="text-sm
                               text-gray-500">
                            Pengeluaran
                        </p>

                        <p
                            class="mt-2
                               text-2xl
                               font-bold
                               text-red-600">
                            Rp
                            {{ number_format($totalExpense, 0, ',', '.') }}
                        </p>

                    </div>


                    <div
                        class="w-11 h-11
                           rounded-xl
                           bg-red-50
                           text-red-600
                           flex items-center
                           justify-center">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 5v14m0 0 5-5m-5 5-5-5" />
                        </svg>

                    </div>

                </div>

            </div>


            {{-- SELISIH --}}
            <div
                class="bg-white
                   rounded-2xl
                   border border-gray-100
                   shadow-sm
                   p-5">

                <div class="flex items-start
                       justify-between">

                    <div>

                        <p class="text-sm
                               text-gray-500">
                            Saldo Bersih
                        </p>

                        <p
                            class="mt-2
                               text-2xl
                               font-bold
                               {{ $netAmount >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            Rp
                            {{ number_format($netAmount, 0, ',', '.') }}
                        </p>

                        <p class="mt-1
                               text-xs
                               text-gray-400">
                            Pemasukan − Pengeluaran
                        </p>

                    </div>


                    <div
                        class="w-11 h-11
                           rounded-xl
                           bg-blue-50
                           text-blue-600
                           flex items-center
                           justify-center">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M4 19V5m0 14h16M8 16v-5m4 5V8m4 8v-3" />
                        </svg>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
    ARUS KEUANGAN
    ========================================================== --}}

        <div
            class="bg-white
               rounded-2xl
               border border-gray-100
               shadow-sm
               overflow-hidden">

            <div class="px-5 py-4
                   border-b border-gray-100">

                <h2 class="font-semibold
                       text-gray-800">
                    Arus Keuangan
                </h2>

                <p class="mt-1
                       text-xs
                       text-gray-500">
                    {{ $chartPeriodLabel }}
                </p>

            </div>


            <div class="p-5">

                <div class="relative
                       h-80">

                    <canvas id="financeChart"></canvas>

                </div>

            </div>

        </div>


        {{-- =========================================================
    KATEGORI
    ========================================================== --}}

        <div class="grid grid-cols-1
               lg:grid-cols-2
               gap-6">

            {{-- PEMASUKAN TERBESAR --}}
            <div
                class="bg-white
                   rounded-2xl
                   border border-gray-100
                   shadow-sm
                   overflow-hidden">

                <div class="px-5 py-4
                       border-b border-gray-100">

                    <h2 class="font-semibold
                           text-gray-800">
                        Pemasukan Terbesar
                    </h2>

                    <p class="mt-1
                           text-xs
                           text-gray-500">
                        Berdasarkan kategori.
                    </p>

                </div>


                <div class="divide-y divide-gray-100">

                    @forelse($incomeByCategory->take(5)
                                as $item)
                        <div
                            class="px-5 py-4
                               flex items-center
                               justify-between
                               gap-4">

                            <div class="flex items-center
                                   gap-3">

                                <div
                                    class="w-8 h-8
                                       rounded-lg
                                       bg-emerald-50
                                       text-emerald-600
                                       flex items-center
                                       justify-center
                                       text-xs
                                       font-bold">
                                    {{ $loop->iteration }}
                                </div>

                                <span
                                    class="text-sm
                                       font-medium
                                       text-gray-700">
                                    {{ $item->category }}
                                </span>

                            </div>


                            <span
                                class="text-sm
                                   font-semibold
                                   text-emerald-600
                                   whitespace-nowrap">
                                Rp
                                {{ number_format($item->total, 0, ',', '.') }}
                            </span>

                        </div>

                    @empty

                        <div
                            class="px-5 py-10
                               text-center
                               text-sm
                               text-gray-400">
                            Belum ada pemasukan.
                        </div>
                    @endforelse

                </div>

            </div>


            {{-- PENGELUARAN TERBESAR --}}
            <div
                class="bg-white
                   rounded-2xl
                   border border-gray-100
                   shadow-sm
                   overflow-hidden">

                <div class="px-5 py-4
                       border-b border-gray-100">

                    <h2 class="font-semibold
                           text-gray-800">
                        Pengeluaran Terbesar
                    </h2>

                    <p class="mt-1
                           text-xs
                           text-gray-500">
                        Berdasarkan kategori.
                    </p>

                </div>


                <div class="divide-y divide-gray-100">

                    @forelse($expenseByCategory->take(5)
                                as $item)
                        <div
                            class="px-5 py-4
                               flex items-center
                               justify-between
                               gap-4">

                            <div class="flex items-center
                                   gap-3">

                                <div
                                    class="w-8 h-8
                                       rounded-lg
                                       bg-red-50
                                       text-red-600
                                       flex items-center
                                       justify-center
                                       text-xs
                                       font-bold">
                                    {{ $loop->iteration }}
                                </div>

                                <span
                                    class="text-sm
                                       font-medium
                                       text-gray-700">
                                    {{ $item->category }}
                                </span>

                            </div>


                            <span
                                class="text-sm
                                   font-semibold
                                   text-red-600
                                   whitespace-nowrap">
                                Rp
                                {{ number_format($item->total, 0, ',', '.') }}
                            </span>

                        </div>

                    @empty

                        <div
                            class="px-5 py-10
                               text-center
                               text-sm
                               text-gray-400">
                            Belum ada pengeluaran.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>


        {{-- =========================================================
    SETORAN KE INDUK
    ========================================================== --}}

        <div
            class="bg-white
               rounded-2xl
               border border-gray-100
               shadow-sm
               overflow-hidden">

            <div class="px-5 py-4
                   border-b border-gray-100">

                <h2 class="font-semibold
                       text-gray-800">
                    Setoran ke Induk
                </h2>

                <p class="mt-1
                       text-xs
                       text-gray-500">
                    Transfer internal unit ke organisasi induk.
                </p>

            </div>


            <div class="grid grid-cols-1
                   md:grid-cols-3">

                {{-- Pending --}}
                <div
                    class="p-5
                       border-b
                       md:border-b-0
                       md:border-r
                       border-gray-100">

                    <p
                        class="text-xs
                           font-semibold
                           uppercase
                           text-amber-600">
                        Menunggu
                    </p>

                    <p
                        class="mt-2
                           text-xl
                           font-bold
                           text-gray-800">
                        Rp
                        {{ number_format($pendingDepositAmount, 0, ',', '.') }}
                    </p>

                    <p class="mt-1
                           text-xs
                           text-gray-400">
                        {{ $pendingDepositCount }} setoran
                    </p>

                </div>


                {{-- Confirmed --}}
                <div
                    class="p-5
                       border-b
                       md:border-b-0
                       md:border-r
                       border-gray-100">

                    <p
                        class="text-xs
                           font-semibold
                           uppercase
                           text-emerald-600">
                        Dikonfirmasi
                    </p>

                    <p
                        class="mt-2
                           text-xl
                           font-bold
                           text-gray-800">
                        Rp
                        {{ number_format($confirmedDepositAmount, 0, ',', '.') }}
                    </p>

                    <p class="mt-1
                           text-xs
                           text-gray-400">
                        {{ $confirmedDepositCount }} setoran
                    </p>

                </div>


                {{-- Rejected --}}
                <div class="p-5">

                    <p
                        class="text-xs
                           font-semibold
                           uppercase
                           text-red-600">
                        Ditolak
                    </p>

                    <p
                        class="mt-2
                           text-xl
                           font-bold
                           text-gray-800">
                        Rp
                        {{ number_format($rejectedDepositAmount, 0, ',', '.') }}
                    </p>

                    <p class="mt-1
                           text-xs
                           text-gray-400">
                        {{ $rejectedDepositCount }} setoran
                    </p>

                </div>

            </div>

        </div>


        {{-- =========================================================
    FILTER POPUP
    ========================================================== --}}

        <div x-cloak x-show="openFilter" class="fixed inset-0 z-50
               overflow-y-auto" role="dialog"
            aria-modal="true" x-transition @click.self="openFilter = false">

            {{-- Overlay --}}
            <div class="fixed inset-0
                   bg-black/50
                   backdrop-blur-sm"
                @click="openFilter = false"></div>


            {{-- Modal --}}
            <div
                class="relative min-h-screen
                   flex items-center
                   justify-center
                   p-4">

                <div x-show="openFilter" x-transition @click.stop
                    class="relative
                       w-full max-w-lg
                       bg-white
                       rounded-2xl
                       shadow-2xl
                       overflow-hidden">

                    {{-- Header --}}
                    <div
                        class="px-6 py-4
                           border-b border-gray-100
                           flex items-center
                           justify-between">

                        <div>

                            <h2
                                class="text-lg
                                   font-bold
                                   text-gray-800">
                                Filter Ringkasan
                            </h2>

                            <p
                                class="mt-1
                                   text-xs
                                   text-gray-500">
                                Pilih tahun dan periode yang ingin dilihat.
                            </p>

                        </div>


                        <button type="button" @click="openFilter = false"
                            class="w-9 h-9
                               rounded-lg
                               text-gray-400
                               hover:bg-gray-100
                               hover:text-gray-600
                               flex items-center
                               justify-center">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M6 6l12 12M18 6 6 18" />
                            </svg>

                        </button>

                    </div>


                    {{-- Form --}}
                    <form method="GET" action="{{ route('kepala-unit.finance.summary.index') }}">

                        <div class="p-6 space-y-5">

                            {{-- Tahun --}}
                            <div>

                                <label
                                    class="block
                                       mb-1.5
                                       text-xs
                                       font-semibold
                                       text-gray-600">
                                    Tahun
                                </label>

                                <select name="year"
                                    class="w-full
                                    rounded-xl
                                    border-gray-300
                                    text-sm
                                    focus:border-blue-500
                                    focus:ring-blue-500">

                                    <option value="" @selected($year === null)>
                                        Semua Tahun
                                    </option>

                                    @foreach ($availableYears as $availableYear)
                                        <option value="{{ $availableYear }}" @selected($year === $availableYear)>
                                            {{ $availableYear }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- Bulan --}}
                            <div>

                                <label
                                    class="block
                                       mb-1.5
                                       text-xs
                                       font-semibold
                                       text-gray-600">
                                    Periode
                                </label>

                                <select name="month"
                                    class="w-full
                                       rounded-xl
                                       border-gray-300
                                       text-sm
                                       focus:border-blue-500
                                       focus:ring-blue-500">

                                    <option value="">
                                        Semua Bulan
                                    </option>

                                    <option value="1" @selected($month === 1)>
                                        Januari
                                    </option>

                                    <option value="2" @selected($month === 2)>
                                        Februari
                                    </option>

                                    <option value="3" @selected($month === 3)>
                                        Maret
                                    </option>

                                    <option value="4" @selected($month === 4)>
                                        April
                                    </option>

                                    <option value="5" @selected($month === 5)>
                                        Mei
                                    </option>

                                    <option value="6" @selected($month === 6)>
                                        Juni
                                    </option>

                                    <option value="7" @selected($month === 7)>
                                        Juli
                                    </option>

                                    <option value="8" @selected($month === 8)>
                                        Agustus
                                    </option>

                                    <option value="9" @selected($month === 9)>
                                        September
                                    </option>

                                    <option value="10" @selected($month === 10)>
                                        Oktober
                                    </option>

                                    <option value="11" @selected($month === 11)>
                                        November
                                    </option>

                                    <option value="12" @selected($month === 12)>
                                        Desember
                                    </option>

                                </select>

                                <p
                                    class="mt-2
                                       text-xs
                                       text-gray-400">
                                    Semua Bulan menampilkan grafik Januari–Desember.
                                    Pilih bulan tertentu untuk melihat grafik harian.
                                </p>

                            </div>

                        </div>


                        {{-- Footer --}}
                        <div
                            class="px-6 py-4
                               bg-gray-50
                               border-t
                               border-gray-100
                               flex justify-end
                               gap-2">

                            <a href="{{ route('kepala-unit.finance.summary.index') }}"
                                class="px-4 py-2.5
                                   rounded-xl
                                   bg-white
                                   border
                                   border-gray-300
                                   text-gray-700
                                   text-sm
                                   font-medium
                                   hover:bg-gray-100">
                                Reset
                            </a>


                            <button type="button" @click="openFilter = false"
                                class="px-4 py-2.5
                                   rounded-xl
                                   bg-gray-200
                                   text-gray-700
                                   text-sm
                                   font-medium
                                   hover:bg-gray-300">
                                Batal
                            </button>


                            <button type="submit"
                                class="px-4 py-2.5
                                   rounded-xl
                                   bg-blue-600
                                   text-white
                                   text-sm
                                   font-medium
                                   hover:bg-blue-700">
                                Terapkan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
CHART.JS
========================================================= --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const canvas =
                document.getElementById('financeChart');

            if (!canvas) {
                return;
            }

            const labels =
                @json($chartLabels);

            const income =
                @json($chartIncome);

            const expense =
                @json($chartExpense);

            new Chart(canvas, {

                type: 'line',

                data: {

                    labels: labels,

                    datasets: [

                        {
                            label: 'Pemasukan',

                            data: income,

                            borderColor: '#059669',

                            backgroundColor: 'rgba(5, 150, 105, 0.08)',

                            borderWidth: 2,

                            pointRadius: 3,

                            pointHoverRadius: 5,

                            tension: 0.3,

                            fill: false
                        },

                        {
                            label: 'Pengeluaran',

                            data: expense,

                            borderColor: '#dc2626',

                            backgroundColor: 'rgba(220, 38, 38, 0.08)',

                            borderWidth: 2,

                            pointRadius: 3,

                            pointHoverRadius: 5,

                            tension: 0.3,

                            fill: false
                        }

                    ]

                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    interaction: {

                        mode: 'index',

                        intersect: false

                    },

                    plugins: {

                        legend: {

                            position: 'top',

                            align: 'end'

                        },

                        tooltip: {

                            callbacks: {

                                label: function(context) {

                                    return (
                                        context.dataset.label +
                                        ': Rp ' +
                                        new Intl.NumberFormat(
                                            'id-ID'
                                        ).format(
                                            context.parsed.y
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

                                callback: function(value) {

                                    return 'Rp ' +
                                        new Intl.NumberFormat(
                                            'id-ID', {
                                                notation: 'compact',
                                                maximumFractionDigits: 1
                                            }
                                        ).format(value);

                                }

                            }

                        },

                        x: {

                            grid: {

                                display: false

                            }

                        }

                    }

                }

            });

        });
    </script>
@endsection
