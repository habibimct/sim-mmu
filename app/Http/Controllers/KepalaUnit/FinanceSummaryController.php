<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use App\Models\FinanceDeposit;
use App\Models\FinanceTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceSummaryController extends Controller
{
    /**
     * Ringkasan keuangan Kepala Unit.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Organisasi yang menjadi kewenangan user
    |--------------------------------------------------------------------------
    */

        $organizationIds = $user
            ->organizations()
            ->where('organizations.is_active', true)
            ->pluck('organizations.id');


        /*
    |--------------------------------------------------------------------------
    | Tahun
    |--------------------------------------------------------------------------
    |
    | null = Semua Tahun
    | 2026 = Tahun 2026
    |
    */

        $yearInput = $request->input('year');

        $year = null;

        if (
            $yearInput !== null
            && $yearInput !== ''
        ) {

            $year = (int) $yearInput;

            /*
        |----------------------------------------------------------------------
        | Validasi tahun
        |----------------------------------------------------------------------
        */

            if (
                $year < 2020
                || $year > now()->year + 1
            ) {
                $year = now()->year;
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Bulan
    |--------------------------------------------------------------------------
    |
    | null / kosong = semua bulan
    | 1-12          = bulan tertentu
    |
    */

        $month = $request->input('month');

        if (
            $month !== null
            && $month !== ''
            && (
                ! is_numeric($month)
                || (int) $month < 1
                || (int) $month > 12
            )
        ) {
            $month = null;
        }

        $month = $month !== null
            ? (int) $month
            : null;


        /*
    |--------------------------------------------------------------------------
    | Periode
    |--------------------------------------------------------------------------
    |
    | Tahun dipilih + bulan dipilih
    |     → periode bulan tersebut
    |
    | Tahun dipilih + semua bulan
    |     → periode satu tahun
    |
    | Semua tahun + bulan dipilih
    |     → tidak menggunakan start/end date
    |        karena bulan akan difilter dengan whereMonth()
    |
    | Semua tahun + semua bulan
    |     → seluruh transaksi
    |
    */

        $startDate = null;
        $endDate = null;

        if ($year !== null) {

            if ($month !== null) {

                $startDate = Carbon::create(
                    $year,
                    $month,
                    1
                )->startOfMonth();

                $endDate = $startDate
                    ->copy()
                    ->endOfMonth();
            } else {

                $startDate = Carbon::create(
                    $year,
                    1,
                    1
                )->startOfYear();

                $endDate = $startDate
                    ->copy()
                    ->endOfYear();
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Query transaksi
    |--------------------------------------------------------------------------
    */

        $transactionQuery = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )

            /*
        |----------------------------------------------------------------------
        | Tahun + bulan
        |----------------------------------------------------------------------
        */

            ->when(
                $startDate && $endDate,
                fn($query) =>
                $query->whereBetween(
                    'transaction_date',
                    [
                        $startDate->toDateString(),
                        $endDate->toDateString(),
                    ]
                )
            )

            /*
        |----------------------------------------------------------------------
        | Semua tahun + bulan tertentu
        |----------------------------------------------------------------------
        */

            ->when(
                $year === null && $month !== null,
                fn($query) =>
                $query->whereMonth(
                    'transaction_date',
                    $month
                )
            );


        /*
    |--------------------------------------------------------------------------
    | Total pemasukan
    |--------------------------------------------------------------------------
    */

        $totalIncome = (clone $transactionQuery)
            ->where('status', 'confirmed')
            ->where('type', 'income')
            ->sum('amount');


        /*
    |--------------------------------------------------------------------------
    | Total pengeluaran
    |--------------------------------------------------------------------------
    */

        $totalExpense = (clone $transactionQuery)
            ->where('status', 'confirmed')
            ->where('type', 'expense')
            ->sum('amount');


        /*
    |--------------------------------------------------------------------------
    | Selisih bersih
    |--------------------------------------------------------------------------
    */

        $netAmount =
            $totalIncome - $totalExpense;


        /*
    |--------------------------------------------------------------------------
    | Pemasukan berdasarkan kategori
    |--------------------------------------------------------------------------
    */

        $incomeByCategory = (clone $transactionQuery)
            ->where('status', 'confirmed')
            ->where('type', 'income')
            ->selectRaw(
                'COALESCE(category, "Tanpa Kategori") as category,
            SUM(amount) as total'
            )
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Pengeluaran berdasarkan kategori
    |--------------------------------------------------------------------------
    */

        $expenseByCategory = (clone $transactionQuery)
            ->where('status', 'confirmed')
            ->where('type', 'expense')
            ->selectRaw(
                'COALESCE(category, "Tanpa Kategori") as category,
            SUM(amount) as total'
            )
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | SETORAN KE INDUK
    |--------------------------------------------------------------------------
    |
    | FinanceDeposit merupakan transfer internal.
    |
    */

        $depositQuery = FinanceDeposit::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )

            /*
        |----------------------------------------------------------------------
        | Tahun + periode
        |----------------------------------------------------------------------
        */

            ->when(
                $startDate && $endDate,
                fn($query) =>
                $query->whereBetween(
                    'deposit_date',
                    [
                        $startDate->toDateString(),
                        $endDate->toDateString(),
                    ]
                )
            )

            /*
        |----------------------------------------------------------------------
        | Semua tahun + bulan tertentu
        |----------------------------------------------------------------------
        */

            ->when(
                $year === null && $month !== null,
                fn($query) =>
                $query->whereMonth(
                    'deposit_date',
                    $month
                )
            );


        /*
    |--------------------------------------------------------------------------
    | Setoran pending
    |--------------------------------------------------------------------------
    */

        $pendingDepositAmount = (clone $depositQuery)
            ->where('status', 'pending')
            ->sum('amount');

        $pendingDepositCount = (clone $depositQuery)
            ->where('status', 'pending')
            ->count();


        /*
    |--------------------------------------------------------------------------
    | Setoran confirmed
    |--------------------------------------------------------------------------
    */

        $confirmedDepositAmount = (clone $depositQuery)
            ->where('status', 'confirmed')
            ->sum('amount');

        $confirmedDepositCount = (clone $depositQuery)
            ->where('status', 'confirmed')
            ->count();


        /*
    |--------------------------------------------------------------------------
    | Setoran rejected
    |--------------------------------------------------------------------------
    */

        $rejectedDepositAmount = (clone $depositQuery)
            ->where('status', 'rejected')
            ->sum('amount');

        $rejectedDepositCount = (clone $depositQuery)
            ->where('status', 'rejected')
            ->count();


        /*
    |--------------------------------------------------------------------------
    | DATA GRAFIK
    |--------------------------------------------------------------------------
    |
    | Tahun tertentu + semua bulan
    |     → Januari - Desember
    |
    | Semua tahun + semua bulan
    |     → Januari - Desember gabungan semua tahun
    |
    | Tahun tertentu + bulan tertentu
    |     → grafik harian
    |
    | Semua tahun + bulan tertentu
    |     → grafik harian gabungan semua tahun
    |
    */

        if ($month === null) {

            /*
        |----------------------------------------------------------------------
        | Grafik tahunan
        |----------------------------------------------------------------------
        */

            $monthlyTransactions = (clone $transactionQuery)
                ->where('status', 'confirmed')
                ->selectRaw(
                    'MONTH(transaction_date) as period,
                SUM(
                    CASE
                        WHEN type = "income"
                        THEN amount
                        ELSE 0
                    END
                ) as income,
                SUM(
                    CASE
                        WHEN type = "expense"
                        THEN amount
                        ELSE 0
                    END
                ) as expense'
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->keyBy('period');


            $chartLabels = [];

            $chartIncome = [];

            $chartExpense = [];


            $monthNames = [
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'Mei',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Agu',
                9 => 'Sep',
                10 => 'Okt',
                11 => 'Nov',
                12 => 'Des',
            ];


            for (
                $i = 1;
                $i <= 12;
                $i++
            ) {

                $chartLabels[] =
                    $monthNames[$i];


                $chartIncome[] =
                    (float) (
                        $monthlyTransactions[$i]->income
                        ?? 0
                    );


                $chartExpense[] =
                    (float) (
                        $monthlyTransactions[$i]->expense
                        ?? 0
                    );
            }


            if ($year === null) {

                $chartPeriodLabel =
                    'Per Bulan - Semua Tahun';
            } else {

                $chartPeriodLabel =
                    'Per Bulan - ' . $year;
            }
        } else {

            /*
        |----------------------------------------------------------------------
        | Grafik bulanan / harian
        |----------------------------------------------------------------------
        */

            $dailyTransactions = (clone $transactionQuery)
                ->where('status', 'confirmed')
                ->selectRaw(
                    'DAY(transaction_date) as period,
                SUM(
                    CASE
                        WHEN type = "income"
                        THEN amount
                        ELSE 0
                    END
                ) as income,
                SUM(
                    CASE
                        WHEN type = "expense"
                        THEN amount
                        ELSE 0
                    END
                ) as expense'
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->keyBy('period');


            /*
        |----------------------------------------------------------------------
        | Jumlah hari
        |----------------------------------------------------------------------
        */

            if ($year !== null) {

                $daysInMonth =
                    $startDate->daysInMonth;
            } else {

                /*
            |------------------------------------------------------------------
            | Semua tahun + bulan tertentu
            |------------------------------------------------------------------
            |
            | Kita gunakan tahun kabisat agar tanggal 29 Februari
            | tetap tersedia.
            |
            */

                $daysInMonth =
                    Carbon::create(
                        2000,
                        $month,
                        1
                    )->daysInMonth;
            }


            $chartLabels = [];

            $chartIncome = [];

            $chartExpense = [];


            for (
                $day = 1;
                $day <= $daysInMonth;
                $day++
            ) {

                $chartLabels[] =
                    (string) $day;


                $chartIncome[] =
                    (float) (
                        $dailyTransactions[$day]->income
                        ?? 0
                    );


                $chartExpense[] =
                    (float) (
                        $dailyTransactions[$day]->expense
                        ?? 0
                    );
            }


            if ($year === null) {

                $chartPeriodLabel =
                    'Per Hari - '
                    . Carbon::create(
                        2000,
                        $month,
                        1
                    )->translatedFormat('F')
                    . ' - Semua Tahun';
            } else {

                $chartPeriodLabel =
                    'Per Hari - '
                    . $startDate->translatedFormat('F Y');
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Label periode
    |--------------------------------------------------------------------------
    */

        if ($year === null) {

            if ($month !== null) {

                $periodLabel =
                    Carbon::create(
                        2000,
                        $month,
                        1
                    )->translatedFormat('F')
                    . ' - Semua Tahun';
            } else {

                $periodLabel =
                    'Semua Tahun';
            }
        } elseif ($month !== null) {

            $periodLabel =
                $startDate->translatedFormat('F Y');
        } else {

            $periodLabel =
                'Januari - Desember ' . $year;
        }


        /*
    |--------------------------------------------------------------------------
    | Daftar tahun untuk filter
    |--------------------------------------------------------------------------
    */

        $availableYears = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->selectRaw(
                'YEAR(transaction_date) as year'
            )
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');


        /*
    |--------------------------------------------------------------------------
    | Pastikan tahun sekarang tetap tersedia
    |--------------------------------------------------------------------------
    */

        if (
            $year !== null
            && ! $availableYears->contains($year)
        ) {
            $availableYears->push($year);
        }


        $availableYears =
            $availableYears
            ->unique()
            ->sortDesc()
            ->values();


        /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

        return view(
            'kepala-unit.finance.summary.index',
            compact(
                'year',
                'month',
                'periodLabel',
                'chartPeriodLabel',

                'availableYears',

                'totalIncome',
                'totalExpense',
                'netAmount',

                'incomeByCategory',
                'expenseByCategory',

                'pendingDepositAmount',
                'pendingDepositCount',

                'confirmedDepositAmount',
                'confirmedDepositCount',

                'rejectedDepositAmount',
                'rejectedDepositCount',

                'chartLabels',
                'chartIncome',
                'chartExpense'
            )
        );
    }
}
