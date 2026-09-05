<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentClassTransferController extends Controller
{
    /**
     * Menampilkan halaman pindah kelas.
     */
    public function index(Request $request): View
    {
        $organizationIds = Student::organizationIdsForUser();

        /*
        |--------------------------------------------------------------------------
        | Tahun ajaran aktif
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Tahun ajaran yang dipilih
        |--------------------------------------------------------------------------
        */

        $academicYearId = $request->integer('academic_year_id');

        if (! $academicYearId) {
            $academicYearId = AcademicYear::query()
                ->where('is_active', true)
                ->orderByDesc('start_date')
                ->value('id');
        }

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang dapat diakses user
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn(
                'id',
                $organizationIds
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang dipilih
        |--------------------------------------------------------------------------
        */

        $organizationId = $request->integer('organization_id');

        if (
            ! $organizationId &&
            $organizationIds->count() === 1
        ) {
            $organizationId = $organizationIds->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Kelas asal
        |--------------------------------------------------------------------------
        */

        $classes = collect();

        if (
            $academicYearId &&
            $organizationId &&
            $organizationIds->contains(
                $organizationId
            )
        ) {
            $classes = SchoolClass::query()
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'is_active',
                    true
                )
                ->orderBy('level')
                ->orderBy('name')
                ->get();
        }

        /*
|--------------------------------------------------------------------------
| Kelas asal dan tujuan yang dipilih
|--------------------------------------------------------------------------
*/

        $sourceClassId = $request->integer(
            'source_class_id'
        );

        $targetClassId = $request->integer(
            'target_class_id'
        );

        /*
|--------------------------------------------------------------------------
| Kelas asal
|--------------------------------------------------------------------------
*/

        $sourceClass = null;

        if (
            $sourceClassId &&
            $classes->contains(
                'id',
                $sourceClassId
            )
        ) {
            $sourceClass = $classes->firstWhere(
                'id',
                $sourceClassId
            );
        }

        /*
|--------------------------------------------------------------------------
| Kelas tujuan
|--------------------------------------------------------------------------
*/

        $targetClass = null;

        if (
            $targetClassId &&
            $classes->contains(
                'id',
                $targetClassId
            )
        ) {
            $targetClass = $classes->firstWhere(
                'id',
                $targetClassId
            );
        }

        /*
|--------------------------------------------------------------------------
| Daftar kelas tujuan
|--------------------------------------------------------------------------
|
| Hanya kelas dalam unit dan tahun ajaran
| yang sama dengan kelas asal.
|
| Perbedaan tingkat maksimal satu.
|
*/

        $targetClasses = collect();

        if ($sourceClass) {

            $targetClasses = $classes
                ->filter(function ($class) use ($sourceClass) {

                    if (
                        $class->id ===
                        $sourceClass->id
                    ) {
                        return false;
                    }

                    return abs(
                        $class->level -
                            $sourceClass->level
                    ) <= 1;
                })
                ->values();
        }

        /*
|--------------------------------------------------------------------------
| Siswa kelas asal
|--------------------------------------------------------------------------
*/

        $sourceStudents = collect();

        if ($sourceClass) {

            $sourceStudents =
                StudentAcademicYear::query()
                ->with('student')
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'school_class_id',
                    $sourceClass->id
                )
                ->where(
                    'status',
                    'active'
                )
                ->whereHas(
                    'student',
                    function ($query) {
                        $query->where(
                            'is_active',
                            true
                        );
                    }
                )
                ->orderBy(
                    'id'
                )
                ->get();
        }

        /*
|--------------------------------------------------------------------------
| Siswa kelas tujuan
|--------------------------------------------------------------------------
*/

        $targetStudents = collect();

        if ($targetClass) {

            $targetStudents =
                StudentAcademicYear::query()
                ->with('student')
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'school_class_id',
                    $targetClass->id
                )
                ->where(
                    'status',
                    'active'
                )
                ->whereHas(
                    'student',
                    function ($query) {
                        $query->where(
                            'is_active',
                            true
                        );
                    }
                )
                ->orderBy(
                    'id'
                )
                ->get();
        }

        return view(
            'admin.students.class-transfer',
            compact(
                'academicYears',
                'organizations',
                'classes',
                'sourceStudents',
                'targetStudents',
                'academicYearId',
                'organizationId',
                'sourceClassId',
                'targetClassId',
                'sourceClass',
                'targetClass',
                'targetClasses'
            )
        );
    }

    /**
     * Memindahkan siswa dari kelas asal ke kelas tujuan.
     */
    public function store(Request $request)
    {
        $organizationIds = Student::organizationIdsForUser();

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],

            'organization_id' => [
                'required',
                'integer',
                Rule::in(
                    $organizationIds->all()
                ),
            ],

            'source_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'target_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'student_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'student_ids.*' => [
                'integer',
                'distinct',
                'exists:students,id',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Tahun ajaran
    |--------------------------------------------------------------------------
    */

        $academicYear = AcademicYear::findOrFail(
            $validated['academic_year_id']
        );

        abort_unless(
            $academicYear->is_active,
            403,
            'Tahun ajaran sudah ditutup dan tidak dapat digunakan untuk memindahkan siswa.'
        );

        /*
    |--------------------------------------------------------------------------
    | Kelas asal
    |--------------------------------------------------------------------------
    */

        $sourceClass = SchoolClass::query()
            ->whereKey(
                $validated['source_class_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        abort_unless(
            $sourceClass,
            403,
            'Kelas asal tidak valid.'
        );

        /*
    |--------------------------------------------------------------------------
    | Kelas tujuan
    |--------------------------------------------------------------------------
    */

        $targetClass = SchoolClass::query()
            ->whereKey(
                $validated['target_class_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        abort_unless(
            $targetClass,
            403,
            'Kelas tujuan tidak valid.'
        );

        /*
    |--------------------------------------------------------------------------
    | Kelas asal dan tujuan tidak boleh sama
    |--------------------------------------------------------------------------
    */

        if (
            $sourceClass->id ===
            $targetClass->id
        ) {
            return back()
                ->withErrors([
                    'target_class_id' =>
                    'Kelas tujuan harus berbeda dari kelas asal.',
                ])
                ->withInput();
        }

        /*
    |--------------------------------------------------------------------------
    | Perpindahan maksimal satu tingkat
    |--------------------------------------------------------------------------
    */

        if (
            abs(
                $sourceClass->level -
                    $targetClass->level
            ) > 1
        ) {
            return back()
                ->withErrors([
                    'target_class_id' =>
                    'Siswa hanya dapat dipindahkan maksimal satu tingkat dari kelas asal.',
                ])
                ->withInput();
        }

        /*
    |--------------------------------------------------------------------------
    | Pastikan siswa benar-benar berada di kelas asal
    |--------------------------------------------------------------------------
    */

        $studentAcademicYears =
            StudentAcademicYear::query()
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'school_class_id',
                $sourceClass->id
            )
            ->where(
                'status',
                'active'
            )
            ->whereIn(
                'student_id',
                $validated['student_ids']
            )
            ->whereHas(
                'student',
                function ($query) {
                    $query->where(
                        'is_active',
                        true
                    );
                }
            )
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Semua siswa yang dipilih harus valid
    |--------------------------------------------------------------------------
    */

        if (
            $studentAcademicYears->count() !==
            count($validated['student_ids'])
        ) {
            return back()
                ->withErrors([
                    'student_ids' =>
                    'Sebagian siswa tidak berada pada kelas asal atau tidak dapat dipindahkan.',
                ])
                ->withInput();
        }

        /*
    |--------------------------------------------------------------------------
    | Pindahkan siswa
    |--------------------------------------------------------------------------
    |
    | Tidak membuat record baru.
    |
    | Hanya school_class_id yang diubah.
    |
    */

        DB::transaction(function () use (
            $studentAcademicYears,
            $targetClass
        ) {
            foreach (
                $studentAcademicYears
                as $studentAcademicYear
            ) {
                $studentAcademicYear->update([
                    'school_class_id' =>
                    $targetClass->id,
                ]);
            }
        });

        return redirect()
            ->route(
                'admin.students.class-transfer.index',
                [
                    'academic_year_id' =>
                    $validated['academic_year_id'],

                    'organization_id' =>
                    $validated['organization_id'],

                    'source_class_id' =>
                    $validated['source_class_id'],
                ]
            )
            ->with(
                'success',
                'Siswa berhasil dipindahkan ke kelas ' .
                    $targetClass->name . '.'
            );
    }
}
