<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BillType;
use App\Models\Organization;
use App\Models\StudentBill;
use App\Models\StudentAcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Exports\BillReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class BillReportController extends Controller
{
    /**
     * Menampilkan laporan tagihan.
     */
    public function index(Request $request): View
    {
        $data = $this->reportData($request);

        /*
    |--------------------------------------------------------------------------
    | Detail tagihan
    |--------------------------------------------------------------------------
    | Diurutkan berdasarkan nama siswa.
    */
        $billsQuery = $data['query']
            ->join(
                'student_academic_years as say',
                'say.id',
                '=',
                'student_bills.student_academic_year_id'
            )
            ->join(
                'students as s',
                's.id',
                '=',
                'say.student_id'
            )
            ->select('student_bills.*')
            ->orderBy('s.name')
            ->orderBy('student_bills.period')
            ->orderBy('student_bills.id');

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
        $bills = $billsQuery
            ->paginate(15)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | Kirim ke view
    |--------------------------------------------------------------------------
    */
        return view(
            'admin.reports.bills.index',
            [
                ...$data,
                'bills' => $bills,
            ]
        );
    }


    /**
     * Menyiapkan seluruh data laporan tagihan.
     *
     * Digunakan bersama oleh:
     * - halaman laporan
     * - export Excel
     * - export PDF
     */
    private function reportData(Request $request): array
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Organisasi yang dapat diakses user
    |--------------------------------------------------------------------------
    */
        $organizationIds = Organization::accessibleIdsForUser($user);

        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */
        $academicYearId = $request->input('academic_year_id', '');
        $organizationId = $request->input('organization_id', '');
        $billTypeId = $request->input('bill_type_id', '');
        $period = $request->input('period', '');
        $status = $request->input('status', '');

        /*
    |--------------------------------------------------------------------------
    | Normalisasi nilai "Semua"
    |--------------------------------------------------------------------------
    */
        if ($academicYearId === 'all') {
            $academicYearId = '';
        }

        if ($organizationId === 'all') {
            $organizationId = '';
        }

        if ($billTypeId === 'all') {
            $billTypeId = '';
        }

        if ($period === 'all') {
            $period = '';
        }

        if ($status === 'all') {
            $status = '';
        }

        /*
    |--------------------------------------------------------------------------
    | Query utama
    |--------------------------------------------------------------------------
    */
        $query = StudentBill::query()
            ->with([
                'studentAcademicYear.student',
                'studentAcademicYear.organization',
                'studentAcademicYear.academicYear',
                'studentAcademicYear.schoolClass',
                'billType',
            ])
            ->whereHas(
                'studentAcademicYear',
                function ($q) use ($organizationIds) {
                    $q->whereIn(
                        'organization_id',
                        $organizationIds
                    );
                }
            );

        /*
    |--------------------------------------------------------------------------
    | Filter Tahun Ajaran
    |--------------------------------------------------------------------------
    */
        if ($academicYearId !== '') {

            $query->whereHas(
                'studentAcademicYear',
                function ($q) use ($academicYearId) {
                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Unit
    |--------------------------------------------------------------------------
    */
        if ($organizationId !== '') {

            if (
                ! $organizationIds->contains(
                    (int) $organizationId
                )
            ) {
                abort(403);
            }

            $query->whereHas(
                'studentAcademicYear',
                function ($q) use ($organizationId) {
                    $q->where(
                        'organization_id',
                        $organizationId
                    );
                }
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Jenis Tagihan
    |--------------------------------------------------------------------------
    */
        if ($billTypeId !== '') {

            $billTypeExists = BillType::query()
                ->whereIn(
                    'organization_id',
                    $organizationIds
                )
                ->whereKey($billTypeId)
                ->exists();

            if (! $billTypeExists) {
                $billTypeId = '';
            } else {
                $query->where(
                    'bill_type_id',
                    $billTypeId
                );
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Periode
    |--------------------------------------------------------------------------
    */
        if ($period !== '') {

            $query->where(
                'period',
                $period
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Status Tagihan
    |--------------------------------------------------------------------------
    */
        if ($status !== '') {

            $query->where(
                'status',
                $status
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Ambil seluruh data untuk perhitungan summary
    |--------------------------------------------------------------------------
    */
        $summaryBills = (clone $query)->get();

        /*
    |--------------------------------------------------------------------------
    | Hitung pembayaran setiap tagihan
    |--------------------------------------------------------------------------
    */
        $billIds = $summaryBills
            ->pluck('id')
            ->values();

        $paidAmounts = collect();

        if ($billIds->isNotEmpty()) {

            $paidAmounts = DB::table('payment_allocations')
                ->join(
                    'payments',
                    'payments.id',
                    '=',
                    'payment_allocations.payment_id'
                )
                ->whereIn(
                    'payment_allocations.student_bill_id',
                    $billIds
                )
                ->where(
                    'payments.status',
                    'confirmed'
                )
                ->select(
                    'payment_allocations.student_bill_id'
                )
                ->selectRaw(
                    'SUM(payment_allocations.amount) as paid_amount'
                )
                ->groupBy(
                    'payment_allocations.student_bill_id'
                )
                ->get()
                ->pluck(
                    'paid_amount',
                    'student_bill_id'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Rekap Tagihan Tiap Siswa
    |--------------------------------------------------------------------------
    */
        $studentSummary = $summaryBills
            ->groupBy('student_academic_year_id')
            ->map(function ($studentBills) use ($paidAmounts) {

                $first = $studentBills->first();

                $totalBillAmount = 0;
                $totalPaidAmount = 0;

                $billCount = 0;
                $paidBillCount = 0;
                $partialBillCount = 0;
                $unpaidBillCount = 0;
                $cancelledBillCount = 0;

                foreach ($studentBills as $bill) {

                    $amount = (float) $bill->amount;
                    $paid = (float) ($paidAmounts[$bill->id] ?? 0);

                    if ($bill->status === 'cancelled') {
                        $cancelledBillCount++;
                        continue;
                    }

                    $billCount++;

                    $paidForBill = min(
                        $paid,
                        $amount
                    );

                    $remaining = max(
                        $amount - $paidForBill,
                        0
                    );

                    $totalBillAmount += $amount;
                    $totalPaidAmount += $paidForBill;

                    if ($remaining <= 0) {
                        $paidBillCount++;
                    } elseif ($paidForBill > 0) {
                        $partialBillCount++;
                    } else {
                        $unpaidBillCount++;
                    }
                }

                return [
                    'student_academic_year_id' =>
                    $first->student_academic_year_id,

                    'student' =>
                    $first->studentAcademicYear?->student,

                    'organization' =>
                    $first->studentAcademicYear?->organization,

                    'school_class' =>
                    $first->studentAcademicYear?->schoolClass,

                    'bill_count' =>
                    $billCount,

                    'total_bill_amount' =>
                    $totalBillAmount,

                    'total_paid_amount' =>
                    $totalPaidAmount,

                    'total_remaining_amount' =>
                    max(
                        $totalBillAmount - $totalPaidAmount,
                        0
                    ),

                    'paid_bill_count' =>
                    $paidBillCount,

                    'partial_bill_count' =>
                    $partialBillCount,

                    'unpaid_bill_count' =>
                    $unpaidBillCount,

                    'cancelled_bill_count' =>
                    $cancelledBillCount,
                ];
            })
            ->sortBy(function ($item) {
                return strtolower(
                    $item['student']?->name ?? ''
                );
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Ringkasan
    |--------------------------------------------------------------------------
    */
        $totalBills = 0;

        $totalBillAmount = 0;
        $totalPaidAmount = 0;
        $totalRemainingAmount = 0;

        $paidBillCount = 0;
        $partialBillCount = 0;
        $unpaidBillCount = 0;
        $cancelledBillCount = 0;

        foreach ($summaryBills as $bill) {

            if ($bill->status === 'cancelled') {
                $cancelledBillCount++;
                continue;
            }

            $totalBills++;

            $amount = (float) $bill->amount;

            $paid = (float) (
                $paidAmounts[$bill->id] ?? 0
            );

            $paid = min(
                $paid,
                $amount
            );

            $remaining = max(
                $amount - $paid,
                0
            );

            $totalBillAmount += $amount;
            $totalPaidAmount += $paid;
            $totalRemainingAmount += $remaining;

            if ($remaining <= 0) {
                $paidBillCount++;
            } elseif ($paid > 0) {
                $partialBillCount++;
            } else {
                $unpaidBillCount++;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Rekap Tagihan per Unit
    |--------------------------------------------------------------------------
    */
        $organizationSummary = $summaryBills
            ->filter(function ($bill) {
                return $bill->status !== 'cancelled';
            })
            ->groupBy(function ($bill) {
                return $bill
                    ->studentAcademicYear
                    ?->organization
                    ?->id;
            })
            ->map(function ($bills) use ($paidAmounts) {

                $organization = $bills
                    ->first()
                    ?->studentAcademicYear
                    ?->organization;

                $totalBills = $bills->count();

                $totalAmount = 0;
                $totalPaid = 0;
                $totalRemaining = 0;

                foreach ($bills as $bill) {

                    $amount = (float) $bill->amount;

                    $paid = (float) (
                        $paidAmounts[$bill->id] ?? 0
                    );

                    $paid = min(
                        $paid,
                        $amount
                    );

                    $remaining = max(
                        $amount - $paid,
                        0
                    );

                    $totalAmount += $amount;
                    $totalPaid += $paid;
                    $totalRemaining += $remaining;
                }

                return (object) [
                    'organization' => $organization,
                    'total_bills' => $totalBills,
                    'total_amount' => $totalAmount,
                    'total_paid' => $totalPaid,
                    'total_remaining' => $totalRemaining,
                ];
            })
            ->sortBy(function ($item) {
                return $item->organization?->name ?? '';
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Tahun Ajaran
    |--------------------------------------------------------------------------
    */
        $academicYears = AcademicYear::query()
            ->orderByDesc('id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Jenis Tagihan
    |--------------------------------------------------------------------------
    */
        $billTypes = BillType::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Periode
    |--------------------------------------------------------------------------
    */
        $periods = StudentBill::query()
            ->whereHas(
                'studentAcademicYear',
                function ($q) use ($organizationIds) {
                    $q->whereIn(
                        'organization_id',
                        $organizationIds
                    );
                }
            )
            ->whereNotNull('period')
            ->where('period', '!=', '')
            ->select('period')
            ->distinct()
            ->orderBy('period')
            ->pluck('period');

        /*
    |--------------------------------------------------------------------------
    | Data dikembalikan
    |--------------------------------------------------------------------------
    */
        return [
            'organizations' => $organizations,
            'academicYears' => $academicYears,
            'billTypes' => $billTypes,
            'periods' => $periods,

            'summaryBills' => $summaryBills,
            'paidAmounts' => $paidAmounts,

            'studentSummary' => $studentSummary,
            'organizationSummary' => $organizationSummary,

            'totalBills' => $totalBills,
            'totalBillAmount' => $totalBillAmount,
            'totalPaidAmount' => $totalPaidAmount,
            'totalRemainingAmount' => $totalRemainingAmount,

            'paidBillCount' => $paidBillCount,
            'partialBillCount' => $partialBillCount,
            'unpaidBillCount' => $unpaidBillCount,
            'cancelledBillCount' => $cancelledBillCount,

            'academicYearId' => $academicYearId,
            'organizationId' => $organizationId,
            'billTypeId' => $billTypeId,
            'period' => $period,
            'status' => $status,

            'query' => $query,
        ];
    }


    /**
     * Export laporan tagihan ke Excel.
     */
    public function excel(Request $request)
    {
        $data = $this->reportData($request);

        /*
    |--------------------------------------------------------------------------
    | Ambil seluruh detail tagihan
    |--------------------------------------------------------------------------
    */
        $bills = $data['query']
            ->join(
                'student_academic_years as say',
                'say.id',
                '=',
                'student_bills.student_academic_year_id'
            )
            ->join(
                'students as s',
                's.id',
                '=',
                'say.student_id'
            )
            ->select('student_bills.*')
            ->orderBy('s.name')
            ->orderBy('student_bills.period')
            ->orderBy('student_bills.id')
            ->get();

        $data['bills'] = $bills;

        return Excel::download(
            new BillReportExport(
                $data
            ),
            'laporan-tagihan.xlsx'
        );
    }

    public function pdf(Request $request)
    {
        $data = $this->reportData($request);

        $bills = $data['query']
            ->join(
                'student_academic_years as say',
                'say.id',
                '=',
                'student_bills.student_academic_year_id'
            )
            ->join(
                'students as s',
                's.id',
                '=',
                'say.student_id'
            )
            ->select('student_bills.*')
            ->orderBy('s.name')
            ->orderBy('student_bills.period')
            ->orderBy('student_bills.id')
            ->get();

        $data['bills'] = $bills;

        /*
    |--------------------------------------------------------------------------
    | Profil Organisasi untuk Kop
    |--------------------------------------------------------------------------
    */

        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        $organizationId =
            $request->input('organization_id');

        $organization = null;

        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {
            $organization =
                Organization::whereIn(
                    'id',
                    $organizationIds
                )->find(
                    $organizationId
                );
        }

        $induk =
            Organization::where(
                'type',
                'induk'
            )->firstOrFail();

        $data['organization'] = $organization;
        $data['induk'] = $induk;

        /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

        $pdf = Pdf::loadView(
            'admin.reports.bills.exports.pdf',
            $data
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-tagihan.pdf');
    }


    /**
     * Mengambil jenis tagihan secara AJAX.
     */
    public function billTypes(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        $organizationId =
            $request->input('organization_id', '');

        $academicYearId =
            $request->input('academic_year_id', '');

        /*
    |--------------------------------------------------------------------------
    | Normalisasi nilai "Semua"
    |--------------------------------------------------------------------------
    */
        if ($organizationId === 'all') {
            $organizationId = '';
        }

        if ($academicYearId === 'all') {
            $academicYearId = '';
        }

        $query = BillType::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->orderBy('name');

        /*
    |--------------------------------------------------------------------------
    | Filter Unit
    |--------------------------------------------------------------------------
    */
        if ($organizationId !== '') {

            if (
                ! $organizationIds->contains(
                    (int) $organizationId
                )
            ) {
                abort(403);
            }

            $query->where(
                'organization_id',
                $organizationId
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Tahun Ajaran
    |--------------------------------------------------------------------------
    |
    | BillType tidak memiliki academic_year_id.
    | Jadi kita mengambil BillType yang benar-benar
    | digunakan oleh StudentBill pada tahun tersebut.
    |--------------------------------------------------------------------------
    */
        if ($academicYearId !== '') {

            $query->whereHas(
                'studentBills.studentAcademicYear',
                function ($q) use ($academicYearId) {
                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            );
        }

        return response()->json(
            $query
                ->get([
                    'id',
                    'organization_id',
                    'code',
                    'name',
                ])
        );
    }


    /**
     * Mengambil periode secara AJAX.
     */
    public function periods(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        $organizationId =
            $request->input('organization_id', '');

        $academicYearId =
            $request->input('academic_year_id', '');

        $billTypeId =
            $request->input('bill_type_id', '');

        /*
    |--------------------------------------------------------------------------
    | Normalisasi nilai "Semua"
    |--------------------------------------------------------------------------
    */
        if ($organizationId === 'all') {
            $organizationId = '';
        }

        if ($academicYearId === 'all') {
            $academicYearId = '';
        }

        if ($billTypeId === 'all') {
            $billTypeId = '';
        }

        $query = StudentBill::query()
            ->whereHas(
                'studentAcademicYear',
                function ($q) use (
                    $organizationIds,
                    $organizationId,
                    $academicYearId
                ) {

                    $q->whereIn(
                        'organization_id',
                        $organizationIds
                    );

                    /*
                |--------------------------------------------------------------------------
                | Unit
                |--------------------------------------------------------------------------
                */
                    if ($organizationId !== '') {

                        if (
                            ! $organizationIds->contains(
                                (int) $organizationId
                            )
                        ) {
                            abort(403);
                        }

                        $q->where(
                            'organization_id',
                            $organizationId
                        );
                    }

                    /*
                |--------------------------------------------------------------------------
                | Tahun Ajaran
                |--------------------------------------------------------------------------
                */
                    if ($academicYearId !== '') {

                        $q->where(
                            'academic_year_id',
                            $academicYearId
                        );
                    }
                }
            );

        /*
    |--------------------------------------------------------------------------
    | Jenis Tagihan
    |--------------------------------------------------------------------------
    */
        if ($billTypeId !== '') {

            $billType = BillType::query()
                ->whereIn(
                    'organization_id',
                    $organizationIds
                )
                ->whereKey($billTypeId)
                ->first();

            if (! $billType) {
                abort(403);
            }

            $query->where(
                'bill_type_id',
                $billTypeId
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Ambil periode
    |--------------------------------------------------------------------------
    */
        $periods = $query
            ->whereNotNull('period')
            ->where('period', '!=', '')
            ->select('period')
            ->distinct()
            ->orderBy('period')
            ->pluck('period')
            ->values();

        return response()->json($periods);
    }
}
