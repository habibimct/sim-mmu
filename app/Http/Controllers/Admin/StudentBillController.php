<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\StudentAcademicYear;
use App\Models\AcademicYear;
use App\Models\BillType;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentBillController extends Controller
{
    /**
     * Daftar tagihan siswa.
     */
    public function index(
        Request $request
    ): View {

        $user = $request->user();

        Gate::authorize(
            'viewAny',
            StudentBill::class
        );


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
            ->pluck(
                'organizations.id'
            );


        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

        $organizationId =
            $request->input(
                'organization_id'
            );

        $academicYearId =
            $request->input(
                'academic_year_id'
            );

        $status =
            $request->input(
                'status'
            );

        $schoolClassId =
            $request->input(
                'school_class_id'
            );

        $billTypeId =
            $request->input(
                'bill_type_id'
            );

        $student =
            $request->input(
                'student'
            );

        /*
    |--------------------------------------------------------------------------
    | Validasi organisasi
    |--------------------------------------------------------------------------
    */

        if (
            $organizationId !== null
            && ! $organizationIds->contains(
                (int) $organizationId
            )
        ) {

            $organizationId = null;
        }


        /*
    |--------------------------------------------------------------------------
    | Validasi status
    |--------------------------------------------------------------------------
    */

        $allowedStatuses = [
            'unpaid',
            'partial',
            'paid',
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
    | Query tagihan
    |--------------------------------------------------------------------------
    */

        $query = StudentBill::query()

            ->with([
                'billType.organization',
                'studentAcademicYear.student',
                'studentAcademicYear.academicYear',
                'studentAcademicYear.organization',
                'studentAcademicYear.schoolClass',
                'canceller',
            ])

            ->whereHas(
                'billType',
                function ($query) use (
                    $organizationIds,
                    $organizationId
                ) {

                    $query->whereIn(
                        'organization_id',
                        $organizationIds
                    );

                    if ($organizationId !== null) {

                        $query->where(
                            'organization_id',
                            $organizationId
                        );
                    }
                }
            )

            ->when(
                $academicYearId,
                function ($query) use (
                    $academicYearId
                ) {

                    $query->whereHas(
                        'studentAcademicYear',
                        function ($query) use (
                            $academicYearId
                        ) {

                            $query->where(
                                'academic_year_id',
                                $academicYearId
                            );
                        }
                    );
                }
            )

            ->when(
                $status,
                fn($query) =>
                $query->where(
                    'status',
                    $status
                )
            )

            ->when(
                $schoolClassId,
                function ($query) use ($schoolClassId) {

                    $query->whereHas(
                        'studentAcademicYear',
                        function ($query) use ($schoolClassId) {

                            $query->where(
                                'school_class_id',
                                $schoolClassId
                            );
                        }
                    );
                }
            )

            ->when(
                $billTypeId,
                function ($query) use ($billTypeId) {

                    $query->where(
                        'bill_type_id',
                        $billTypeId
                    );
                }
            )

            ->when(
                $student,
                function ($query) use ($student) {

                    $query->whereHas(
                        'studentAcademicYear.student',
                        function ($query) use ($student) {

                            $query->where(function ($query) use ($student) {

                                $query
                                    ->where(
                                        'nis',
                                        'like',
                                        "%{$student}%"
                                    )
                                    ->orWhere(
                                        'name',
                                        'like',
                                        "%{$student}%"
                                    );
                            });
                        }
                    );
                }
            );


        /*
|--------------------------------------------------------------------------
| Ringkasan tagihan
|--------------------------------------------------------------------------
|
| Menggunakan query yang sama dengan tabel,
| sehingga ringkasan mengikuti filter.
|
*/

        $summaryQuery = clone $query;


        $totalBills = (clone $summaryQuery)
            ->count();

        $totalAmount = (clone $summaryQuery)
            ->sum('amount');


        $unpaidBills = (clone $summaryQuery)
            ->where(
                'status',
                'unpaid'
            )
            ->count();

        $unpaidAmount = (clone $summaryQuery)
            ->where(
                'status',
                'unpaid'
            )
            ->sum('amount');


        $partialBills = (clone $summaryQuery)
            ->where(
                'status',
                'partial'
            )
            ->count();

        $partialAmount = (clone $summaryQuery)
            ->where(
                'status',
                'partial'
            )
            ->sum('amount');


        $paidBills = (clone $summaryQuery)
            ->where(
                'status',
                'paid'
            )
            ->count();

        $paidAmount = (clone $summaryQuery)
            ->where(
                'status',
                'paid'
            )
            ->sum('amount');


        $cancelledBills = (clone $summaryQuery)
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
    | Daftar tagihan
    |--------------------------------------------------------------------------
    */

        $studentBills = $query
            ->orderByDesc('due_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();


        /*
    |--------------------------------------------------------------------------
    | Organisasi untuk filter
    |--------------------------------------------------------------------------
    */

        $organizations = $user
            ->organizations()
            ->where(
                'organizations.is_active',
                true
            )
            ->whereNotNull(
                'organizations.parent_id'
            )
            ->orderBy(
                'name'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Tahun akademik
    |--------------------------------------------------------------------------
    */

        $academicYears = AcademicYear::query()
            ->orderByDesc(
                'start_date'
            )
            ->get();


        /*
|--------------------------------------------------------------------------
| Data untuk modal Create
|--------------------------------------------------------------------------
*/

        $billTypes = BillType::query()
            ->with('organization')
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy(
                'organization_id'
            )
            ->orderBy(
                'name'
            )
            ->get();


        $studentAcademicYears = StudentAcademicYear::query()
            ->with([
                'student',
                'academicYear',
                'organization',
                'schoolClass',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'status',
                'active'
            )
            ->orderBy(
                'academic_year_id'
            )
            ->get();


        $schoolClasses = SchoolClass::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'is_active',
                true
            )
            ->when(
                $organizationId,
                function ($query) use ($organizationId) {

                    $query->where(
                        'organization_id',
                        $organizationId
                    );
                }
            )
            ->when(
                $academicYearId,
                function ($query) use ($academicYearId) {

                    $query->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            )
            ->orderBy(
                'organization_id'
            )
            ->orderBy(
                'level'
            )
            ->orderBy(
                'name'
            )
            ->get();


        return view(
            'admin.finance.bills.index',
            compact(
                'studentBills',

                'organizations',
                'academicYears',
                'schoolClasses',
                'billTypes',

                'organizationId',
                'academicYearId',
                'schoolClassId',
                'billTypeId',
                'status',
                'student',

                'studentAcademicYears',
                'totalBills',
                'totalAmount',

                'unpaidBills',
                'unpaidAmount',

                'partialBills',
                'partialAmount',

                'paidBills',
                'paidAmount',

                'cancelledBills',
                'cancelledAmount',
            )
        );
    }

    /**
     * Menyimpan tagihan siswa baru.
     */
    public function store(
        Request $request
    ) {
        $user = $request->user();

        Gate::authorize(
            'create',
            StudentBill::class
        );


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
            ->pluck(
                'organizations.id'
            );


        /*
    |--------------------------------------------------------------------------
    | Pastikan user memiliki unit
    |--------------------------------------------------------------------------
    */

        if ($organizationIds->isEmpty()) {

            abort(
                403,
                'Anda tidak memiliki organisasi/unit yang dapat mengelola tagihan.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Saat ini Bendahara Unit menggunakan satu unit
    |--------------------------------------------------------------------------
    */

        $organizationId =
            (int) $organizationIds->first();


        /*
    |--------------------------------------------------------------------------
    | Mode pembuatan
    |--------------------------------------------------------------------------
    */

        $mode = $request->input(
            'create_mode'
        );


        if (
            ! in_array(
                $mode,
                [
                    'individual',
                    'bulk',
                ],
                true
            )
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'create_mode' =>
                    'Mode pembuatan tagihan tidak valid.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Validasi data umum
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'bill_type_id' => [
                'required',
                'integer',
                'exists:bill_types,id',
            ],

            'period' => [
                'required',
                'string',
                'max:50',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);


        /*
    |--------------------------------------------------------------------------
    | Ambil BillType
    |--------------------------------------------------------------------------
    */

        $billType = \App\Models\BillType::query()
            ->findOrFail(
                $validated['bill_type_id']
            );


        /*
    |--------------------------------------------------------------------------
    | Pastikan jenis tagihan milik unit user
    |--------------------------------------------------------------------------
    */

        if (
            (int) $billType->organization_id
            !== $organizationId
        ) {

            abort(
                403,
                'Jenis tagihan bukan milik unit Anda.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | MODE INDIVIDU
    |--------------------------------------------------------------------------
    */

        if ($mode === 'individual') {

            $individualData =
                $request->validate([

                    'student_academic_year_id' => [
                        'required',
                        'integer',
                        'exists:student_academic_years,id',
                    ],

                ]);


            /*
        |--------------------------------------------------------------------------
        | Ambil data siswa
        |--------------------------------------------------------------------------
        */

            $studentAcademicYear =
                \App\Models\StudentAcademicYear::query()
                ->findOrFail(
                    $individualData['student_academic_year_id']
                );


            /*
        |--------------------------------------------------------------------------
        | Pastikan siswa milik unit user
        |--------------------------------------------------------------------------
        */

            if (
                (int) $studentAcademicYear->organization_id
                !== $organizationId
            ) {

                abort(
                    403,
                    'Siswa tidak berasal dari unit Anda.'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | Pastikan siswa masih aktif
        |--------------------------------------------------------------------------
        */

            if (
                $studentAcademicYear->status !== 'active'
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'student_academic_year_id' =>
                        'Siswa tidak berstatus aktif.',
                    ]);
            }


            /*
        |--------------------------------------------------------------------------
        | Cegah tagihan ganda
        |--------------------------------------------------------------------------
        |
        | Untuk sementara kombinasi yang dianggap unik:
        |
        | siswa + jenis tagihan + periode
        |
        */

            $alreadyExists =
                StudentBill::query()
                ->where(
                    'student_academic_year_id',
                    $studentAcademicYear->id
                )
                ->where(
                    'bill_type_id',
                    $billType->id
                )
                ->where(
                    'period',
                    $validated['period']
                )
                ->exists();


            if ($alreadyExists) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'student_academic_year_id' =>
                        'Siswa tersebut sudah memiliki tagihan dengan jenis dan periode yang sama.',
                    ]);
            }


            /*
        |--------------------------------------------------------------------------
        | Buat tagihan individu
        |--------------------------------------------------------------------------
        */

            StudentBill::create([

                'student_academic_year_id' =>
                $studentAcademicYear->id,

                'bill_type_id' =>
                $billType->id,

                'period' =>
                $validated['period'],

                'amount' =>
                $validated['amount'],

                'due_date' =>
                $validated['due_date'] ?? null,

                'status' =>
                'unpaid',

                'description' =>
                $validated['description'] ?? null,

            ]);


            return redirect()
                ->route(
                    'admin.finance.bills.index'
                )
                ->with(
                    'success',
                    'Tagihan siswa berhasil dibuat.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | MODE MASSAL
    |--------------------------------------------------------------------------
    */

        $bulkData =
            $request->validate([

                'bulk_academic_year_id' => [
                    'required',
                    'integer',
                    'exists:academic_years,id',
                ],

                'bulk_school_class_id' => [
                    'required',
                    'integer',
                    'exists:school_classes,id',
                ],

            ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan kelas milik unit dan tahun akademik yang dipilih
    |--------------------------------------------------------------------------
    */

        $schoolClass =
            \App\Models\SchoolClass::query()
            ->findOrFail(
                $bulkData['bulk_school_class_id']
            );


        if (
            (int) $schoolClass->organization_id
            !== $organizationId
        ) {

            abort(
                403,
                'Kelas bukan milik unit Anda.'
            );
        }


        if (
            (int) $schoolClass->academic_year_id
            !==
            (int) $bulkData['bulk_academic_year_id']
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'bulk_school_class_id' =>
                    'Kelas tidak sesuai dengan tahun akademik yang dipilih.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Ambil seluruh siswa aktif pada kelas
    |--------------------------------------------------------------------------
    */

        $studentAcademicYears =
            \App\Models\StudentAcademicYear::query()
            ->where(
                'organization_id',
                $organizationId
            )
            ->where(
                'academic_year_id',
                $bulkData['bulk_academic_year_id']
            )
            ->where(
                'school_class_id',
                $schoolClass->id
            )
            ->where(
                'status',
                'active'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Tidak ada siswa
    |--------------------------------------------------------------------------
    */

        if (
            $studentAcademicYears->isEmpty()
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'bulk_school_class_id' =>
                    'Tidak ada siswa aktif pada kelas tersebut.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Buat tagihan massal
    |--------------------------------------------------------------------------
    */

        $createdCount = 0;

        $skippedCount = 0;


        foreach (
            $studentAcademicYears
            as $studentAcademicYear
        ) {

            /*
        |--------------------------------------------------------------------------
        | Cek duplikat
        |--------------------------------------------------------------------------
        */

            $alreadyExists =
                StudentBill::query()
                ->where(
                    'student_academic_year_id',
                    $studentAcademicYear->id
                )
                ->where(
                    'bill_type_id',
                    $billType->id
                )
                ->where(
                    'period',
                    $validated['period']
                )
                ->exists();


            if ($alreadyExists) {

                $skippedCount++;

                continue;
            }


            /*
        |--------------------------------------------------------------------------
        | Buat tagihan
        |--------------------------------------------------------------------------
        */

            StudentBill::create([

                'student_academic_year_id' =>
                $studentAcademicYear->id,

                'bill_type_id' =>
                $billType->id,

                'period' =>
                $validated['period'],

                'amount' =>
                $validated['amount'],

                'due_date' =>
                $validated['due_date'] ?? null,

                'status' =>
                'unpaid',

                'description' =>
                $validated['description'] ?? null,

            ]);


            $createdCount++;
        }


        /*
    |--------------------------------------------------------------------------
    | Hasil proses massal
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route(
                'admin.finance.bills.index'
            )
            ->with(
                'success',
                "Tagihan massal selesai. "
                    . "Berhasil dibuat: {$createdCount}. "
                    . "Dilewati karena sudah ada: {$skippedCount}."
            );
    }

    /**
     * Memperbarui tagihan siswa.
     */
    public function update(
        Request $request,
        StudentBill $studentBill
    ) {
        $user = $request->user();

        Gate::authorize(
            'update',
            $studentBill
        );


        /*
    |--------------------------------------------------------------------------
    | Ambil organisasi yang menjadi kewenangan user
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
            ->pluck(
                'organizations.id'
            );


        /*
    |--------------------------------------------------------------------------
    | Muat relasi
    |--------------------------------------------------------------------------
    */

        $studentBill->load([
            'studentAcademicYear.student',
            'studentAcademicYear.academicYear',
            'studentAcademicYear.organization',
            'billType.organization',
        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan tagihan memang milik unit user
    |--------------------------------------------------------------------------
    */

        if (
            ! $organizationIds->contains(
                (int) $studentBill
                    ->studentAcademicYear
                    ->organization_id
            )
        ) {

            abort(
                403,
                'Anda tidak memiliki kewenangan terhadap tagihan ini.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Validasi input
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'bill_type_id' => [
                'required',
                'integer',
                'exists:bill_types,id',
            ],

            'period' => [
                'required',
                'string',
                'max:50',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);


        /*
    |--------------------------------------------------------------------------
    | Ambil BillType baru
    |--------------------------------------------------------------------------
    */

        $billType = \App\Models\BillType::query()
            ->findOrFail(
                $validated['bill_type_id']
            );


        /*
    |--------------------------------------------------------------------------
    | Pastikan BillType berasal dari unit yang sama
    |--------------------------------------------------------------------------
    */

        if (
            (int) $billType->organization_id
            !==
            (int) $studentBill
                ->studentAcademicYear
                ->organization_id
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'bill_type_id' =>
                    'Jenis tagihan tidak berasal dari unit siswa.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Perbarui tagihan
    |--------------------------------------------------------------------------
    */

        $studentBill->update([

            'bill_type_id' =>
            $billType->id,

            'period' =>
            $validated['period'],

            'amount' =>
            $validated['amount'],

            'due_date' =>
            $validated['due_date'] ?? null,

            'description' =>
            $validated['description'] ?? null,

        ]);


        /*
    |--------------------------------------------------------------------------
    | Kembali ke daftar
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route(
                'admin.finance.bills.index'
            )
            ->with(
                'success',
                'Tagihan siswa berhasil diperbarui.'
            );
    }

    /**
     * Membatalkan tagihan siswa.
     */
    public function cancel(
        Request $request,
        StudentBill $studentBill
    ) {
        $user = $request->user();

        Gate::authorize(
            'cancel',
            $studentBill
        );


        /*
    |--------------------------------------------------------------------------
    | Muat relasi
    |--------------------------------------------------------------------------
    */

        $studentBill->load([
            'studentAcademicYear',
        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan status masih unpaid
    |--------------------------------------------------------------------------
    */

        if (
            $studentBill->status !== 'unpaid'
        ) {

            return back()
                ->withErrors([
                    'studentBill' =>
                    'Tagihan ini tidak dapat dibatalkan karena sudah diproses.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Validasi alasan
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'cancellation_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Batalkan tagihan
    |--------------------------------------------------------------------------
    */

        $studentBill->update([
            'status' =>
            'cancelled',

            'cancellation_reason' =>
            $validated['cancellation_reason'],

            'cancelled_by' =>
            $user->id,

            'cancelled_at' =>
            now(),
        ]);


        return redirect()
            ->route(
                'admin.finance.bills.index'
            )
            ->with(
                'success',
                'Tagihan siswa berhasil dibatalkan.'
            );
    }
}
