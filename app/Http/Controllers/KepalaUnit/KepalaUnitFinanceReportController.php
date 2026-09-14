<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KepalaUnitFinanceReportController extends Controller
{
    /**
     * Laporan keuangan unit Kepala Unit.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi Kepala Unit
        |--------------------------------------------------------------------------
        |
        | Kepala Unit hanya dapat melihat organisasi yang menjadi scope-nya.
        |
        */

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
        |--------------------------------------------------------------------------
        | Pastikan hanya Unit
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->where('type', 'unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Unit Kepala Unit
        |--------------------------------------------------------------------------
        |
        | Jika user hanya memiliki satu Unit, langsung gunakan Unit tersebut.
        | Jika memiliki lebih dari satu scope Unit, user dapat memilih Unit
        | dari dropdown.
        |
        */

        $organizationId =
            $request->input('organization_id');

        if ($organizationId !== null) {

            $organizationId = (int) $organizationId;

            abort_unless(
                $organizationIds->contains($organizationId),
                403
            );

            $organization = Organization::query()
                ->where('id', $organizationId)
                ->where('type', 'unit')
                ->where('is_active', true)
                ->firstOrFail();
        } else {

            $organization = $organizations->first();

            abort_unless(
                $organization,
                403
            );

            $organizationId = $organization->id;
        }

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
        | Query dasar
        |--------------------------------------------------------------------------
        */

        $baseQuery = FinanceTransaction::query()
            ->where(
                'organization_id',
                $organizationId
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
        | Filter jenis laporan
        |--------------------------------------------------------------------------
        */

        if ($reportType === 'income') {

            $baseQuery
                ->where('type', 'income')
                ->where(
                    'source_type',
                    '!=',
                    'deposit'
                );
        } elseif ($reportType === 'expense') {

            $baseQuery
                ->where('type', 'expense')
                ->where(
                    'source_type',
                    '!=',
                    'deposit'
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

        $totalDeposit = (float) (clone $baseQuery)
            ->where(
                'source_type',
                'deposit'
            )
            ->sum('amount');

        $depositCount = (clone $baseQuery)
            ->where(
                'source_type',
                'deposit'
            )
            ->count();

        $totalIncome = (float) (clone $baseQuery)
            ->where('type', 'income')
            ->where(
                'source_type',
                '!=',
                'deposit'
            )
            ->sum('amount');

        $totalExpense = (float) (clone $baseQuery)
            ->where('type', 'expense')
            ->where(
                'source_type',
                '!=',
                'deposit'
            )
            ->sum('amount');

        $netBalance =
            $totalIncome - $totalExpense;

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
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'kepala-unit.reports.finance.index',
            compact(
                'induk',
                'organization',
                'organizations',
                'organizationId',
                'dateFrom',
                'dateTo',
                'reportType',
                'totalIncome',
                'totalExpense',
                'totalDeposit',
                'depositCount',
                'netBalance',
                'transactions'
            )
        );
    }

    /**
     * Cetak laporan keuangan dalam PDF.
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
            ->where('type', 'unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Unit
        |--------------------------------------------------------------------------
        */

        $organizationId =
            $request->input('organization_id');

        if ($organizationId !== null) {

            $organizationId = (int) $organizationId;

            abort_unless(
                $organizationIds->contains($organizationId),
                403
            );

            $organization = Organization::query()
                ->where('id', $organizationId)
                ->where('type', 'unit')
                ->where('is_active', true)
                ->firstOrFail();
        } else {

            $organization = $organizations->first();

            abort_unless(
                $organization,
                403
            );

            $organizationId = $organization->id;
        }

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
            ->where(
                'organization_id',
                $organizationId
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

        if ($reportType === 'income') {

            $query
                ->where('type', 'income')
                ->where(
                    'source_type',
                    '!=',
                    'deposit'
                );
        } elseif ($reportType === 'expense') {

            $query
                ->where('type', 'expense')
                ->where(
                    'source_type',
                    '!=',
                    'deposit'
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
                ->where(
                    'source_type',
                    'deposit'
                )
                ->sum('amount');

        $totalIncome =
            (float) $transactions
                ->where('type', 'income')
                ->where(
                    'source_type',
                    '!=',
                    'deposit'
                )
                ->sum('amount');

        $totalExpense =
            (float) $transactions
                ->where('type', 'expense')
                ->where(
                    'source_type',
                    '!=',
                    'deposit'
                )
                ->sum('amount');

        $netBalance =
            $totalIncome - $totalExpense;

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
        | Kepala Unit
        |--------------------------------------------------------------------------
        */

        $kepalaUnit = $organization->users()
            ->whereHas('roles', function ($query) {
                $query->where('code', 'kepala_unit');
            })
            ->where('is_active', true)
            ->first();

        $namaKepalaUnit = $kepalaUnit?->name ?? '-';

        // Bendahara Unit
        $bendaharaUnit = $organization->users()
            ->whereHas('roles', function ($query) {
                $query->where('code', 'bendahara_unit');
            })
            ->where('is_active', true)
            ->first();

        $namaBendaharaUnit = $bendaharaUnit?->name ?? '-';

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'kepala-unit.reports.finance.pdf',
            compact(
                'induk',
                'organization',
                'organizations',
                'organizationId',
                'dateFrom',
                'dateTo',
                'reportType',
                'transactions',
                'totalIncome',
                'totalExpense',
                'totalDeposit',
                'netBalance',
                'namaKepalaUnit',
                'namaBendaharaUnit'
            )
        )->setPaper(
            'a4',
            'landscape'
        );

        $fileName =
            'laporan-keuangan-' .
            $organization->code .
            '-' .
            $startDate->format('Y-m-d') .
            '-sd-' .
            $endDate->format('Y-m-d') .
            '.pdf';

        return $pdf->download($fileName);
    }
}
