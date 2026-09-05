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
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentPlacementController extends Controller
{
    /**
     * Menampilkan halaman penempatan siswa.
     */
    public function index(Request $request): View
    {
        $organizationIds =
            $this->placementOrganizationIds();

        /*
        |--------------------------------------------------------------------------
        | Tahun ajaran
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        $academicYearId = $request->integer(
            'academic_year_id'
        );

        if (! $academicYearId) {
            $academicYearId = AcademicYear::query()
                ->where('is_active', true)
                ->orderByDesc('start_date')
                ->value('id');
        }

        /*
        |--------------------------------------------------------------------------
        | Organisasi
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

        $organizationId = $request->integer(
            'organization_id'
        );

        if (
            ! $organizationId &&
            $organizationIds->count() === 1
        ) {
            $organizationId =
                $organizationIds->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Kelas
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
        | Kelas tujuan penempatan
        |--------------------------------------------------------------------------
        */

        $schoolClassId = $request->integer(
            'school_class_id'
        );

        /*
        |--------------------------------------------------------------------------
        | Siswa yang belum mempunyai penempatan
        | pada tahun ajaran tersebut
        |--------------------------------------------------------------------------
        */

        $students = collect();

        if (
            $academicYearId &&
            $organizationId &&
            $schoolClassId &&
            $classes->contains(
                'id',
                $schoolClassId
            )
        ) {
            $students = Student::query()
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'is_active',
                    true
                )
                ->whereDoesntHave(
                    'studentAcademicYears',
                    function ($query) use (
                        $academicYearId
                    ) {
                        $query->where(
                            'academic_year_id',
                            $academicYearId
                        );
                    }
                )
                ->orderBy('name')
                ->get();
        }

        $selectedClass = null;
        $placedStudents = collect();

        if (
            $schoolClassId &&
            $classes->contains('id', $schoolClassId)
        ) {
            $selectedClass = $classes->firstWhere(
                'id',
                $schoolClassId
            );

            $placedStudents = StudentAcademicYear::query()
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
                    $schoolClassId
                )
                ->where(
                    'status',
                    'active'
                )
                ->whereHas('student', function ($query) {
                    $query->where(
                        'is_active',
                        true
                    );
                })
                ->orderBy('id')
                ->get();
        }

        return view(
            'admin.students.placement',
            compact(
                'academicYears',
                'organizations',
                'classes',
                'students',
                'academicYearId',
                'organizationId',
                'schoolClassId',
                'placedStudents',
                'selectedClass',
            )
        );
    }

    /**
     * Menempatkan siswa ke tahun ajaran dan kelas.
     */
    public function store(Request $request)
    {
        $organizationIds =
            $this->placementOrganizationIds();

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

            'school_class_id' => [
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
        | Tahun ajaran harus masih terbuka
        |--------------------------------------------------------------------------
        */

        $academicYear =
            AcademicYear::findOrFail(
                $validated['academic_year_id']
            );

        if (! $academicYear->is_active) {
            return back()
                ->withErrors([
                    'academic_year_id' =>
                    'Tahun ajaran sudah ditutup dan tidak dapat digunakan untuk penempatan siswa.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan kelas sesuai dengan tahun ajaran
        | dan organisasi
        |--------------------------------------------------------------------------
        */

        $schoolClass = SchoolClass::query()
            ->whereKey(
                $validated['school_class_id']
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

        if (! $schoolClass) {
            return back()
                ->withErrors([
                    'school_class_id' =>
                    'Kelas tidak sesuai dengan tahun ajaran atau unit yang dipilih.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan semua siswa berada dalam organisasi
        | yang sama dan masih aktif
        |--------------------------------------------------------------------------
        */

        $students = Student::query()
            ->forCurrentUser()
            ->whereIn(
                'id',
                $validated['student_ids']
            )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'is_active',
                true
            )
            ->get();

        if (
            $students->count() !==
            count($validated['student_ids'])
        ) {
            return back()
                ->withErrors([
                    'student_ids' =>
                    'Sebagian siswa tidak valid, tidak aktif, atau berada di luar kewenangan Anda.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan penempatan
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $students,
            $validated,
            $schoolClass,
            $academicYear
        ) {
            foreach ($students as $student) {

                /*
                |----------------------------------------------------------------------
                | Cegah duplikasi tahun ajaran
                |----------------------------------------------------------------------
                */

                $exists =
                    StudentAcademicYear::query()
                    ->where(
                        'student_id',
                        $student->id
                    )
                    ->where(
                        'academic_year_id',
                        $academicYear->id
                    )
                    ->exists();

                if ($exists) {
                    continue;
                }

                StudentAcademicYear::create([
                    'student_id' =>
                    $student->id,

                    'academic_year_id' =>
                    $academicYear->id,

                    'organization_id' =>
                    $schoolClass->organization_id,

                    'school_class_id' =>
                    $schoolClass->id,

                    'status' =>
                    'active',

                    'started_at' =>
                    $academicYear->start_date,

                    'ended_at' =>
                    null,
                ]);
            }
        });

        return redirect()
            ->route(
                'admin.students.placement.index',
                [
                    'academic_year_id' =>
                    $validated['academic_year_id'],

                    'organization_id' =>
                    $validated['organization_id'],

                    'school_class_id' =>
                    $validated['school_class_id'],
                ]
            )
            ->with(
                'success',
                'Siswa berhasil ditempatkan ke kelas ' .
                    $schoolClass->name . '.'
            );
    }

    /**
     * Organisasi yang boleh dikelola pada Placement.
     */
    private function placementOrganizationIds()
    {
        $user = Auth::user();

        /*
    |--------------------------------------------------------------------------
    | Admin INDUK / Admin Sistem
    |--------------------------------------------------------------------------
    |
    | Admin INDUK mempunyai kewenangan lintas unit.
    |
    */

        if (
            $user->roles->contains(
                'code',
                'admin_sistem'
            )
        ) {
            return Organization::query()
                ->where(
                    'type',
                    'unit'
                )
                ->where(
                    'is_active',
                    true
                )
                ->pluck('id');
        }

        /*
    |--------------------------------------------------------------------------
    | Admin Unit
    |--------------------------------------------------------------------------
    */

        return Student::organizationIdsForUser();
    }
}
