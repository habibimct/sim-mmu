<?php

namespace App\Http\Controllers\Admin\Reports;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PaymentReportExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentReportController extends Controller
{
    /**
     * Laporan pembayaran siswa.
     */
    public function index(Request $request): View
    {
        Gate::authorize(
            'viewAny',
            Payment::class
        );

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang menjadi kewenangan user
        |--------------------------------------------------------------------------
        */

        $organizationIds = $user
            ->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->pluck('organizations.id');


        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $status = $request->input(
            'status'
        );

        $paymentMethod = $request->input(
            'payment_method'
        );

        $year = $request->input(
            'year'
        );

        $month = $request->input(
            'month'
        );

        $dateFrom = $request->input(
            'date_from'
        );

        $dateTo = $request->input(
            'date_to'
        );

        $search = trim(
            $request->input('search', '')
        );


        /*
        |--------------------------------------------------------------------------
        | Validasi status
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [
            'pending',
            'confirmed',
            'failed',
            'cancelled',
        ];

        if (
            $status !== null
            && ! in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $status = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi metode pembayaran
        |--------------------------------------------------------------------------
        */

        $allowedPaymentMethods = [
            'cash',
            'bank_transfer',
            'online',
        ];

        if (
            $paymentMethod !== null
            && ! in_array(
                $paymentMethod,
                $allowedPaymentMethods,
                true
            )
        ) {
            $paymentMethod = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi tahun dan bulan
        |--------------------------------------------------------------------------
        */

        $year = $year
            ? (int) $year
            : null;

        $month = $month
            ? (int) $month
            : null;

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
        | Query pembayaran
        |--------------------------------------------------------------------------
        */

        $query = Payment::query()
            ->with([
                'organization',
                'creator',
                'confirmer',

                'allocations.studentBill.billType',

                'allocations.studentBill.studentAcademicYear.student',

                'allocations.studentBill.studentAcademicYear.schoolClass',

                'allocations.studentBill.studentAcademicYear.academicYear',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->when(
                $status,
                fn($query) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $paymentMethod,
                fn($query) => $query->where(
                    'payment_method',
                    $paymentMethod
                )
            )
            ->when(
                $year,
                fn($query) => $query->whereYear(
                    'payment_date',
                    $year
                )
            )
            ->when(
                $month,
                fn($query) => $query->whereMonth(
                    'payment_date',
                    $month
                )
            )
            ->when(
                $dateFrom,
                fn($query) => $query->whereDate(
                    'payment_date',
                    '>=',
                    $dateFrom
                )
            )
            ->when(
                $dateTo,
                fn($query) => $query->whereDate(
                    'payment_date',
                    '<=',
                    $dateTo
                )
            )
            ->when(
                $search,
                function ($query) use ($search) {

                    $query->where(
                        function ($query) use ($search) {

                            $query
                                ->where(
                                    'payment_number',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'allocations.studentBill.studentAcademicYear.student',
                                    function ($query) use ($search) {

                                        $query
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'nis',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                );
                        }
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        $summaryQuery = clone $query;


        $totalPayments = (clone $summaryQuery)
            ->count();

        $totalAmount = (clone $summaryQuery)
            ->sum('amount');


        $confirmedPayments = (clone $summaryQuery)
            ->where(
                'status',
                'confirmed'
            )
            ->count();

        $confirmedAmount = (clone $summaryQuery)
            ->where(
                'status',
                'confirmed'
            )
            ->sum('amount');


        $pendingPayments = (clone $summaryQuery)
            ->where(
                'status',
                'pending'
            )
            ->count();

        $pendingAmount = (clone $summaryQuery)
            ->where(
                'status',
                'pending'
            )
            ->sum('amount');


        $failedPayments = (clone $summaryQuery)
            ->where(
                'status',
                'failed'
            )
            ->count();

        $failedAmount = (clone $summaryQuery)
            ->where(
                'status',
                'failed'
            )
            ->sum('amount');


        $cancelledPayments = (clone $summaryQuery)
            ->where(
                'status',
                'cancelled'
            )
            ->count();

        $cancelledAmount = (clone $summaryQuery)
            ->where(
                'status',
                'cancelled'
            )
            ->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | Daftar pembayaran
        |--------------------------------------------------------------------------
        */

        $payments = $query
            ->orderByDesc(
                'payment_date'
            )
            ->orderByDesc(
                'id'
            )
            ->paginate(20)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Tahun yang tersedia
        |--------------------------------------------------------------------------
        */

        $years = Payment::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->selectRaw(
                'YEAR(payment_date) as year'
            )
            ->distinct()
            ->orderByDesc(
                'year'
            )
            ->pluck('year');


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.reports.payments.index',
            compact(
                'payments',

                'years',

                'status',
                'paymentMethod',
                'year',
                'month',
                'dateFrom',
                'dateTo',
                'search',

                'totalPayments',
                'totalAmount',

                'confirmedPayments',
                'confirmedAmount',

                'pendingPayments',
                'pendingAmount',

                'failedPayments',
                'failedAmount',

                'cancelledPayments',
                'cancelledAmount'
            )
        );
    }


    /**
     * Detail pembayaran untuk modal laporan.
     */
    public function detail(
        Request $request,
        Payment $payment
    ) {
        Gate::authorize(
            'view',
            $payment
        );
        $payment->load([
            'organization',
            'creator',
            'confirmer',
            'allocations.studentBill.billType',
            'allocations.studentBill.studentAcademicYear.student',
            'allocations.studentBill.studentAcademicYear.schoolClass',
            'allocations.studentBill.studentAcademicYear.academicYear',
        ]);

        return response()->json([
            'id' => $payment->id,
            'payment_number' =>
            $payment->payment_number,
            'payment_date' =>
            $payment->payment_date
                ?->format('d/m/Y H:i'),
            'amount' =>
            (float) $payment->amount,
            'payment_method' =>
            $payment->payment_method,
            'payment_provider' =>
            $payment->payment_provider,
            'status' =>
            $payment->status,
            'description' =>
            $payment->description,
            'organization' =>
            $payment->organization?->name,
            'creator' =>
            $payment->creator?->name,
            'confirmer' =>
            $payment->confirmer?->name,
            'confirmed_at' =>
            $payment->confirmed_at
                ?->format('d/m/Y H:i'),
            'allocations' =>
            $payment->allocations
                ->map(
                    function ($allocation) {
                        $bill =
                            $allocation->studentBill;
                        $studentAcademicYear =
                            $bill?->studentAcademicYear;
                        $student =
                            $studentAcademicYear?->student;
                        return [
                            'student_name' =>
                            $student?->name,
                            'nis' =>
                            $student?->nis,
                            'bill_type' =>
                            $bill?->billType?->name,
                            'period' =>
                            $bill?->period,
                            'class' =>
                            $studentAcademicYear
                                ?->schoolClass
                                ?->name,
                            'academic_year' =>
                            $studentAcademicYear
                                ?->academicYear
                                ?->name,
                            'amount' =>
                            (float) $allocation->amount,
                        ];
                    }
                )
                ->values(),
        ]);
    }

    public function exportExcel(Request $request)
    {
        Gate::authorize('viewAny', Payment::class);

        $status = $request->input('status');
        $paymentMethod = $request->input('payment_method');
        $year = $request->input('year');
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $request->validate([
            'status' => [
                'nullable',
                'in:pending,confirmed,failed,cancelled',
            ],

            'payment_method' => [
                'nullable',
                'in:cash,bank_transfer,online',
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

        $filters = [
            'status' => $status,
            'payment_method' => $paymentMethod,
            'year' => $year,
            'month' => $month,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'search' => $search,
        ];

        $filename = 'laporan-pembayaran';

        if ($year) {
            $filename .= '-' . $year;
        }

        if ($month) {
            $filename .= '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        }

        $filename .= '.xlsx';

        return Excel::download(
            new PaymentReportExport($filters),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', Payment::class);

        $status = $request->input('status');
        $paymentMethod = $request->input('payment_method');
        $year = $request->input('year');
        $month = $request->input('month');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $request->validate([
            'status' => [
                'nullable',
                'in:pending,confirmed,failed,cancelled',
            ],

            'payment_method' => [
                'nullable',
                'in:cash,bank_transfer,online',
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

        $user = Auth::user();

        /*
|--------------------------------------------------------------------------
| Scope organisasi
|--------------------------------------------------------------------------
*/

        $organizationIds =
            Organization::accessibleIdsForUser($user);
        /*
    |--------------------------------------------------------------------------
    | Query pembayaran
    |--------------------------------------------------------------------------
    */
        $payments = Payment::query()
            ->with([
                'organization',
                'creator',
                'confirmer',
                'allocations.studentBill.billType',
                'allocations.studentBill.studentAcademicYear.student',
                'allocations.studentBill.studentAcademicYear.schoolClass',
                'allocations.studentBill.studentAcademicYear.academicYear',
            ])
            ->whereIn('organization_id', $organizationIds)

            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })

            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })

            ->when($year, function ($query) use ($year) {
                $query->whereYear('payment_date', $year);
            })

            ->when($month, function ($query) use ($month) {
                $query->whereMonth('payment_date', $month);
            })

            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('payment_date', '>=', $dateFrom);
            })

            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('payment_date', '<=', $dateTo);
            })

            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {

                    $query->where(
                        'payment_number',
                        'like',
                        '%' . $search . '%'
                    )

                        ->orWhereHas(
                            'allocations.studentBill.studentAcademicYear.student',
                            function ($query) use ($search) {

                                $query->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('nis', 'like', '%' . $search . '%');
                            }
                        );
                });
            })

            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Data filter untuk ditampilkan di PDF
    |--------------------------------------------------------------------------
    */
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $filterDescription = [];

        if ($year) {
            $filterDescription[] = 'Tahun: ' . $year;
        }

        if ($month) {
            $filterDescription[] = 'Bulan: ' . ($months[$month] ?? $month);
        }

        if ($status) {
            $filterDescription[] = 'Status: ' . match ($status) {
                'pending' => 'Menunggu Konfirmasi',
                'confirmed' => 'Dikonfirmasi',
                'failed' => 'Gagal',
                'cancelled' => 'Dibatalkan',
                default => $status,
            };
        }

        if ($paymentMethod) {
            $filterDescription[] = 'Metode: ' . match ($paymentMethod) {
                'cash' => 'Tunai',
                'bank_transfer' => 'Transfer Bank',
                'online' => 'Online',
                default => $paymentMethod,
            };
        }

        if ($dateFrom) {
            $filterDescription[] = 'Dari: ' .
                \Carbon\Carbon::parse($dateFrom)->format('d/m/Y');
        }

        if ($dateTo) {
            $filterDescription[] = 'Sampai: ' .
                \Carbon\Carbon::parse($dateTo)->format('d/m/Y');
        }

        if ($search) {
            $filterDescription[] = 'Pencarian: ' . $search;
        }


        /*
|--------------------------------------------------------------------------
| Profil Organisasi untuk Kop
|--------------------------------------------------------------------------
*/

        $organization = null;

        $organizationNames = $payments
            ->pluck('organization.name')
            ->filter()
            ->unique()
            ->values();

        $organizationName =
            $organizationNames->count() === 1
            ? $organizationNames->first()
            : 'Beberapa Unit';

        if ($organizationNames->count() === 1) {

            $organization =
                Organization::whereIn(
                    'id',
                    $organizationIds
                )
                ->where(
                    'name',
                    $organizationNames->first()
                )
                ->first();
        }

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
            'admin.reports.payments.exports.pdf',
            [
                'payments' => $payments,
                'filterDescription' => $filterDescription,

                'organization' => $organization,
                'induk' => $induk,
                'organizationName' => $organizationName,
            ]
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pembayaran.pdf');
    }
}
