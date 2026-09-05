<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            'Akun Guru belum terhubung dengan data Guru.'
        );

        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $academicYearId = $request->input('academic_year_id');
        $organizationId = $request->input('organization_id');
        $schoolClassId = $request->input('school_class_id');
        $subjectId = $request->input('subject_id');
        $date = $request->input('date');


        /*
        |--------------------------------------------------------------------------
        | Tahun Ajaran
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        if ($academicYearId === null) {
            $academicYearId = $academicYears->first()?->id;
        }


        /*
        |--------------------------------------------------------------------------
        | Teaching Assignment Guru
        |--------------------------------------------------------------------------
        |
        | Semua pilihan filter diambil dari assignment Guru yang sebenarnya.
        |
        */

        $assignmentsQuery = TeachingAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with([
                'organization',
                'schoolClass',
                'subject',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Filter Tahun Ajaran
        |--------------------------------------------------------------------------
        */

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $assignmentsQuery->whereHas(
                'schoolClass',
                function ($query) use ($academicYearId) {
                    $query->where(
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

        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {
            $assignmentsQuery->where(
                'organization_id',
                $organizationId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Kelas
        |--------------------------------------------------------------------------
        */

        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {
            $assignmentsQuery->where(
                'school_class_id',
                $schoolClassId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        if (
            $subjectId !== null &&
            $subjectId !== 'all'
        ) {
            $assignmentsQuery->where(
                'subject_id',
                $subjectId
            );
        }


        $assignments = $assignmentsQuery->get();


        /*
        |--------------------------------------------------------------------------
        | Options Unit
        |--------------------------------------------------------------------------
        */

        $organizationOptions = TeachingAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with('organization')
            ->get()
            ->pluck('organization')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(function ($organization) {
                return [
                    $organization->id => $organization->name,
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | Options Kelas
        |--------------------------------------------------------------------------
        */

        $classOptions = $assignments
            ->pluck('schoolClass')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(function ($class) {
                return [
                    $class->id => $class->name,
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | Options Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        $subjectOptions = $assignments
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(function ($subject) {
                return [
                    $subject->id => $subject->name,
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | Data Kehadiran Guru
        |--------------------------------------------------------------------------
        |
        | Attendance hanya diambil dari TeachingAssignment milik Guru.
        |
        */

        $attendancesQuery = Attendance::query()
            ->with([
                'teachingAssignment.teacher',
                'teachingAssignment.organization',
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',
                'details.studentAcademicYear.student',
            ])
            ->withCount('details')
            ->whereHas(
                'teachingAssignment',
                function ($query) use (
                    $teacher,
                    $academicYearId,
                    $organizationId,
                    $schoolClassId,
                    $subjectId
                ) {

                    $query->where(
                        'teacher_id',
                        $teacher->id
                    );

                    $query->where(
                        'is_active',
                        true
                    );


                    /*
                    |--------------------------------------------------------------
                    | Tahun Ajaran
                    |--------------------------------------------------------------
                    */

                    if (
                        $academicYearId !== null &&
                        $academicYearId !== 'all'
                    ) {
                        $query->whereHas(
                            'schoolClass',
                            function ($q) use ($academicYearId) {
                                $q->where(
                                    'academic_year_id',
                                    $academicYearId
                                );
                            }
                        );
                    }


                    /*
                    |--------------------------------------------------------------
                    | Unit
                    |--------------------------------------------------------------
                    */

                    if (
                        $organizationId !== null &&
                        $organizationId !== 'all'
                    ) {
                        $query->where(
                            'organization_id',
                            $organizationId
                        );
                    }


                    /*
                    |--------------------------------------------------------------
                    | Kelas
                    |--------------------------------------------------------------
                    */

                    if (
                        $schoolClassId !== null &&
                        $schoolClassId !== 'all'
                    ) {
                        $query->where(
                            'school_class_id',
                            $schoolClassId
                        );
                    }


                    /*
                    |--------------------------------------------------------------
                    | Mata Pelajaran
                    |--------------------------------------------------------------
                    */

                    if (
                        $subjectId !== null &&
                        $subjectId !== 'all'
                    ) {
                        $query->where(
                            'subject_id',
                            $subjectId
                        );
                    }
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Filter Tanggal
        |--------------------------------------------------------------------------
        */

        if ($date) {
            $attendancesQuery->whereDate(
                'date',
                $date
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Urutan
        |--------------------------------------------------------------------------
        */

        $attendances = $attendancesQuery
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Chart Absensi Mingguan
        |--------------------------------------------------------------------------
        |
        | Chart menggunakan data Attendance yang sudah tersimpan.
        | Hari     = date
        | Jam      = created_at
        | Isi      = Kelas + Mata Pelajaran + Pertemuan
        |
        */
        /*
        |--------------------------------------------------------------------------
        | Minggu Chart
        |--------------------------------------------------------------------------
        */

        $week = $request->input('week');

        if ($week) {
            $weekDate = \Carbon\Carbon::parse($week);
        } else {
            $weekDate = now();
        }

        $weekStart = $weekDate->copy()->startOfWeek();
        $weekEnd = $weekDate->copy()->endOfWeek();

        $weeklyAttendancesQuery = Attendance::query()
            ->with([
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',
            ])
            ->whereHas(
                'teachingAssignment',
                function ($query) use (
                    $teacher,
                    $academicYearId,
                    $organizationId,
                    $schoolClassId,
                    $subjectId
                ) {

                    $query->where(
                        'teacher_id',
                        $teacher->id
                    );

                    $query->where(
                        'is_active',
                        true
                    );

                    /*
            |--------------------------------------------------------------
            | Tahun Ajaran
            |--------------------------------------------------------------
            */

                    if (
                        $academicYearId !== null &&
                        $academicYearId !== 'all'
                    ) {
                        $query->whereHas(
                            'schoolClass',
                            function ($q) use ($academicYearId) {
                                $q->where(
                                    'academic_year_id',
                                    $academicYearId
                                );
                            }
                        );
                    }

                    /*
            |--------------------------------------------------------------
            | Unit
            |--------------------------------------------------------------
            */

                    if (
                        $organizationId !== null &&
                        $organizationId !== 'all'
                    ) {
                        $query->where(
                            'organization_id',
                            $organizationId
                        );
                    }

                    /*
            |--------------------------------------------------------------
            | Kelas
            |--------------------------------------------------------------
            */

                    if (
                        $schoolClassId !== null &&
                        $schoolClassId !== 'all'
                    ) {
                        $query->where(
                            'school_class_id',
                            $schoolClassId
                        );
                    }

                    /*
            |--------------------------------------------------------------
            | Mata Pelajaran
            |--------------------------------------------------------------
            */

                    if (
                        $subjectId !== null &&
                        $subjectId !== 'all'
                    ) {
                        $query->where(
                            'subject_id',
                            $subjectId
                        );
                    }
                }
            )
            ->whereBetween(
                'date',
                [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ]
            );


        $weeklyAttendances = $weeklyAttendancesQuery
            ->orderBy('date')
            ->orderBy('created_at')
            ->get();


        return view(
            'guru.teacher-attendance.index',
            compact(
                'attendances',
                'academicYears',
                'academicYearId',
                'organizationOptions',
                'organizationId',
                'classOptions',
                'schoolClassId',
                'subjectOptions',
                'subjectId',
                'date',
                'weeklyAttendances',
                'weekStart',
                'weekEnd',
                'week'
            )
        );
    }

    public function filterOptions(Request $request)
    {
        $teacher = $request->user()->teacher;

        abort_unless(
            $teacher,
            403,
            'Akun Guru belum terhubung dengan data Guru.'
        );

        $academicYearId = $request->input('academic_year_id');
        $organizationId = $request->input('organization_id');

        $query = TeachingAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with([
                'organization',
                'schoolClass',
                'subject',
            ]);

        /*
    |--------------------------------------------------------------------------
    | Tahun Ajaran
    |--------------------------------------------------------------------------
    */

        if (
            $academicYearId !== null &&
            $academicYearId !== '' &&
            $academicYearId !== 'all'
        ) {
            $query->whereHas(
                'schoolClass',
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
    | Unit
    |--------------------------------------------------------------------------
    */

        if (
            $organizationId !== null &&
            $organizationId !== '' &&
            $organizationId !== 'all'
        ) {
            $query->where(
                'organization_id',
                $organizationId
            );
        }


        $assignments = $query->get();


        /*
    |--------------------------------------------------------------------------
    | Unit
    |--------------------------------------------------------------------------
    */

        $organizations = $assignments
            ->pluck('organization')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(function ($organization) {
                return [
                    'id' => $organization->id,
                    'name' => $organization->name,
                ];
            });


        /*
    |--------------------------------------------------------------------------
    | Kelas
    |--------------------------------------------------------------------------
    */

        $classes = $assignments
            ->pluck('schoolClass')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                ];
            });


        /*
    |--------------------------------------------------------------------------
    | Mata Pelajaran
    |--------------------------------------------------------------------------
    */

        $subjects = $assignments
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ];
            });


        return response()->json([
            'organizations' => $organizations,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }
}
