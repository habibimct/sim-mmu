<?php

namespace App\Http\Controllers\KetuaInduk;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class FinanceController extends Controller
{
    /**
     * Ringkasan keuangan Ketua INDUK.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validasi akses
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $user->is_active
                && $user->can('finance.view')
                && $user->roles()
                ->where('code', 'ketua_induk')
                ->exists(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Organisasi INDUK milik user
        |--------------------------------------------------------------------------
        */

        $induk = $user
            ->organizations()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->first();

        abort_unless(
            $induk,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Organisasi dalam cakupan INDUK
        |--------------------------------------------------------------------------
        */

        $organizationIds = Organization::query()
            ->where(function ($query) use ($induk) {

                $query
                    ->whereKey($induk->id)
                    ->orWhere(
                        'parent_id',
                        $induk->id
                    );
            })
            ->where('is_active', true)
            ->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Daftar organisasi untuk filter dan rekap
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn(
                'id',
                $organizationIds
            )
            ->where('is_active', true)
            ->orderByRaw(
                'CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END'
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Tahun dan bulan
        |--------------------------------------------------------------------------
        |
        | Keduanya bersifat opsional.
        |
        | year  = 2026
        | month = kosong
        | → seluruh tahun 2026
        |
        | year  = 2026
        | month = 8
        | → Agustus 2026
        |
        */

        $year = $request->filled('year')
            ? (int) $request->year
            : null;

        $month = $request->filled('month')
            ? (int) $request->month
            : null;


        /*
        |--------------------------------------------------------------------------
        | Validasi tahun
        |--------------------------------------------------------------------------
        */

        if (
            $year !== null
            && (
                $year < 2000
                || $year > now()->year + 1
            )
        ) {
            $year = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi bulan
        |--------------------------------------------------------------------------
        */

        if (
            $month !== null
            && (
                $month < 1
                || $month > 12
            )
        ) {
            $month = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Query dasar transaksi
        |--------------------------------------------------------------------------
        */

        $transactionQuery = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            );


        /*
        |--------------------------------------------------------------------------
        | Filter tanggal lama
        |--------------------------------------------------------------------------
        |
        | Filter date_from dan date_to tetap dipertahankan.
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $transactionQuery->whereDate(
                'transaction_date',
                '>=',
                $request->date_from
            );
        }


        if ($request->filled('date_to')) {

            $transactionQuery->whereDate(
                'transaction_date',
                '<=',
                $request->date_to
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter TAHUN
        |--------------------------------------------------------------------------
        */

        if ($year !== null) {

            $transactionQuery->whereYear(
                'transaction_date',
                $year
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter BULAN
        |--------------------------------------------------------------------------
        */

        if ($month !== null) {

            $transactionQuery->whereMonth(
                'transaction_date',
                $month
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter organisasi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('organization_id')) {

            $selectedOrganizationId =
                (int) $request->organization_id;

            abort_unless(
                $organizationIds->contains(
                    $selectedOrganizationId
                ),
                403
            );

            $transactionQuery->where(
                'organization_id',
                $selectedOrganizationId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter jenis transaksi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('type')) {

            $transactionQuery->where(
                'type',
                $request->type
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $transactionQuery->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter metode pembayaran
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_method')) {

            $transactionQuery->where(
                'payment_method',
                $request->payment_method
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $transactionQuery->where(
                function ($query) use ($search) {

                    $query
                        ->where(
                            'category',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'description',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        |
        | Ringkasan otomatis mengikuti seluruh filter:
        |
        | - tanggal
        | - tahun
        | - bulan
        | - organisasi
        | - jenis
        | - status
        | - metode
        | - pencarian
        |
        | Hanya transaksi confirmed yang dihitung.
        |--------------------------------------------------------------------------
        */

        $summary = (clone $transactionQuery)
            ->where(
                'status',
                'confirmed'
            )
            ->selectRaw(
                '
                COALESCE(
                    SUM(
                        CASE
                            WHEN type = "income"
                            THEN amount
                            ELSE 0
                        END
                    ),
                    0
                ) as total_income,

                COALESCE(
                    SUM(
                        CASE
                            WHEN type = "expense"
                            THEN amount
                            ELSE 0
                        END
                    ),
                    0
                ) as total_expense
                '
            )
            ->first();


        $totalIncome =
            (float) $summary->total_income;


        $totalExpense =
            (float) $summary->total_expense;


        $balance =
            $totalIncome
            - $totalExpense;


        /*
        |--------------------------------------------------------------------------
        | Rekap per organisasi
        |--------------------------------------------------------------------------
        |
        | Filter organisasi sengaja TIDAK digunakan di sini.
        |
        | Ketua INDUK tetap dapat melihat perbandingan seluruh organisasi
        | dalam cakupan induknya.
        |
        | Tetapi:
        |
        | - date_from
        | - date_to
        | - year
        | - month
        | - type
        | - payment_method
        |
        | tetap memengaruhi rekap.
        |--------------------------------------------------------------------------
        */

        $organizationSummaryQuery = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'status',
                'confirmed'
            );


        /*
        |--------------------------------------------------------------------------
        | Filter tanggal untuk rekap
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $organizationSummaryQuery->whereDate(
                'transaction_date',
                '>=',
                $request->date_from
            );
        }


        if ($request->filled('date_to')) {

            $organizationSummaryQuery->whereDate(
                'transaction_date',
                '<=',
                $request->date_to
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter tahun untuk rekap
        |--------------------------------------------------------------------------
        */

        if ($year !== null) {

            $organizationSummaryQuery->whereYear(
                'transaction_date',
                $year
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter bulan untuk rekap
        |--------------------------------------------------------------------------
        */

        if ($month !== null) {

            $organizationSummaryQuery->whereMonth(
                'transaction_date',
                $month
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter jenis untuk rekap
        |--------------------------------------------------------------------------
        */

        if ($request->filled('type')) {

            $organizationSummaryQuery->where(
                'type',
                $request->type
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter metode pembayaran untuk rekap
        |--------------------------------------------------------------------------
        */

        if ($request->filled('payment_method')) {

            $organizationSummaryQuery->where(
                'payment_method',
                $request->payment_method
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Bentuk rekap per organisasi
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| Rekap per organisasi
|--------------------------------------------------------------------------
|
| Sebelumnya setiap organisasi melakukan query income + expense
| masing-masing.
|
| Sekarang seluruh organisasi dihitung dalam SATU query agregasi.
|
*/

        $organizationTotals = (clone $organizationSummaryQuery)
            ->selectRaw(
                '
        organization_id,

        SUM(
            CASE
                WHEN type = "income"
                THEN amount
                ELSE 0
            END
        ) as income_total,

        SUM(
            CASE
                WHEN type = "expense"
                THEN amount
                ELSE 0
            END
        ) as expense_total
        '
            )
            ->groupBy('organization_id')
            ->get()
            ->keyBy('organization_id');


        /*
|--------------------------------------------------------------------------
| Tempelkan hasil agregasi ke daftar organisasi
|--------------------------------------------------------------------------
*/

        $organizationSummary = $organizations
            ->map(
                function ($organization) use (
                    $organizationTotals
                ) {

                    $total =
                        $organizationTotals->get(
                            $organization->id
                        );


                    $income =
                        (float) (
                            $total->income_total
                            ?? 0
                        );


                    $expense =
                        (float) (
                            $total->expense_total
                            ?? 0
                        );


                    $organization->income_total =
                        $income;

                    $organization->expense_total =
                        $expense;

                    $organization->balance =
                        $income - $expense;


                    return $organization;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | GRAFIK ARUS KEUANGAN
        |--------------------------------------------------------------------------
        |
        | Grafik menggunakan transaksi confirmed.
        |
        | Jika:
        |
        | year = 2026
        | month = kosong
        |
        | → 12 bulan.
        |
        | Jika:
        |
        | year = 2026
        | month = 8
        |
        | → tanggal 1 - 31 Agustus.
        |
        |--------------------------------------------------------------------------
        */

        $chartQuery = clone $transactionQuery;

        $chartQuery->where(
            'status',
            'confirmed'
        );


        $chartLabels = [];

        $chartIncome = [];

        $chartExpense = [];


        /*
        |--------------------------------------------------------------------------
        | GRAFIK PER BULAN
        |--------------------------------------------------------------------------
        */

        if ($month === null) {

            /*
            | Jika tahun dipilih:
            | gunakan tahun tersebut.
            |
            | Jika tahun tidak dipilih:
            | gunakan seluruh data dan kelompokkan per bulan.
            */

            $chartDataQuery = clone $chartQuery;


            if ($year !== null) {

                $chartDataQuery
                    ->whereYear(
                        'transaction_date',
                        $year
                    );
            }


            $chartData = $chartDataQuery
                ->selectRaw(
                    '
                    MONTH(transaction_date) as period,

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
                    ) as expense
                    '
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->keyBy('period');


            $monthNames = [
                1  => 'Jan',
                2  => 'Feb',
                3  => 'Mar',
                4  => 'Apr',
                5  => 'Mei',
                6  => 'Jun',
                7  => 'Jul',
                8  => 'Agu',
                9  => 'Sep',
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
                        $chartData[$i]->income
                        ?? 0
                    );


                $chartExpense[] =
                    (float) (
                        $chartData[$i]->expense
                        ?? 0
                    );
            }


            if ($year !== null) {

                $chartPeriodLabel =
                    'Arus keuangan per bulan tahun '
                    . $year;
            } else {

                $chartPeriodLabel =
                    'Arus keuangan per bulan';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GRAFIK PER HARI
        |--------------------------------------------------------------------------
        */ else {

            /*
            | Jika bulan dipilih tetapi tahun kosong,
            | gunakan tahun berjalan.
            */

            $chartYear =
                $year ?? now()->year;


            $chartStartDate =
                Carbon::create(
                    $chartYear,
                    $month,
                    1
                )->startOfMonth();


            $chartEndDate =
                $chartStartDate
                ->copy()
                ->endOfMonth();


            /*
            | Query harian.
            */

            $dailyData = (clone $chartQuery)
                ->whereYear(
                    'transaction_date',
                    $chartYear
                )
                ->whereMonth(
                    'transaction_date',
                    $month
                )
                ->selectRaw(
                    '
                    DAY(transaction_date) as period,

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
                    ) as expense
                    '
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->keyBy('period');


            /*
            | Buat semua tanggal dalam bulan.
            */

            for (
                $day = 1;
                $day <= $chartStartDate->daysInMonth;
                $day++
            ) {

                $chartLabels[] =
                    (string) $day;


                $chartIncome[] =
                    (float) (
                        $dailyData[$day]->income
                        ?? 0
                    );


                $chartExpense[] =
                    (float) (
                        $dailyData[$day]->expense
                        ?? 0
                    );
            }


            $chartPeriodLabel =
                'Arus keuangan per hari - '
                . $chartStartDate
                ->translatedFormat('F Y');
        }


        /*
        |--------------------------------------------------------------------------
        | Daftar tahun untuk filter
        |--------------------------------------------------------------------------
        |
        | Daftar tahun relatif stabil sehingga tidak perlu dihitung
        | ulang setiap kali halaman dibuka.
        |
        */

        $availableYears = Cache::remember(
            'ketua-induk.finance.years.' . $induk->id,
            now()->addHours(6),
            function () use ($organizationIds) {

                return FinanceTransaction::query()
                    ->whereIn(
                        'organization_id',
                        $organizationIds
                    )
                    ->selectRaw(
                        'YEAR(transaction_date) as year'
                    )
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year')
                    ->map(fn($year) => (int) $year)
                    ->values()
                    ->all();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Pastikan tahun berjalan tetap tersedia
        |--------------------------------------------------------------------------
        */

        $availableYears = collect(
            $availableYears
        );


        if (
            ! $availableYears->contains(
                now()->year
            )
        ) {

            $availableYears->push(
                now()->year
            );
        }


        $availableYears = $availableYears
            ->unique()
            ->sortDesc()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Transaksi
        |--------------------------------------------------------------------------
        |
        | Query ini tetap mempertahankan seluruh filter lama.
        |--------------------------------------------------------------------------
        */

        $transactions = (clone $transactionQuery)
            ->with([
                'organization',
                'creator',
            ])
            ->orderByDesc(
                'transaction_date'
            )
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'ketua-induk.finance.index',
            compact(
                'induk',

                'organizations',

                'organizationSummary',

                'totalIncome',

                'totalExpense',

                'balance',

                'transactions',

                /*
                | Filter tahun
                */

                'year',

                'month',

                'availableYears',

                /*
                | Grafik
                */

                'chartLabels',

                'chartIncome',

                'chartExpense',

                'chartPeriodLabel'
            )
        );
    }
}
