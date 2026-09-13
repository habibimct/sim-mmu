<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\Organization;
use App\Exports\FinanceReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class FinanceReportController extends Controller
{
    /**
     * Laporan keuangan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang boleh dilihat
        |--------------------------------------------------------------------------
        */

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
        |--------------------------------------------------------------------------
        | Daftar organisasi
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn(
                'id',
                $organizationIds
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Filter tanggal
        |--------------------------------------------------------------------------
        |
        | Default:
        | awal bulan berjalan sampai hari ini.
        |
        */

        $dateFrom = $request->input(
            'date_from',
            now()->startOfMonth()->toDateString()
        );

        $dateTo = $request->input(
            'date_to',
            now()->toDateString()
        );

        /*
        |--------------------------------------------------------------------------
        | Validasi tanggal
        |--------------------------------------------------------------------------
        */

        try {

            $startDate = Carbon::parse($dateFrom)
                ->startOfDay();

            $endDate = Carbon::parse($dateTo)
                ->endOfDay();
        } catch (\Throwable $e) {

            $startDate = now()
                ->startOfMonth()
                ->startOfDay();

            $endDate = now()
                ->endOfDay();

            $dateFrom =
                $startDate->toDateString();

            $dateTo =
                $endDate->toDateString();
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan tanggal tidak terbalik
        |--------------------------------------------------------------------------
        */

        if ($startDate->gt($endDate)) {

            [$startDate, $endDate] =
                [$endDate, $startDate];

            $dateFrom =
                $startDate->toDateString();

            $dateTo =
                $endDate->toDateString();
        }

        /*
        |--------------------------------------------------------------------------
        | Filter organisasi
        |--------------------------------------------------------------------------
        */

        $organizationId =
            $request->input(
                'organization_id'
            );

        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {

            $organizationId =
                (int) $organizationId;

            abort_unless(
                $organizationIds->contains(
                    $organizationId
                ),
                403
            );
        } else {

            $organizationId = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Filter jenis transaksi
        |--------------------------------------------------------------------------
        */

        $type =
            $request->input('type');

        if (
            $type !== 'income' &&
            $type !== 'expense'
        ) {

            $type = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Filter kategori
        |--------------------------------------------------------------------------
        */

        $category =
            $request->input('category');

        /*
        |--------------------------------------------------------------------------
        | Query dasar
        |--------------------------------------------------------------------------
        |
        | Hanya transaksi CONFIRMED yang dihitung.
        |
        */

        $baseQuery = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'status',
                'confirmed'
            )
            ->whereBetween(
                'transaction_date',
                [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Terapkan filter organisasi
        |--------------------------------------------------------------------------
        */

        if ($organizationId !== 'all') {

            $baseQuery->where(
                'organization_id',
                $organizationId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Terapkan filter jenis
        |--------------------------------------------------------------------------
        */

        if ($type !== 'all') {

            $baseQuery->where(
                'type',
                $type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Terapkan filter kategori
        |--------------------------------------------------------------------------
        */

        if (
            filled($category) &&
            $category !== 'all'
        ) {

            $baseQuery->where(
                'category',
                $category
            );
        } else {

            $category = 'all';
        }

        /*
|--------------------------------------------------------------------------
| Kategori yang tersedia
|--------------------------------------------------------------------------
|
| Kategori mengikuti:
| - scope organisasi user
| - unit yang dipilih
| - jenis transaksi yang dipilih
| - hanya transaksi confirmed
|
*/

        $categoriesQuery = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'status',
                'confirmed'
            )
            ->whereNotNull('category')
            ->where(
                'category',
                '!=',
                ''
            );


        /*
        |--------------------------------------------------------------------------
        | Filter Unit
        |--------------------------------------------------------------------------
        */

        if ($organizationId !== 'all') {

            $categoriesQuery->where(
                'organization_id',
                $organizationId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Jenis Transaksi
        |--------------------------------------------------------------------------
        */

        if ($type !== 'all') {

            $categoriesQuery->where(
                'type',
                $type
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil kategori
        |--------------------------------------------------------------------------
        */

        $categories = $categoriesQuery
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        /*
|--------------------------------------------------------------------------
| Pastikan kategori yang dipilih masih tersedia
|--------------------------------------------------------------------------
*/

        if (
            $category !== 'all' &&
            ! $categories->contains($category)
        ) {

            $category = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        $summary = (clone $baseQuery)
            ->reorder()
            ->selectRaw("
                SUM(
                    CASE
                        WHEN type = 'income'
                        THEN amount
                        ELSE 0
                    END
                ) AS total_income,

                SUM(
                    CASE
                        WHEN type = 'expense'
                        THEN amount
                        ELSE 0
                    END
                ) AS total_expense
            ")
            ->first();

        $totalIncome =
            (float) ($summary->total_income ?? 0);

        $totalExpense =
            (float) ($summary->total_expense ?? 0);

        $netBalance =
            $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | Grafik perkembangan keuangan
        |--------------------------------------------------------------------------
        |
        | Mengikuti periode filter.
        |
        | Jika rentang <= 31 hari:
        |    grafik berdasarkan tanggal.
        |
        | Jika lebih panjang:
        |    grafik berdasarkan bulan.
        |
        */

        $days =
            $startDate->diffInDays(
                $endDate
            ) + 1;

        if ($days <= 31) {

            $chartRows = (clone $baseQuery)
                ->reorder()
                ->selectRaw("
                    transaction_date AS period,

                    SUM(
                        CASE
                            WHEN type = 'income'
                            THEN amount
                            ELSE 0
                        END
                    ) AS income,

                    SUM(
                        CASE
                            WHEN type = 'expense'
                            THEN amount
                            ELSE 0
                        END
                    ) AS expense
                ")
                ->groupBy(
                    'transaction_date'
                )
                ->orderBy(
                    'transaction_date'
                )
                ->get();

            $chartLabels = [];
            $chartIncome = [];
            $chartExpense = [];

            $cursor =
                $startDate->copy();

            while (
                $cursor->lte($endDate)
            ) {

                $key =
                    $cursor->toDateString();

                $row =
                    $chartRows->first(
                        fn($item) =>
                        (string) $item->period ===
                            $key
                    );

                $chartLabels[] =
                    $cursor->format('d/m');

                $chartIncome[] =
                    (float) (
                        $row->income ?? 0
                    );

                $chartExpense[] =
                    (float) (
                        $row->expense ?? 0
                    );

                $cursor->addDay();
            }

            $chartPeriodType = 'daily';
        } else {

            $chartRows = (clone $baseQuery)
                ->reorder()
                ->selectRaw("
                    DATE_FORMAT(
                        transaction_date,
                        '%Y-%m'
                    ) AS period,

                    SUM(
                        CASE
                            WHEN type = 'income'
                            THEN amount
                            ELSE 0
                        END
                    ) AS income,

                    SUM(
                        CASE
                            WHEN type = 'expense'
                            THEN amount
                            ELSE 0
                        END
                    ) AS expense
                ")
                ->groupBy(
                    'period'
                )
                ->orderBy(
                    'period'
                )
                ->get()
                ->keyBy('period');

            $chartLabels = [];
            $chartIncome = [];
            $chartExpense = [];

            $cursor =
                $startDate->copy()
                ->startOfMonth();

            $lastMonth =
                $endDate->copy()
                ->startOfMonth();

            while (
                $cursor->lte($lastMonth)
            ) {

                $key =
                    $cursor->format('Y-m');

                $row =
                    $chartRows->get($key);

                $chartLabels[] =
                    $cursor->translatedFormat(
                        'M Y'
                    );

                $chartIncome[] =
                    (float) (
                        $row->income ?? 0
                    );

                $chartExpense[] =
                    (float) (
                        $row->expense ?? 0
                    );

                $cursor->addMonth();
            }

            $chartPeriodType = 'monthly';
        }

        /*
        |--------------------------------------------------------------------------
        | Rekap per organisasi
        |--------------------------------------------------------------------------
        */

        $organizationSummary =
            (clone $baseQuery)
            ->reorder()
            ->selectRaw("
                    organization_id,

                    SUM(
                        CASE
                            WHEN type = 'income'
                            THEN amount
                            ELSE 0
                        END
                    ) AS total_income,

                    SUM(
                        CASE
                            WHEN type = 'expense'
                            THEN amount
                            ELSE 0
                        END
                    ) AS total_expense
                ")
            ->groupBy(
                'organization_id'
            )
            ->with('organization:id,name')
            ->get()
            ->map(function ($row) {

                $income =
                    (float) $row->total_income;

                $expense =
                    (float) $row->total_expense;

                return (object) [
                    'organization_id' =>
                    $row->organization_id,

                    'organization' =>
                    $row->organization,

                    'total_income' =>
                    $income,

                    'total_expense' =>
                    $expense,

                    'net_balance' =>
                    $income - $expense,
                ];
            })
            ->sortBy(
                fn($row) =>
                $row->organization?->name
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Data grafik per organisasi
        |--------------------------------------------------------------------------
        */

        $organizationChart =
            $organizationSummary
            ->map(function ($row) {

                return [
                    'name' =>
                    $row->organization?->name
                        ?? 'Tanpa Organisasi',

                    'income' =>
                    $row->total_income,

                    'expense' =>
                    $row->total_expense,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Detail transaksi
        |--------------------------------------------------------------------------
        */

        $transactions =
            (clone $baseQuery)
            ->with([
                'organization',
                'creator',
            ])
            ->orderByDesc(
                'transaction_date'
            )
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Return view
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.reports.finance.index',
            compact(
                'organizations',
                'categories',

                'dateFrom',
                'dateTo',
                'organizationId',
                'type',
                'category',

                'totalIncome',
                'totalExpense',
                'netBalance',

                'chartLabels',
                'chartIncome',
                'chartExpense',
                'chartPeriodType',

                'organizationSummary',
                'organizationChart',

                'transactions'
            )
        );
    }

    /**
     * Menyiapkan data Laporan Keuangan
     * untuk Excel dan PDF.
     */
    private function buildExportData(Request $request): array
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Scope organisasi
    |--------------------------------------------------------------------------
    */

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
    |--------------------------------------------------------------------------
    | Periode
    |--------------------------------------------------------------------------
    */

        $dateFrom = $request->input(
            'date_from',
            now()->startOfMonth()->toDateString()
        );

        $dateTo = $request->input(
            'date_to',
            now()->toDateString()
        );

        try {

            $startDate = Carbon::parse($dateFrom)
                ->startOfDay();

            $endDate = Carbon::parse($dateTo)
                ->endOfDay();
        } catch (\Throwable $e) {

            $startDate = now()
                ->startOfMonth()
                ->startOfDay();

            $endDate = now()
                ->endOfDay();

            $dateFrom =
                $startDate->toDateString();

            $dateTo =
                $endDate->toDateString();
        }

        /*
    |--------------------------------------------------------------------------
    | Jika periode terbalik
    |--------------------------------------------------------------------------
    */

        if ($startDate->gt($endDate)) {

            [$startDate, $endDate] =
                [$endDate, $startDate];

            $dateFrom =
                $startDate->toDateString();

            $dateTo =
                $endDate->toDateString();
        }

        /*
    |--------------------------------------------------------------------------
    | Organisasi
    |--------------------------------------------------------------------------
    */

        $organizationId =
            $request->input('organization_id');

        $organization = null;

        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {

            $organizationId =
                (int) $organizationId;

            abort_unless(
                $organizationIds->contains(
                    $organizationId
                ),
                403
            );

            $organization =
                Organization::find(
                    $organizationId
                );
        } else {

            $organizationId = 'all';
        }

        /*
    |--------------------------------------------------------------------------
    | Jenis transaksi
    |--------------------------------------------------------------------------
    */

        $type =
            $request->input('type');

        if (
            $type !== 'income' &&
            $type !== 'expense'
        ) {

            $type = 'all';
        }

        /*
    |--------------------------------------------------------------------------
    | Kategori
    |--------------------------------------------------------------------------
    */

        $category =
            $request->input('category');

        if (
            ! filled($category) ||
            $category === 'all'
        ) {

            $category = 'all';
        }

        /*
    |--------------------------------------------------------------------------
    | Query dasar
    |--------------------------------------------------------------------------
    */

        $query = FinanceTransaction::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'status',
                'confirmed'
            )
            ->whereBetween(
                'transaction_date',
                [
                    $startDate->toDateString(),
                    $endDate->toDateString(),
                ]
            );

        /*
    |--------------------------------------------------------------------------
    | Filter organisasi
    |--------------------------------------------------------------------------
    */

        if ($organizationId !== 'all') {

            $query->where(
                'organization_id',
                $organizationId
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter jenis
    |--------------------------------------------------------------------------
    */

        if ($type !== 'all') {

            $query->where(
                'type',
                $type
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter kategori
    |--------------------------------------------------------------------------
    */

        if ($category !== 'all') {

            $query->where(
                'category',
                $category
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Ringkasan
    |--------------------------------------------------------------------------
    */

        $summary = (clone $query)
            ->reorder()
            ->selectRaw("
            SUM(
                CASE
                    WHEN type = 'income'
                    THEN amount
                    ELSE 0
                END
            ) AS total_income,

            SUM(
                CASE
                    WHEN type = 'expense'
                    THEN amount
                    ELSE 0
                END
            ) AS total_expense
        ")
            ->first();

        $totalIncome =
            (float) ($summary->total_income ?? 0);

        $totalExpense =
            (float) ($summary->total_expense ?? 0);

        $netBalance =
            $totalIncome - $totalExpense;

        /*
    |--------------------------------------------------------------------------
    | Rekap organisasi
    |--------------------------------------------------------------------------
    */

        $organizationSummary =
            (clone $query)
            ->reorder()
            ->selectRaw("
                organization_id,

                SUM(
                    CASE
                        WHEN type = 'income'
                        THEN amount
                        ELSE 0
                    END
                ) AS total_income,

                SUM(
                    CASE
                        WHEN type = 'expense'
                        THEN amount
                        ELSE 0
                    END
                ) AS total_expense
            ")
            ->groupBy(
                'organization_id'
            )
            ->with(
                'organization:id,name'
            )
            ->get()
            ->map(function ($row) {

                $income =
                    (float) $row->total_income;

                $expense =
                    (float) $row->total_expense;

                return (object) [
                    'organization' =>
                    $row->organization,

                    'total_income' =>
                    $income,

                    'total_expense' =>
                    $expense,

                    'net_balance' =>
                    $income - $expense,
                ];
            })
            ->sortBy(
                fn($row) =>
                $row->organization?->name
            )
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Detail transaksi
    |--------------------------------------------------------------------------
    |
    | Export tidak menggunakan pagination.
    |
    */

        $transactions =
            (clone $query)
            ->with([
                'organization',
                'creator',
            ])
            ->orderBy(
                'transaction_date'
            )
            ->orderBy('id')
            ->get();

        return [
            'transactions' =>
            $transactions,

            'organizationSummary' =>
            $organizationSummary,

            'totalIncome' =>
            $totalIncome,

            'totalExpense' =>
            $totalExpense,

            'netBalance' =>
            $netBalance,

            'dateFrom' =>
            $dateFrom,

            'dateTo' =>
            $dateTo,

            'organization' =>
            $organization,

            'type' =>
            $type,

            'category' =>
            $category,
        ];
    }


    /**
     * Menyiapkan data laporan untuk PDF
     * beserta data grafik.
     */
    private function buildReportData(Request $request): array
    {
        /*
    |--------------------------------------------------------------------------
    | Gunakan data export yang sudah memiliki
    | filter yang sama
    |--------------------------------------------------------------------------
    */

        $data = $this->buildExportData($request);

        /*
    |--------------------------------------------------------------------------
    | Tanggal Carbon
    |--------------------------------------------------------------------------
    */

        $startDate = Carbon::parse(
            $data['dateFrom']
        )->startOfDay();

        $endDate = Carbon::parse(
            $data['dateTo']
        )->endOfDay();

        /*
    |--------------------------------------------------------------------------
    | Transaksi hasil filter
    |--------------------------------------------------------------------------
    */

        $transactions =
            collect($data['transactions']);

        /*
    |--------------------------------------------------------------------------
    | Grafik tren
    |--------------------------------------------------------------------------
    */

        $days =
            $startDate->copy()
            ->startOfDay()
            ->diffInDays(
                $endDate->copy()
                    ->startOfDay()
            ) + 1;

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        /*
    |--------------------------------------------------------------------------
    | Grafik harian
    |--------------------------------------------------------------------------
    */

        if ($days <= 31) {

            $grouped =
                $transactions
                ->groupBy(function ($transaction) {

                    return Carbon::parse(
                        $transaction->transaction_date
                    )->toDateString();
                });

            $cursor =
                $startDate->copy()
                ->startOfDay();

            $lastDate =
                $endDate->copy()
                ->startOfDay();

            while (
                $cursor->lte($lastDate)
            ) {

                $key =
                    $cursor->toDateString();

                $rows =
                    $grouped->get(
                        $key,
                        collect()
                    );

                $chartLabels[] =
                    $cursor->format('d/m');

                $chartIncome[] =
                    (float) $rows
                        ->where(
                            'type',
                            'income'
                        )
                        ->sum('amount');

                $chartExpense[] =
                    (float) $rows
                        ->where(
                            'type',
                            'expense'
                        )
                        ->sum('amount');

                $cursor->addDay();
            }

            $chartPeriodType = 'daily';
        } else {

            /*
        |--------------------------------------------------------------------------
        | Grafik bulanan
        |--------------------------------------------------------------------------
        */

            $grouped =
                $transactions
                ->groupBy(function ($transaction) {

                    return Carbon::parse(
                        $transaction->transaction_date
                    )->format('Y-m');
                });

            $cursor =
                $startDate->copy()
                ->startOfMonth();

            $lastMonth =
                $endDate->copy()
                ->startOfMonth();

            while (
                $cursor->lte($lastMonth)
            ) {

                $key =
                    $cursor->format('Y-m');

                $rows =
                    $grouped->get(
                        $key,
                        collect()
                    );

                $chartLabels[] =
                    $cursor->translatedFormat(
                        'M Y'
                    );

                $chartIncome[] =
                    (float) $rows
                        ->where(
                            'type',
                            'income'
                        )
                        ->sum('amount');

                $chartExpense[] =
                    (float) $rows
                        ->where(
                            'type',
                            'expense'
                        )
                        ->sum('amount');

                $cursor->addMonth();
            }

            $chartPeriodType = 'monthly';
        }

        /*
    |--------------------------------------------------------------------------
    | Grafik per organisasi
    |--------------------------------------------------------------------------
    */

        $organizationChart =
            collect($data['organizationSummary'])
            ->map(function ($row) {

                return [
                    'name' =>
                    $row->organization?->name
                        ?? 'Tanpa Organisasi',

                    'income' =>
                    (float)
                    $row->total_income,

                    'expense' =>
                    (float)
                    $row->total_expense,
                ];
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Data yang dibutuhkan PDF
    |--------------------------------------------------------------------------
    */

        return [
            'transactions' =>
            $data['transactions'],

            'organizationSummary' =>
            $data['organizationSummary'],

            'totalIncome' =>
            $data['totalIncome'],

            'totalExpense' =>
            $data['totalExpense'],

            'netBalance' =>
            $data['netBalance'],

            'dateFrom' =>
            $data['dateFrom'],

            'dateTo' =>
            $data['dateTo'],

            'startDate' =>
            $startDate,

            'endDate' =>
            $endDate,

            /*
        | PDF menggunakan nama ini
        */

            'selectedOrganization' =>
            $data['organization'],

            'type' =>
            $data['type'],

            'category' =>
            $data['category'],

            /*
        | Data grafik
        */

            'chartLabels' =>
            $chartLabels,

            'chartIncome' =>
            $chartIncome,

            'chartExpense' =>
            $chartExpense,

            'chartPeriodType' =>
            $chartPeriodType,

            'organizationChart' =>
            $organizationChart,
        ];
    }


    /**
     * Export Laporan Keuangan ke Excel.
     */
    public function excel(Request $request)
    {
        $data =
            $this->buildExportData(
                $request
            );

        $fileName =
            'laporan-keuangan-' .
            $data['dateFrom'] .
            '-sampai-' .
            $data['dateTo'] .
            '.xlsx';

        return Excel::download(
            new FinanceReportExport(
                $data['transactions'],
                $data['organizationSummary'],
                $data['totalIncome'],
                $data['totalExpense'],
                $data['netBalance'],
                $data['dateFrom'],
                $data['dateTo'],
                $data['organization'],
                $data['type'],
                $data['category'],
            ),
            $fileName
        );
    }


    /**
     * Export Laporan Keuangan ke PDF.
     */
    /**
     * Export Laporan Keuangan ke PDF.
     */
    public function pdf(Request $request)
    {
        $data =
            $this->buildReportData(
                $request
            );

        /*
|--------------------------------------------------------------------------
| Profil Induk untuk Kop
|--------------------------------------------------------------------------
*/

        $data['induk'] =
            Organization::where(
                'type',
                'induk'
            )->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | Grafik untuk PDF
    |--------------------------------------------------------------------------
    */

        $data['trendChartImage'] =
            $this->buildTrendChartImage(
                $data['chartLabels'],
                $data['chartIncome'],
                $data['chartExpense']
            );

        $data['organizationChartImage'] =
            $this->buildOrganizationChartImage(
                $data['organizationChart']
            );

        /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

        $pdf = Pdf::loadView(
            'admin.reports.finance.exports.pdf',
            $data
        )->setPaper(
            'a4',
            'landscape'
        );

        $fileName =
            'laporan-keuangan-' .
            $data['startDate']->format('Y-m-d') .
            '-sd-' .
            $data['endDate']->format('Y-m-d') .
            '.pdf';

        return $pdf->download(
            $fileName
        );
    }

    private function buildTrendChartImage(
        array $labels,
        array $income,
        array $expense
    ): string {
        $width = 1400;
        $height = 500;

        $left = 100;
        $right = 50;
        $top = 70;
        $bottom = 90;

        $chartWidth =
            $width - $left - $right;

        $chartHeight =
            $height - $top - $bottom;

        $image =
            imagecreatetruecolor(
                $width,
                $height
            );

        /*
    |--------------------------------------------------------------------------
    | Background
    |--------------------------------------------------------------------------
    */

        $white =
            imagecolorallocate(
                $image,
                255,
                255,
                255
            );

        $black =
            imagecolorallocate(
                $image,
                40,
                40,
                40
            );

        $gray =
            imagecolorallocate(
                $image,
                220,
                220,
                220
            );

        $green =
            imagecolorallocate(
                $image,
                25,
                135,
                84
            );

        $red =
            imagecolorallocate(
                $image,
                220,
                53,
                69
            );

        imagefill(
            $image,
            0,
            0,
            $white
        );

        /*
    |--------------------------------------------------------------------------
    | Judul
    |--------------------------------------------------------------------------
    */

        imagestring(
            $image,
            5,
            500,
            20,
            'Tren Pemasukan dan Pengeluaran',
            $black
        );

        /*
    |--------------------------------------------------------------------------
    | Nilai maksimum
    |--------------------------------------------------------------------------
    */

        $maxValue = max(
            max($income ?: [0]),
            max($expense ?: [0]),
            1
        );

        /*
    |--------------------------------------------------------------------------
    | Grid
    |--------------------------------------------------------------------------
    */

        $gridCount = 5;

        for ($i = 0; $i <= $gridCount; $i++) {

            $gridY =
                $top +
                (
                    $chartHeight *
                    $i /
                    $gridCount
                );

            imageline(
                $image,
                $left,
                $gridY,
                $width - $right,
                $gridY,
                $gray
            );

            $value =
                $maxValue *
                (
                    1 -
                    ($i / $gridCount)
                );

            imagestring(
                $image,
                3,
                10,
                $gridY - 7,
                number_format(
                    $value,
                    0,
                    ',',
                    '.'
                ),
                $black
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Sumbu
    |--------------------------------------------------------------------------
    */

        imageline(
            $image,
            $left,
            $top,
            $left,
            $top + $chartHeight,
            $black
        );

        imageline(
            $image,
            $left,
            $top + $chartHeight,
            $width - $right,
            $top + $chartHeight,
            $black
        );

        /*
    |--------------------------------------------------------------------------
    | Fungsi koordinat Y
    |--------------------------------------------------------------------------
    */

        $getY = function ($value) use (
            $top,
            $chartHeight,
            $maxValue
        ) {
            return (int) (
                $top +
                $chartHeight -
                (
                    ($value / $maxValue)
                    * $chartHeight
                )
            );
        };

        $count =
            count($labels);

        if ($count > 1) {

            $stepX =
                $chartWidth /
                ($count - 1);
        } else {

            $stepX = 0;
        }

        /*
    |--------------------------------------------------------------------------
    | Grafik garis
    |--------------------------------------------------------------------------
    */

        $drawLine = function (
            array $values,
            $lineColor
        ) use (
            $image,
            $left,
            $stepX,
            $getY,
            $count
        ) {

            if ($count === 0) {
                return;
            }

            $previousX = null;
            $previousY = null;

            foreach (
                $values as $index => $value
            ) {

                $x =
                    (int) (
                        $left +
                        ($index * $stepX)
                    );

                $y =
                    $getY(
                        (float) $value
                    );

                /*
            | Titik
            */

                imagefilledellipse(
                    $image,
                    $x,
                    $y,
                    8,
                    8,
                    $lineColor
                );

                /*
            | Garis
            */

                if (
                    $previousX !== null &&
                    $previousY !== null
                ) {

                    imageline(
                        $image,
                        $previousX,
                        $previousY,
                        $x,
                        $y,
                        $lineColor
                    );

                    imageline(
                        $image,
                        $previousX,
                        $previousY + 1,
                        $x,
                        $y + 1,
                        $lineColor
                    );
                }

                $previousX = $x;
                $previousY = $y;
            }
        };

        $drawLine(
            $income,
            $green
        );

        $drawLine(
            $expense,
            $red
        );

        /*
    |--------------------------------------------------------------------------
    | Label periode
    |--------------------------------------------------------------------------
    */

        if ($count > 0) {

            $labelStep =
                max(
                    1,
                    (int) ceil($count / 12)
                );

            foreach (
                $labels as $index => $label
            ) {

                if (
                    $index % $labelStep !== 0 &&
                    $index !== $count - 1
                ) {
                    continue;
                }

                $x =
                    (int) (
                        $left +
                        ($index * $stepX)
                    );

                imagestring(
                    $image,
                    3,
                    $x - 20,
                    $top + $chartHeight + 15,
                    (string) $label,
                    $black
                );
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Legend
    |--------------------------------------------------------------------------
    */

        imagefilledrectangle(
            $image,
            1050,
            25,
            1070,
            45,
            $green
        );

        imagestring(
            $image,
            3,
            1080,
            27,
            'Pemasukan',
            $black
        );

        imagefilledrectangle(
            $image,
            1200,
            25,
            1220,
            45,
            $red
        );

        imagestring(
            $image,
            3,
            1230,
            27,
            'Pengeluaran',
            $black
        );

        /*
    |--------------------------------------------------------------------------
    | Output PNG -> Base64
    |--------------------------------------------------------------------------
    */

        ob_start();

        imagepng(
            $image,
            null,
            6
        );

        $imageData =
            ob_get_clean();

        imagedestroy(
            $image
        );

        return
            'data:image/png;base64,' .
            base64_encode(
                $imageData
            );
    }

    private function buildOrganizationChartImage(
        Collection $organizationChart
    ): string {
        $width = 1400;
        $height = 550;

        $left = 100;
        $right = 50;
        $top = 80;
        $bottom = 130;

        $chartWidth =
            $width - $left - $right;

        $chartHeight =
            $height - $top - $bottom;

        $image =
            imagecreatetruecolor(
                $width,
                $height
            );

        /*
    |--------------------------------------------------------------------------
    | Warna
    |--------------------------------------------------------------------------
    */

        $white =
            imagecolorallocate(
                $image,
                255,
                255,
                255
            );

        $black =
            imagecolorallocate(
                $image,
                40,
                40,
                40
            );

        $gray =
            imagecolorallocate(
                $image,
                220,
                220,
                220
            );

        $green =
            imagecolorallocate(
                $image,
                25,
                135,
                84
            );

        $red =
            imagecolorallocate(
                $image,
                220,
                53,
                69
            );

        imagefill(
            $image,
            0,
            0,
            $white
        );

        /*
    |--------------------------------------------------------------------------
    | Judul
    |--------------------------------------------------------------------------
    */

        imagestring(
            $image,
            5,
            550,
            25,
            'Keuangan per Unit',
            $black
        );

        /*
    |--------------------------------------------------------------------------
    | Tidak ada data
    |--------------------------------------------------------------------------
    */

        $count =
            $organizationChart->count();

        if ($count === 0) {

            imagestring(
                $image,
                4,
                600,
                250,
                'Tidak ada data per unit',
                $black
            );

            ob_start();

            imagepng(
                $image
            );

            $imageData =
                ob_get_clean();

            imagedestroy(
                $image
            );

            return
                'data:image/png;base64,' .
                base64_encode(
                    $imageData
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Nilai maksimum
    |--------------------------------------------------------------------------
    */

        $maxValue = max(
            $organizationChart
                ->map(
                    fn($row) =>
                    max(
                        (float) $row['income'],
                        (float) $row['expense']
                    )
                )
                ->max(),
            1
        );

        /*
    |--------------------------------------------------------------------------
    | Grid
    |--------------------------------------------------------------------------
    */

        $gridCount = 5;

        for ($i = 0; $i <= $gridCount; $i++) {

            $gridY =
                $top +
                (
                    $chartHeight *
                    $i /
                    $gridCount
                );

            imageline(
                $image,
                $left,
                $gridY,
                $width - $right,
                $gridY,
                $gray
            );

            $value =
                $maxValue *
                (
                    1 -
                    ($i / $gridCount)
                );

            imagestring(
                $image,
                3,
                10,
                $gridY - 7,
                number_format(
                    $value,
                    0,
                    ',',
                    '.'
                ),
                $black
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Sumbu
    |--------------------------------------------------------------------------
    */

        imageline(
            $image,
            $left,
            $top,
            $left,
            $top + $chartHeight,
            $black
        );

        imageline(
            $image,
            $left,
            $top + $chartHeight,
            $width - $right,
            $top + $chartHeight,
            $black
        );

        /*
    |--------------------------------------------------------------------------
    | Lebar group
    |--------------------------------------------------------------------------
    */

        $groupWidth =
            $chartWidth / $count;

        $barWidth =
            min(
                45,
                $groupWidth / 4
            );

        /*
    |--------------------------------------------------------------------------
    | Batang
    |--------------------------------------------------------------------------
    */

        foreach (
            $organizationChart as $index => $row
        ) {

            $centerX =
                $left +
                ($groupWidth * $index) +
                ($groupWidth / 2);

            $income =
                (float) $row['income'];

            $expense =
                (float) $row['expense'];

            $incomeHeight =
                (
                    $income /
                    $maxValue
                ) *
                $chartHeight;

            $expenseHeight =
                (
                    $expense /
                    $maxValue
                ) *
                $chartHeight;

            $incomeX =
                $centerX -
                $barWidth -
                5;

            $expenseX =
                $centerX +
                5;

            $incomeY =
                $top +
                $chartHeight -
                $incomeHeight;

            $expenseY =
                $top +
                $chartHeight -
                $expenseHeight;

            /*
        | Pemasukan
        */

            imagefilledrectangle(
                $image,
                $incomeX,
                $incomeY,
                $incomeX + $barWidth,
                $top + $chartHeight,
                $green
            );

            /*
        | Pengeluaran
        */

            imagefilledrectangle(
                $image,
                $expenseX,
                $expenseY,
                $expenseX + $barWidth,
                $top + $chartHeight,
                $red
            );

            /*
        | Nama unit
        */

            $name =
                (string) (
                    $row['name']
                    ?? 'Unit'
                );

            /*
        | Batasi panjang label agar tidak
        | bertabrakan.
        */

            if (
                strlen($name) > 18
            ) {

                $name =
                    substr(
                        $name,
                        0,
                        18
                    ) . '...';
            }

            imagestring(
                $image,
                3,
                $centerX - 40,
                $top + $chartHeight + 20,
                $name,
                $black
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Legend
    |--------------------------------------------------------------------------
    */

        imagefilledrectangle(
            $image,
            1050,
            45,
            1070,
            65,
            $green
        );

        imagestring(
            $image,
            3,
            1080,
            47,
            'Pemasukan',
            $black
        );

        imagefilledrectangle(
            $image,
            1200,
            45,
            1220,
            65,
            $red
        );

        imagestring(
            $image,
            3,
            1230,
            47,
            'Pengeluaran',
            $black
        );

        /*
    |--------------------------------------------------------------------------
    | Output PNG -> Base64
    |--------------------------------------------------------------------------
    */

        ob_start();

        imagepng(
            $image,
            null,
            6
        );

        $imageData =
            ob_get_clean();

        imagedestroy(
            $image
        );

        return
            'data:image/png;base64,' .
            base64_encode(
                $imageData
            );
    }

    private function formatChartValue(
        float $value
    ): string {

        if ($value >= 1000000000) {

            return number_format(
                $value / 1000000000,
                1,
                ',',
                '.'
            ) . ' M';
        }

        if ($value >= 1000000) {

            return number_format(
                $value / 1000000,
                1,
                ',',
                '.'
            ) . ' jt';
        }

        if ($value >= 1000) {

            return number_format(
                $value / 1000,
                0,
                ',',
                '.'
            ) . ' rb';
        }

        return number_format(
            $value,
            0,
            ',',
            '.'
        );
    }

    public function categories(Request $request)
    {
        $user = $request->user();

        $organizationIds = Organization::accessibleIdsForUser($user);

        $organizationId = $request->input('organization_id', 'all');
        $type = $request->input('type', 'all');

        $query = FinanceTransaction::query()
            ->whereIn('organization_id', $organizationIds)
            ->where('status', 'confirmed')
            ->whereNotNull('category')
            ->where('category', '!=', '');

        if ($organizationId !== 'all') {
            $query->where(
                'organization_id',
                $organizationId
            );
        }

        if ($type !== 'all') {
            $query->where(
                'type',
                $type
            );
        }

        $categories = $query
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values()
            ->toArray();

        return response()->json($categories);
    }
}
