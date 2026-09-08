<?php

namespace App\Http\Controllers\Admin;

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
        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $academicYearId = $request->input('academic_year_id');
        $organizationId = $request->input('organization_id');
        $schoolClassId = $request->input('school_class_id');
        $subjectId = $request->input('subject_id');

        /*
        |--------------------------------------------------------------------------
        | Tahun Akademik
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
        | Teaching Assignment
        |--------------------------------------------------------------------------
        */

        $assignmentsQuery = TeachingAssignment::query()
            ->where('is_active', true)
            ->with([
                'organization',
                'teacher',
                'schoolClass',
                'subject',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Filter Tahun Akademik
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
        | Minggu
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

        /*
        |--------------------------------------------------------------------------
        | Data Jadwal / Absensi
        |--------------------------------------------------------------------------
        |
        | Untuk sementara waktu dan hari diperoleh dari Attendance.created_at.
        |
        */

        $weeklyAttendances = Attendance::query()
            ->with([
                'teachingAssignment.organization',
                'teachingAssignment.teacher',
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',

                'details.studentAcademicYear.student',
            ])
            ->whereBetween(
                'date',
                [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ]
            )
            ->whereHas(
                'teachingAssignment',
                function ($query) use (
                    $academicYearId,
                    $organizationId,
                    $schoolClassId,
                    $subjectId
                ) {

                    $query->where(
                        'is_active',
                        true
                    );

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

                    if (
                        $organizationId !== null &&
                        $organizationId !== 'all'
                    ) {
                        $query->where(
                            'organization_id',
                            $organizationId
                        );
                    }

                    if (
                        $schoolClassId !== null &&
                        $schoolClassId !== 'all'
                    ) {
                        $query->where(
                            'school_class_id',
                            $schoolClassId
                        );
                    }

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
            ->orderBy('date')
            ->orderBy('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Tampilan Jam
        |--------------------------------------------------------------------------
        |
        | Sementara kita gunakan rentang default.
        | Nantinya akan dipindahkan ke pengaturan Teacher Attendance Admin.
        |
        */

        $teacherAttendanceStart = $request->input(
            'teacherAttendance_start',
            '07:00'
        );

        $teacherAttendanceEnd = $request->input(
            'teacherAttendance_end',
            '16:00'
        );

        $teacherAttendanceInterval = (int) $request->input(
            'teacherAttendance_interval',
            30
        );

        $teacherAttendanceStartTime = \Carbon\Carbon::createFromFormat(
            'H:i',
            $teacherAttendanceStart
        );

        $teacherAttendanceEndTime = \Carbon\Carbon::createFromFormat(
            'H:i',
            $teacherAttendanceEnd
        );

        $teacherAttendanceHours = [];

        $currentTime = $teacherAttendanceStartTime->copy();

        while ($currentTime <= $teacherAttendanceEndTime) {

            $teacherAttendanceHours[] = $currentTime->format('H:i');

            $currentTime->addMinutes(
                $teacherAttendanceInterval
            );
        }


        return view(
            'admin.teacher-attendance.index',
            compact(
                'academicYears',
                'academicYearId',
                'organizationOptions',
                'organizationId',
                'classOptions',
                'schoolClassId',
                'subjectOptions',
                'subjectId',
                'week',
                'weekStart',
                'weekEnd',
                'weeklyAttendances',
                'teacherAttendanceStart',
                'teacherAttendanceEnd',
                'teacherAttendanceInterval',
                'teacherAttendanceHours'
            )
        );
    }

    public function filterOptions(Request $request)
    {
        $academicYearId = $request->input('academic_year_id', 'all');
        $organizationId = $request->input('organization_id', 'all');

        $query = TeachingAssignment::query()
            ->where('is_active', true)
            ->with([
                'schoolClass',
                'subject',
            ]);


        /*
     * Tahun Akademik
     */
        if ($academicYearId !== 'all') {

            $query->whereHas('schoolClass', function ($q) use ($academicYearId) {

                $q->where('academic_year_id', $academicYearId);
            });
        }


        /*
     * Unit
     */
        if ($organizationId !== 'all') {

            $query->where('organization_id', $organizationId);
        }


        /*
     * Kelas
     */
        $classes = $query
            ->get()
            ->pluck('schoolClass')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(function ($schoolClass) {

                return [
                    $schoolClass->id => $schoolClass->name,
                ];
            });


        /*
     * Mata Pelajaran
     */
        $subjects = $query
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
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }

    public function detail(Attendance $attendance)
    {
        $attendance->load([
            'teachingAssignment.organization',
            'teachingAssignment.teacher',
            'teachingAssignment.schoolClass',
            'teachingAssignment.subject',
            'details.studentAcademicYear.student',
        ]);

        $students = $attendance->details
            ->map(function ($detail) {
                return [
                    'nis' => $detail->studentAcademicYear?->student?->nis,
                    'name' => $detail->studentAcademicYear?->student?->name,
                    'status' => $detail->status,
                    'notes' => $detail->notes,
                ];
            })
            ->values();

        return response()->json([
            'attendance' => [
                'id' => $attendance->id,
                'date' => $attendance->date?->format('d/m/Y'),
                'meeting_number' => $attendance->meeting_number,
                'time' => $attendance->created_at?->format('H:i'),
            ],

            'teacher' =>
            $attendance->teachingAssignment?->teacher?->name,

            'organization' =>
            $attendance->teachingAssignment?->organization?->name,

            'class' =>
            $attendance->teachingAssignment?->schoolClass?->name,

            'subject' =>
            $attendance->teachingAssignment?->subject?->name,

            'students' => $students,
        ]);
    }
}
