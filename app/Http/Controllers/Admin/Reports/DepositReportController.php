<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Exports\DepositReportExport;
use App\Http\Controllers\Controller;
use App\Models\FinanceDeposit;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DepositReportController extends Controller
{
    /**
     * Laporan Setoran.
     */
    public function index(Request $request)
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
        | Filter
        |--------------------------------------------------------------------------
        */

        $status = $request->input('status');
        $year = $request->input('year');
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'status' => [
                'nullable',
                'in:pending,confirmed,rejected',
            ],

            'year' => [
                'nullable',
                'integer',
                'min:2000',
                'max:2100',
            ],

            'month' => [
                'nullable',
                'integer',
                'between:1,12',
            ],

            'date_from' => [
                'nullable',
                'date',
            ],

            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],

            'search' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = FinanceDeposit::query()
            ->with([
                'organization',
                'targetOrganization',
                'creator',
                'confirmer',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )

            ->when($status, function ($query) use ($status) {
                $query->where(
                    'status',
                    $status
                );
            })

            ->when($year, function ($query) use ($year) {
                $query->whereYear(
                    'deposit_date',
                    $year
                );
            })

            ->when($month, function ($query) use ($month) {
                $query->whereMonth(
                    'deposit_date',
                    $month
                );
            })

            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate(
                    'deposit_date',
                    '>=',
                    $dateFrom
                );
            })

            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate(
                    'deposit_date',
                    '<=',
                    $dateTo
                );
            })

            ->when($search, function ($query) use ($search) {

                $query->where(function ($query) use ($search) {

                    $query->whereHas(
                        'organization',
                        function ($query) use ($search) {

                            $query->where(
                                'name',
                                'like',
                                '%' . $search . '%'
                            );
                        }
                    )

                        ->orWhereHas(
                            'targetOrganization',
                            function ($query) use ($search) {

                                $query->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                );
                            }
                        )

                        ->orWhere(
                            'description',
                            'like',
                            '%' . $search . '%'
                        );
                });
            });

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalDeposits = (clone $query)->count();

        $totalAmount = (clone $query)->sum('amount');

        $confirmedDeposits = (clone $query)
            ->where('status', 'confirmed')
            ->count();

        $confirmedAmount = (clone $query)
            ->where('status', 'confirmed')
            ->sum('amount');

        $pendingDeposits = (clone $query)
            ->where('status', 'pending')
            ->count();

        $pendingAmount = (clone $query)
            ->where('status', 'pending')
            ->sum('amount');

        $rejectedDeposits = (clone $query)
            ->where('status', 'rejected')
            ->count();

        $rejectedAmount = (clone $query)
            ->where('status', 'rejected')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $deposits = $query
            ->orderByDesc('deposit_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Tahun
        |--------------------------------------------------------------------------
        */

        $years = FinanceDeposit::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->selectRaw(
                'YEAR(deposit_date) as year'
            )
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view(
            'admin.reports.deposits.index',
            compact(
                'deposits',
                'years',

                'status',
                'year',
                'month',
                'dateFrom',
                'dateTo',
                'search',

                'totalDeposits',
                'totalAmount',

                'confirmedDeposits',
                'confirmedAmount',

                'pendingDeposits',
                'pendingAmount',

                'rejectedDeposits',
                'rejectedAmount',
            )
        );
    }

    /**
     * Detail setoran.
     */
    public function detail(FinanceDeposit $deposit)
    {
        $user = request()->user();

        /*
    |--------------------------------------------------------------------------
    | Scope organisasi
    |--------------------------------------------------------------------------
    */

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
    |--------------------------------------------------------------------------
    | Pastikan setoran berada dalam scope user
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $organizationIds->contains(
                $deposit->organization_id
            ),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Load relasi
    |--------------------------------------------------------------------------
    */

        $deposit->load([
            'organization',
            'targetOrganization',
            'creator',
            'confirmer',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'id' => $deposit->id,

            'deposit_date' =>
            optional($deposit->deposit_date)
                ->format('d/m/Y'),

            'organization' => [
                'id' => $deposit->organization?->id,
                'name' => $deposit->organization?->name,
            ],

            'target_organization' => [
                'id' => $deposit->targetOrganization?->id,
                'name' => $deposit->targetOrganization?->name,
            ],

            'amount' =>
            $deposit->amount,

            'payment_method' =>
            $deposit->payment_method,

            'status' =>
            $deposit->status,

            'description' =>
            $deposit->description,

            'proof' => [
                'path' =>
                $deposit->proof_path,

                'original_name' =>
                $deposit->proof_original_name,

                'size' =>
                $deposit->proof_size,
            ],

            'creator' => [
                'id' =>
                $deposit->creator?->id,

                'name' =>
                $deposit->creator?->name,
            ],

            'confirmer' => [
                'id' =>
                $deposit->confirmer?->id,

                'name' =>
                $deposit->confirmer?->name,
            ],

            'confirmed_at' =>
            optional($deposit->confirmed_at)
                ->format('d/m/Y H:i'),

            'rejection_reason' =>
            $deposit->rejection_reason,
        ]);
    }

    /**
     * Export laporan setoran ke Excel.
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new DepositReportExport($request),
            'laporan-setoran-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    /**
     * Export laporan setoran ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        $status = $request->input('status');
        $year = $request->input('year');
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $deposits = FinanceDeposit::query()
            ->with([
                'organization',
                'targetOrganization',
                'creator',
                'confirmer',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->when(
                $status,
                fn($query) =>
                $query->where('status', $status)
            )
            ->when(
                $year,
                fn($query) =>
                $query->whereYear('deposit_date', $year)
            )
            ->when(
                $month,
                fn($query) =>
                $query->whereMonth('deposit_date', $month)
            )
            ->when(
                $dateFrom,
                fn($query) =>
                $query->whereDate(
                    'deposit_date',
                    '>=',
                    $dateFrom
                )
            )
            ->when(
                $dateTo,
                fn($query) =>
                $query->whereDate(
                    'deposit_date',
                    '<=',
                    $dateTo
                )
            )
            ->when(
                $search,
                function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {

                        $query->whereHas(
                            'organization',
                            function ($query) use ($search) {
                                $query->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                );
                            }
                        )

                            ->orWhereHas(
                                'targetOrganization',
                                function ($query) use ($search) {
                                    $query->where(
                                        'name',
                                        'like',
                                        '%' . $search . '%'
                                    );
                                }
                            )

                            ->orWhere(
                                'description',
                                'like',
                                '%' . $search . '%'
                            );
                    });
                }
            )
            ->orderByDesc('deposit_date')
            ->orderByDesc('id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Nama organisasi berdasarkan data laporan
    |--------------------------------------------------------------------------
    */

        $organizationNames = $deposits
            ->pluck('organization.name')
            ->filter()
            ->unique()
            ->values();

        $organizationName =
            $organizationNames->count() === 1
            ? $organizationNames->first()
            : 'Beberapa Unit';

        /*
    |--------------------------------------------------------------------------
    | Siapkan bukti setoran untuk PDF
    |--------------------------------------------------------------------------
    */

        foreach ($deposits as $deposit) {

            $deposit->proof_base64 = null;

            if (!$deposit->proof_path) {
                continue;
            }

            $filePath =
                storage_path(
                    'app/public/' . $deposit->proof_path
                );

            if (!is_file($filePath)) {
                continue;
            }

            $mimeType =
                mime_content_type($filePath);

            if (!in_array($mimeType, [
                'image/jpeg',
                'image/png',
                'image/webp',
            ])) {
                continue;
            }

            $deposit->proof_base64 =
                'data:' .
                $mimeType .
                ';base64,' .
                base64_encode(
                    file_get_contents($filePath)
                );
        }

        $pdf = Pdf::loadView(
            'admin.reports.deposits.exports.pdf',
            [
                'deposits' => $deposits,
                'organizationName' => $organizationName,
                'status' => $status,
                'year' => $year,
                'month' => $month,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'search' => $search,
            ]
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download(
            'laporan-setoran-' .
                now()->format('Y-m-d-His') .
                '.pdf'
        );
    }
}
