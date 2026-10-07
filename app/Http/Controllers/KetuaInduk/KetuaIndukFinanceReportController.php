<?php

namespace App\Http\Controllers\KetuaInduk;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KetuaIndukFinanceReportController extends Controller
{
    /**
     * Laporan keuangan Ketua Induk.
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
        | Daftar Unit
        |--------------------------------------------------------------------------
        |
        | Ketua Induk dapat melihat seluruh organisasi
        | yang berada dalam scope-nya.
        |
        */

        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

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

            $dateFrom = $startDate->toDateString();
            $dateTo = $endDate->toDateString();
        }

        /*
        |--------------------------------------------------------------------------
        | Jika tanggal terbalik
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
        | Filter Unit
        |--------------------------------------------------------------------------
        */

        $organizationId =
            $request->input('organization_id', 'all');

        if (
            $organizationId !== 'all'
            && $organizationId !== null
        ) {
            $organizationId = (int) $organizationId;

            abort_unless(
                $organizationIds->contains($organizationId),
                403
            );
        } else {
            $organizationId = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Filter kategori laporan
        |--------------------------------------------------------------------------
        |
        | all     = semua
        | income  = pemasukan
        | expense = pengeluaran
        | deposit = setoran
        |
        */

        $reportType =
            $request->input('report_type', 'all');

        if (! in_array(
            $reportType,
            ['all', 'income', 'expense', 'deposit'],
            true
        )) {
            $reportType = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Query dasar
        |--------------------------------------------------------------------------
        |
        | Hanya transaksi CONFIRMED.
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
        | Filter Unit
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
        | Filter jenis laporan
        |--------------------------------------------------------------------------
        */

        if ($reportType === 'income') {

            $baseQuery->where(
                'type',
                'income'
            );
        } elseif ($reportType === 'expense') {

            $baseQuery->where(
                'type',
                'expense'
            );
        } elseif ($reportType === 'deposit') {

            $baseQuery->where(
                'source_type',
                'deposit'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        // Total setoran internal
        $totalDeposit = (float) (clone $baseQuery)
            ->where(
                'source_type',
                'deposit'
            )
            ->where(
                'type',
                'expense'
            )
            ->sum('amount');

        // Total pemasukan
        $totalIncome = (float) (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        // Total pengeluaran
        $totalExpense = (float) (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        // Saldo
        $netBalance = $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | Rekap per Unit
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
                ) AS total_expense,

                SUM(
                    CASE
                        WHEN source_type = 'deposit'
                            AND type = 'expense'
                        THEN amount
                        ELSE 0
                    END
                ) AS total_deposit
            ")
            ->groupBy('organization_id')
            ->with('organization:id,name,type')
            ->get()
            ->map(function ($row) {

                $income = (float) $row->total_income;
                $expense = (float) $row->total_expense;
                $deposit = (float) $row->total_deposit;

                return (object) [
                    'organization_id' => $row->organization_id,

                    'organization' => $row->organization,

                    'total_income' => $income,

                    'total_expense' => $expense,

                    'total_deposit' => $deposit,

                    'net_balance' => $income - $expense,
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
        */

        $transactions =
            (clone $baseQuery)
            ->with([
                'organization',
                'creator',
            ])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();


        $induk = Organization::where(
            'type',
            'induk'
        )->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'ketua-induk.reports.finance.index',
            compact(
                'induk',
                'organizations',
                'dateFrom',
                'dateTo',
                'organizationId',
                'reportType',
                'totalIncome',
                'totalExpense',
                'totalDeposit',
                'netBalance',
                'organizationSummary',
                'transactions'
            )
        );
    }


    /**
     * Cetak laporan keuangan Ketua Induk ke PDF.
     */
    public function pdf(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Scope organisasi
        |--------------------------------------------------------------------------
        */

        $organizationIds =
            Organization::accessibleIdsForUser($user);


        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

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

            $dateFrom = $startDate->toDateString();
            $dateTo = $endDate->toDateString();
        }

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
        | Filter Unit
        |--------------------------------------------------------------------------
        */

        $organizationId =
            $request->input('organization_id', 'all');

        if (
            $organizationId !== 'all'
            && $organizationId !== null
        ) {
            $organizationId = (int) $organizationId;

            abort_unless(
                $organizationIds->contains($organizationId),
                403
            );

            $organization =
                Organization::findOrFail(
                    $organizationId
                );
        } else {
            $organizationId = 'all';
            $organization = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Filter kategori
        |--------------------------------------------------------------------------
        */

        $reportType =
            $request->input('report_type', 'all');

        if (! in_array(
            $reportType,
            ['all', 'income', 'expense', 'deposit'],
            true
        )) {
            $reportType = 'all';
        }

        /*
        |--------------------------------------------------------------------------
        | Query transaksi
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

        if ($organizationId !== 'all') {
            $query->where(
                'organization_id',
                $organizationId
            );
        }

        if ($reportType === 'income') {

            $query->where(
                'type',
                'income'
            );
        } elseif ($reportType === 'expense') {

            $query->where(
                'type',
                'expense'
            );
        } elseif ($reportType === 'deposit') {

            $query->where(
                'source_type',
                'deposit'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Data transaksi
        |--------------------------------------------------------------------------
        */

        $transactions =
            $query
            ->with([
                'organization',
                'creator',
            ])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        $totalDeposit =
            (float) $transactions
                ->where('source_type', 'deposit')
                ->where('type', 'expense')
                ->sum('amount');

        $totalIncome =
            (float) $transactions
                ->where('type', 'income')
                ->sum('amount');

        $totalExpense =
            (float) $transactions
                ->where('type', 'expense')
                ->sum('amount');

        $netBalance =
            $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | Rekap per Unit
        |--------------------------------------------------------------------------
        */

        $organizationSummary =
            $transactions
            ->groupBy('organization_id')
            ->map(function ($rows) {

                $organization =
                    $rows->first()?->organization;

                $income =
                    (float) $rows
                        ->where('type', 'income')
                        ->sum('amount');

                $expense =
                    (float) $rows
                        ->where('type', 'expense')
                        ->sum('amount');

                $deposit =
                    (float) $rows
                        ->where(
                            'source_type',
                            'deposit'
                        )
                        ->where(
                            'type',
                            'expense'
                        )
                        ->sum('amount');

                return (object) [
                    'organization' =>
                    $organization,

                    'total_income' =>
                    $income,

                    'total_expense' =>
                    $expense,

                    'total_deposit' =>
                    $deposit,

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
        | Profil Induk
        |--------------------------------------------------------------------------
        */

        $induk =
            Organization::where(
                'type',
                'induk'
            )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'ketua-induk.reports.finance.pdf',
            compact(
                'transactions',
                'organizationSummary',
                'totalIncome',
                'totalExpense',
                'totalDeposit',
                'netBalance',
                'dateFrom',
                'dateTo',
                'organization',
                'organizationId',
                'reportType',
                'induk',
                'organizations'
            )
        )->setPaper(
            'a4',
            'landscape'
        );

        $fileName =
            'laporan-keuangan-ketua-induk-' .
            $startDate->format('Y-m-d') .
            '-sd-' .
            $endDate->format('Y-m-d') .
            '.pdf';

        return $pdf->download($fileName);
    }
}
