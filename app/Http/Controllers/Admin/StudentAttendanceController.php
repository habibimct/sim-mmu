<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceDetail;
use App\Models\Organization;
use App\Models\SchoolClass;
use App\Models\StudentAcademicYear;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang dapat diakses user
        |--------------------------------------------------------------------------
        */
        $organizationIds = Organization::accessibleIdsForUser($user);

        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */
        $academicYearId = $request->input('academic_year_id', 'all');
        $organizationId = $request->input('organization_id', 'all');
        $schoolClassId = $request->input('school_class_id', 'all');
        $studentId = $request->input('student_id', 'all');
        $subjectId = $request->input('subject_id', 'all');

        /*
        |--------------------------------------------------------------------------
        | Tahun Akademik
        |--------------------------------------------------------------------------
        */
        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Default Tahun Akademik
        |--------------------------------------------------------------------------
        */
        if ($academicYearId === 'all' && $academicYears->isNotEmpty()) {
            $academicYearId = $academicYears->first()->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Pilihan Unit
        |--------------------------------------------------------------------------
        */
        $organizationOptions = StudentAcademicYear::query()
            ->whereIn('organization_id', $organizationIds)
            ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            })
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
        | Pilihan Kelas
        |--------------------------------------------------------------------------
        */
        $classOptions = SchoolClass::query()
            ->whereIn('organization_id', $organizationIds)
            ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            })
            ->when($organizationId !== 'all', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        /*
        |--------------------------------------------------------------------------
        | Pilihan Siswa
        |--------------------------------------------------------------------------
        */
        $studentOptions = StudentAcademicYear::query()
            ->whereIn('organization_id', $organizationIds)
            ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            })
            ->when($organizationId !== 'all', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->when($schoolClassId !== 'all', function ($query) use ($schoolClassId) {
                $query->where('school_class_id', $schoolClassId);
            })
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(function ($student) {
                return [
                    $student->id => $student->name,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Pilihan Mata Pelajaran
        |--------------------------------------------------------------------------
        */
        $subjectOptions = TeachingAssignment::query()
            ->where('is_active', true)
            ->whereIn('organization_id', $organizationIds)
            ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                $query->whereHas('schoolClass', function ($query) use ($academicYearId) {
                    $query->where('academic_year_id', $academicYearId);
                });
            })
            ->when($organizationId !== 'all', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->when($schoolClassId !== 'all', function ($query) use ($schoolClassId) {
                $query->where('school_class_id', $schoolClassId);
            })
            ->with('subject')
            ->get()
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
        | Pengaturan Chart
        |--------------------------------------------------------------------------
        */

        $startHour = (int) $request->input('start_hour', 7);
        $endHour = (int) $request->input('end_hour', 16);
        $interval = (int) $request->input('interval', 60);

        /*
        |--------------------------------------------------------------------------
        | Validasi sederhana pengaturan chart
        |--------------------------------------------------------------------------
        */

        $startHour = max(0, min($startHour, 23));
        $endHour = max(1, min($endHour, 24));

        if ($endHour <= $startHour) {
            $endHour = min($startHour + 1, 24);
        }

        if (! in_array($interval, [15, 30, 45, 60], true)) {
            $interval = 60;
        }

        /*
        |--------------------------------------------------------------------------
        | Minggu
        |--------------------------------------------------------------------------
        */

        $weekStart = $request->filled('week')
            ? now()->parse($request->input('week'))->startOfWeek()
            : now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        /*
        |--------------------------------------------------------------------------
        | Absensi Siswa Mingguan
        |--------------------------------------------------------------------------
        */
        $weeklyAttendances = AttendanceDetail::query()
            ->with([
                'studentAcademicYear.student',
                'attendance.teachingAssignment.organization',
                'attendance.teachingAssignment.teacher',
                'attendance.teachingAssignment.schoolClass',
                'attendance.teachingAssignment.subject',
            ])
            ->whereHas('studentAcademicYear', function ($query) use (
                $organizationIds,
                $academicYearId,
                $organizationId,
                $schoolClassId,
                $studentId
            ) {
                $query->whereIn('organization_id', $organizationIds)
                    ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                        $query->where('academic_year_id', $academicYearId);
                    })
                    ->when($organizationId !== 'all', function ($query) use ($organizationId) {
                        $query->where('organization_id', $organizationId);
                    })
                    ->when($schoolClassId !== 'all', function ($query) use ($schoolClassId) {
                        $query->where('school_class_id', $schoolClassId);
                    })
                    ->when($studentId !== 'all', function ($query) use ($studentId) {
                        $query->where('student_id', $studentId);
                    });
            })
            ->whereHas('attendance', function ($query) use (
                $weekStart,
                $weekEnd,
                $organizationIds,
                $subjectId
            ) {
                $query->whereBetween('date', [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ])
                    ->whereIn('organization_id', $organizationIds)
                    ->whereHas('teachingAssignment', function ($query) use ($subjectId) {
                        $query->where('is_active', true)
                            ->when($subjectId !== 'all', function ($query) use ($subjectId) {
                                $query->where('subject_id', $subjectId);
                            });
                    });
            })
            ->get();

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */
        return view('admin.student-attendance.index', compact(
            'weeklyAttendances',
            'weekStart',
            'weekEnd',
            'academicYears',
            'academicYearId',
            'organizationOptions',
            'organizationId',
            'classOptions',
            'schoolClassId',
            'studentOptions',
            'studentId',
            'subjectOptions',
            'subjectId',
            'startHour',
            'endHour',
            'interval',
        ));
    }


    public function filterOptions(Request $request)
    {
        $user = $request->user();

        $organizationIds = Organization::accessibleIdsForUser($user);

        $academicYearId = $request->input('academic_year_id', 'all');
        $organizationId = $request->input('organization_id', 'all');
        $schoolClassId = $request->input('school_class_id', 'all');

        /*
    |--------------------------------------------------------------------------
    | Kelas
    |--------------------------------------------------------------------------
    */
        $classOptions = SchoolClass::query()
            ->whereIn('organization_id', $organizationIds)
            ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            })
            ->when($organizationId !== 'all', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        /*
    |--------------------------------------------------------------------------
    | Siswa
    |--------------------------------------------------------------------------
    */
        $studentOptions = StudentAcademicYear::query()
            ->whereIn('organization_id', $organizationIds)
            ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            })
            ->when($organizationId !== 'all', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->when($schoolClassId !== 'all', function ($query) use ($schoolClassId) {
                $query->where('school_class_id', $schoolClassId);
            })
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(function ($student) {
                return [
                    $student->id => $student->name,
                ];
            });

        /*
    |--------------------------------------------------------------------------
    | Mata Pelajaran
    |--------------------------------------------------------------------------
    */
        $subjectOptions = TeachingAssignment::query()
            ->where('is_active', true)
            ->whereIn('organization_id', $organizationIds)
            ->when($academicYearId !== 'all', function ($query) use ($academicYearId) {
                $query->whereHas('schoolClass', function ($query) use ($academicYearId) {
                    $query->where('academic_year_id', $academicYearId);
                });
            })
            ->when($organizationId !== 'all', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->when($schoolClassId !== 'all', function ($query) use ($schoolClassId) {
                $query->where('school_class_id', $schoolClassId);
            })
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(function ($subject) {
                return [
                    $subject->id => $subject->name,
                ];
            });

        return response()->json([
            'classes' => $classOptions,
            'students' => $studentOptions,
            'subjects' => $subjectOptions,
        ]);
    }
}
