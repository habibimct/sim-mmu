<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Organization;
use App\Models\StudentAcademicYear;
use App\Models\TeachingAssignment;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exports\AttendanceTeacherReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AttendanceStudentReportExport;


class AttendanceReportController extends Controller
{
    /**
     * Laporan Absensi
     *
     * Tab:
     * - guru  : mingguan
     * - siswa : bulanan
     */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'guru');

        if (! in_array($tab, ['guru', 'siswa'], true)) {
            $tab = 'guru';
        }

        if ($tab === 'siswa') {
            return $this->studentReport($request);
        }

        return $this->teacherReport($request);
    }

    /**
     * ==========================================================
     * ABSENSI GURU
     * ==========================================================
     *
     * Sumber aktivitas:
     * Attendance.created_at
     *
     * Hari:
     * Attendance.date
     */
    protected function teacherReport(Request $request): View
    {
        $user = $request->user();

        $tab = 'guru';

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
        $academicYearId = $request->input('academic_year_id');
        $organizationId = $request->input('organization_id');
        $schoolClassId = $request->input('school_class_id');
        $subjectId = $request->input('subject_id');

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
        | Organisasi
        |--------------------------------------------------------------------------
        */
        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Teaching Assignment
        |--------------------------------------------------------------------------
        */
        $assignmentsQuery = TeachingAssignment::query()
            ->where('is_active', true)
            ->whereIn('organization_id', $organizationIds)
            ->with([
                'organization',
                'teacher',
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
        | Unit
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
        | Kelas
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
        | Mata Pelajaran
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
        | Pilihan Kelas
        |--------------------------------------------------------------------------
        */
        $schoolClassesQuery = \App\Models\SchoolClass::query()
            ->whereIn('organization_id', $organizationIds);

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $schoolClassesQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }

        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {
            $schoolClassesQuery->where(
                'organization_id',
                $organizationId
            );
        }

        $schoolClasses = $schoolClassesQuery
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pilihan Mata Pelajaran
        |--------------------------------------------------------------------------
        */
        $subjects = $assignments
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name');

        /*
        |--------------------------------------------------------------------------
        | Minggu
        |--------------------------------------------------------------------------
        */
        $week = $request->input('week');

        $weekDate = $week
            ? Carbon::parse($week)
            : now();

        $weekStart = $weekDate->copy()->startOfWeek();
        $weekEnd = $weekDate->copy()->endOfWeek();

        /*
        |--------------------------------------------------------------------------
        | Data Absensi Guru
        |--------------------------------------------------------------------------
        |
        | Hari berasal dari attendance.date
        | Jam berasal dari attendance.created_at
        |
        */
        $weeklyAttendances = Attendance::query()
            ->with([
                'teachingAssignment.organization',
                'teachingAssignment.teacher',
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',
            ])
            ->whereIn('organization_id', $organizationIds)
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
                    $query->where('is_active', true);

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
        | Rentang Jam
        |--------------------------------------------------------------------------
        |
        | Mengikuti tampilan Absensi Guru yang sudah ada.
        |
        */
        $teacherAttendanceStart =
            $request->input(
                'attendance_start',
                '07:00'
            );

        $teacherAttendanceEnd =
            $request->input(
                'attendance_end',
                '16:00'
            );

        $teacherAttendanceInterval =
            (int) $request->input(
                'attendance_interval',
                30
            );

        $startTime = Carbon::createFromFormat(
            'H:i',
            $teacherAttendanceStart
        );

        $endTime = Carbon::createFromFormat(
            'H:i',
            $teacherAttendanceEnd
        );

        $teacherAttendanceHours = [];

        $currentTime = $startTime->copy();

        while ($currentTime <= $endTime) {
            $teacherAttendanceHours[] =
                $currentTime->format('H:i');

            $currentTime->addMinutes(
                $teacherAttendanceInterval
            );
        }

        return view(
            'admin.reports.attendance.index',
            compact(
                'tab',
                'academicYears',
                'academicYearId',
                'organizations',
                'organizationId',
                'schoolClasses',
                'schoolClassId',
                'subjects',
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

    /**
     * Menentukan status absensi siswa per hari.
     *
     * Aturan:
     * 1. Jika minimal 1 kali hadir → Hadir.
     * 2. Jika tidak hadir sama sekali → ambil status terbanyak.
     * 3. Jika seri → Sakit > Izin > Alpa.
     */
    protected function buildDailyStatuses($attendanceDetails)
    {
        return $attendanceDetails
            ->filter(fn($detail) => $detail->attendance?->date)
            ->groupBy(
                fn($detail) =>
                $detail->attendance->date->format('Y-m-d')
            )
            ->map(function ($dayDetails) {

                $statuses = $dayDetails->pluck('status');

                // Jika minimal satu kali hadir,
                // maka hari tersebut dianggap HADIR.
                if ($statuses->contains('present')) {
                    return 'present';
                }

                // Tidak ada hadir.
                // Hitung jumlah masing-masing status.
                $counts = [
                    'sick' => $statuses
                        ->filter(fn($status) => $status === 'sick')
                        ->count(),

                    'permission' => $statuses
                        ->filter(fn($status) => $status === 'permission')
                        ->count(),

                    'absent' => $statuses
                        ->filter(fn($status) => $status === 'absent')
                        ->count(),
                ];

                // Prioritas jika jumlah sama:
                // sick > permission > absent
                $winner = 'sick';
                $maxCount = $counts['sick'];

                if ($counts['permission'] > $maxCount) {
                    $winner = 'permission';
                    $maxCount = $counts['permission'];
                }

                if ($counts['absent'] > $maxCount) {
                    $winner = 'absent';
                }

                return $winner;
            });
    }

    /**
     * ==========================================================
     * ABSENSI SISWA
     * ==========================================================
     *
     * Laporan siswa menggunakan periode bulanan.
     */
    protected function studentReport(Request $request): View
    {
        $user = $request->user();

        $tab = 'siswa';

        /*
        |--------------------------------------------------------------------------
        | Organisasi
        |--------------------------------------------------------------------------
        */
        $organizationIds = Organization::accessibleIdsForUser($user);

        /*
        |--------------------------------------------------------------------------
        | Periode
        |--------------------------------------------------------------------------
        */
        $month = (int) $request->input(
            'month',
            now()->month
        );

        $year = (int) $request->input(
            'year',
            now()->year
        );

        /*
        |--------------------------------------------------------------------------
        | Tahun Ajaran
        |--------------------------------------------------------------------------
        */
        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        $academicYearId = $request->input(
            'academic_year_id'
        );

        if ($academicYearId === null) {
            $academicYearId =
                $academicYears->first()?->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Organisasi
        |--------------------------------------------------------------------------
        */
        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->orderBy('name')
            ->get();

        $organizationId =
            $request->input('organization_id');

        /*
        |--------------------------------------------------------------------------
        | Kelas
        |--------------------------------------------------------------------------
        */
        $schoolClassId =
            $request->input('school_class_id');


        /*
        |--------------------------------------------------------------------------
        | Mapel
        |--------------------------------------------------------------------------
        */
        $subjectId =
            $request->input('subject_id');

        /*
        |--------------------------------------------------------------------------
        | Data Siswa
        |--------------------------------------------------------------------------
        |
        | Untuk tahap pertama kita ambil siswa yang memiliki
        | StudentAcademicYear aktif pada tahun ajaran terpilih.
        |
        */
        $studentQuery = StudentAcademicYear::query()
            ->with([
                'student',
                'organization',
                'schoolClass',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where('status', 'active');

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $studentQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }

        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {
            $studentQuery->where(
                'organization_id',
                $organizationId
            );
        }

        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {
            $studentQuery->where(
                'school_class_id',
                $schoolClassId
            );
        }

        $studentAcademicYears =
            $studentQuery
            ->orderBy('organization_id')
            ->get();


        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        /*
        |--------------------------------------------------------------------------
        | Rentang Bulan
        |--------------------------------------------------------------------------
        */
        $monthStart = Carbon::create(
            $year,
            $month,
            1
        )->startOfMonth();

        $monthEnd = $monthStart
            ->copy()
            ->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Detail Absensi
        |--------------------------------------------------------------------------
        */
        $attendanceDetails =
            \App\Models\AttendanceDetail::query()
            ->with([
                'attendance',
            ])
            ->whereHas(
                'attendance',
                function ($query) use (
                    $monthStart,
                    $monthEnd,
                    $organizationIds,
                    $subjectId
                ) {
                    $query
                        ->whereIn(
                            'organization_id',
                            $organizationIds
                        )
                        ->whereBetween(
                            'date',
                            [
                                $monthStart->toDateString(),
                                $monthEnd->toDateString(),
                            ]
                        );

                    if (
                        $subjectId !== null &&
                        $subjectId !== 'all'
                    ) {
                        $query->whereHas(
                            'teachingAssignment',
                            function ($q) use ($subjectId) {
                                $q->where(
                                    'subject_id',
                                    $subjectId
                                );
                            }
                        );
                    }
                }
            )
            ->whereIn(
                'student_academic_year_id',
                $studentAcademicYears->pluck('id')
            )
            ->get()
            ->groupBy(
                'student_academic_year_id'
            );

        $dailyStatuses = $attendanceDetails->map(
            fn($details) => $this->buildDailyStatuses($details)
        );

        /*
        |--------------------------------------------------------------------------
        | Pilihan Kelas
        |--------------------------------------------------------------------------
        */
        $schoolClasses = $studentAcademicYears
            ->pluck('schoolClass')
            ->filter()
            ->unique('id')
            ->sortBy('name');

        return view(
            'admin.reports.attendance.index',
            compact(
                'tab',
                'academicYears',
                'academicYearId',
                'organizations',
                'organizationId',
                'schoolClasses',
                'schoolClassId',
                'subjects',
                'subjectId',
                'month',
                'year',
                'monthStart',
                'monthEnd',
                'studentAcademicYears',
                'attendanceDetails',
                'dailyStatuses',
            )
        );
    }

    /**
     * ==========================================================
     * EXPORT EXCEL ABSENSI GURU
     * ==========================================================
     */
    public function excel(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */
        $academicYearId =
            $request->input('academic_year_id');

        $organizationId =
            $request->input('organization_id');

        $schoolClassId =
            $request->input('school_class_id');

        $subjectId =
            $request->input('subject_id');

        /*
    |--------------------------------------------------------------------------
    | Minggu
    |--------------------------------------------------------------------------
    */
        $week = $request->input('week');

        $weekDate = $week
            ? Carbon::parse($week)
            : now();

        $weekStart =
            $weekDate->copy()->startOfWeek();

        $weekEnd =
            $weekDate->copy()->endOfWeek();

        /*
    |--------------------------------------------------------------------------
    | Absensi Guru
    |--------------------------------------------------------------------------
    */
        $weeklyAttendances =
            Attendance::query()
            ->with([
                'teachingAssignment.organization',
                'teachingAssignment.teacher',
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
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
                            function ($q) use (
                                $academicYearId
                            ) {
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
    | Jam
    |--------------------------------------------------------------------------
    */
        $teacherAttendanceStart =
            $request->input(
                'attendance_start',
                '07:00'
            );

        $teacherAttendanceEnd =
            $request->input(
                'attendance_end',
                '16:00'
            );

        $teacherAttendanceInterval =
            (int) $request->input(
                'attendance_interval',
                30
            );

        $startTime =
            Carbon::createFromFormat(
                'H:i',
                $teacherAttendanceStart
            );

        $endTime =
            Carbon::createFromFormat(
                'H:i',
                $teacherAttendanceEnd
            );

        $teacherAttendanceHours = [];

        $currentTime =
            $startTime->copy();

        while ($currentTime <= $endTime) {

            $teacherAttendanceHours[] =
                $currentTime->format('H:i');

            $currentTime->addMinutes(
                $teacherAttendanceInterval
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Informasi Filter
    |--------------------------------------------------------------------------
    */
        $academicYear = null;

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $academicYear =
                AcademicYear::find(
                    $academicYearId
                );
        }

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

        $schoolClass = null;

        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {
            $schoolClass =
                \App\Models\SchoolClass::whereIn(
                    'organization_id',
                    $organizationIds
                )->find(
                    $schoolClassId
                );
        }

        $subject = null;

        if (
            $subjectId !== null &&
            $subjectId !== 'all'
        ) {
            $subject =
                \App\Models\Subject::find(
                    $subjectId
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */
        return Excel::download(
            new AttendanceTeacherReportExport(
                $weeklyAttendances,
                $weekStart,
                $weekEnd,
                $teacherAttendanceHours,
                $teacherAttendanceInterval,
                $academicYear,
                $organization,
                $schoolClass,
                $subject
            ),
            'laporan-absensi-guru-' .
                $weekStart->format('Y-m-d') .
                '-sampai-' .
                $weekEnd->format('Y-m-d') .
                '.xlsx'
        );
    }

    /**
     * ==========================================================
     * EXPORT PDF ABSENSI GURU
     * ==========================================================
     */
    public function pdf(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */
        $academicYearId =
            $request->input('academic_year_id');

        $organizationId =
            $request->input('organization_id');

        $schoolClassId =
            $request->input('school_class_id');

        $subjectId =
            $request->input('subject_id');

        /*
    |--------------------------------------------------------------------------
    | Minggu
    |--------------------------------------------------------------------------
    */
        $week = $request->input('week');

        $weekDate = $week
            ? Carbon::parse($week)
            : now();

        $weekStart =
            $weekDate->copy()->startOfWeek();

        $weekEnd =
            $weekDate->copy()->endOfWeek();

        /*
    |--------------------------------------------------------------------------
    | Absensi Guru
    |--------------------------------------------------------------------------
    */
        $weeklyAttendances =
            Attendance::query()
            ->with([
                'teachingAssignment.organization',
                'teachingAssignment.teacher',
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
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
                            function ($q) use (
                                $academicYearId
                            ) {
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
    | Rentang Jam
    |--------------------------------------------------------------------------
    */
        $teacherAttendanceStart =
            $request->input(
                'attendance_start',
                '07:00'
            );

        $teacherAttendanceEnd =
            $request->input(
                'attendance_end',
                '16:00'
            );

        $teacherAttendanceInterval =
            (int) $request->input(
                'attendance_interval',
                30
            );

        $startTime =
            Carbon::createFromFormat(
                'H:i',
                $teacherAttendanceStart
            );

        $endTime =
            Carbon::createFromFormat(
                'H:i',
                $teacherAttendanceEnd
            );

        $teacherAttendanceHours = [];

        $currentTime =
            $startTime->copy();

        while ($currentTime <= $endTime) {

            $teacherAttendanceHours[] =
                $currentTime->format('H:i');

            $currentTime->addMinutes(
                $teacherAttendanceInterval
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Informasi Filter
    |--------------------------------------------------------------------------
    */
        $academicYear = null;

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $academicYear =
                AcademicYear::find(
                    $academicYearId
                );
        }

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

        $schoolClass = null;

        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {
            $schoolClass =
                \App\Models\SchoolClass::whereIn(
                    'organization_id',
                    $organizationIds
                )->find(
                    $schoolClassId
                );
        }

        $subject = null;

        if (
            $subjectId !== null &&
            $subjectId !== 'all'
        ) {
            $subject =
                \App\Models\Subject::find(
                    $subjectId
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */
        $pdf = Pdf::loadView(
            'admin.reports.attendance.exports.teacher-pdf',
            [
                'weeklyAttendances' =>
                $weeklyAttendances,

                'weekStart' =>
                $weekStart,

                'weekEnd' =>
                $weekEnd,

                'teacherAttendanceHours' =>
                $teacherAttendanceHours,

                'teacherAttendanceInterval' =>
                $teacherAttendanceInterval,

                'academicYear' =>
                $academicYear,

                'organization' =>
                $organization,

                'schoolClass' =>
                $schoolClass,

                'subject' =>
                $subject,
            ]
        );

        $pdf->setPaper(
            'a4',
            'landscape'
        );

        return $pdf->download(
            'laporan-absensi-guru-' .
                $weekStart->format('Y-m-d') .
                '-sampai-' .
                $weekEnd->format('Y-m-d') .
                '.pdf'
        );
    }

    /**
     * ==========================================================
     * EXPORT EXCEL ABSENSI SISWA
     * ==========================================================
     */
    public function studentExcel(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */
        $month = (int) $request->input(
            'month',
            now()->month
        );

        $year = (int) $request->input(
            'year',
            now()->year
        );

        $academicYearId =
            $request->input('academic_year_id');

        $organizationId =
            $request->input('organization_id');

        $schoolClassId =
            $request->input('school_class_id');

        $subjectId =
            $request->input('subject_id');

        /*
    |--------------------------------------------------------------------------
    | Siswa
    |--------------------------------------------------------------------------
    */
        $studentQuery =
            StudentAcademicYear::query()
            ->with([
                'student',
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
            );

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $studentQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }

        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {
            $studentQuery->where(
                'organization_id',
                $organizationId
            );
        }

        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {
            $studentQuery->where(
                'school_class_id',
                $schoolClassId
            );
        }

        $studentAcademicYears =
            $studentQuery
            ->orderBy('organization_id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Bulan
    |--------------------------------------------------------------------------
    */
        $monthStart =
            Carbon::create(
                $year,
                $month,
                1
            )->startOfMonth();

        $monthEnd =
            $monthStart
            ->copy()
            ->endOfMonth();

        /*
    |--------------------------------------------------------------------------
    | Detail Absensi
    |--------------------------------------------------------------------------
    */
        $attendanceDetails =
            \App\Models\AttendanceDetail::query()
            ->with([
                'attendance',
            ])
            ->whereHas(
                'attendance',
                function ($query) use (
                    $monthStart,
                    $monthEnd,
                    $organizationIds,
                    $subjectId
                ) {
                    $query
                        ->whereIn(
                            'organization_id',
                            $organizationIds
                        )
                        ->whereBetween(
                            'date',
                            [
                                $monthStart->toDateString(),
                                $monthEnd->toDateString(),
                            ]
                        );

                    if (
                        $subjectId !== null &&
                        $subjectId !== 'all'
                    ) {
                        $query->whereHas(
                            'teachingAssignment',
                            function ($q) use ($subjectId) {
                                $q->where(
                                    'subject_id',
                                    $subjectId
                                );
                            }
                        );
                    }
                }
            )
            ->whereIn(
                'student_academic_year_id',
                $studentAcademicYears->pluck('id')
            )
            ->get()
            ->groupBy(
                'student_academic_year_id'
            );

        $dailyStatuses = $attendanceDetails->map(
            fn($details) => $this->buildDailyStatuses($details)
        );

        /*
    |--------------------------------------------------------------------------
    | Informasi Filter
    |--------------------------------------------------------------------------
    */
        $academicYear = null;

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $academicYear =
                AcademicYear::find(
                    $academicYearId
                );
        }

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

        $schoolClass = null;

        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {
            $schoolClass =
                \App\Models\SchoolClass::whereIn(
                    'organization_id',
                    $organizationIds
                )->find(
                    $schoolClassId
                );
        }

        $subject = null;

        if (
            $subjectId !== null &&
            $subjectId !== 'all'
        ) {
            $subject = Subject::find($subjectId);
        }

        /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */
        return Excel::download(
            new AttendanceStudentReportExport(
                $studentAcademicYears,
                $attendanceDetails,
                $dailyStatuses,
                $monthStart,
                $monthEnd,
                $academicYear,
                $organization,
                $schoolClass,
                $subject
            ),
            'laporan-absensi-siswa-' .
                $monthStart->format('Y-m') .
                '.xlsx'
        );
    }

    public function classes(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        $query = \App\Models\SchoolClass::query()
            ->where('is_active', true)
            ->whereIn(
                'organization_id',
                $organizationIds
            );

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('organization_id')) {
            $query->where(
                'organization_id',
                $request->organization_id
            );
        }

        return response()->json(
            $query
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                ])
        );
    }

    public function subjects(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        $query = TeachingAssignment::query()
            ->where('is_active', true)
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->with('subject');

        if ($request->filled('academic_year_id')) {
            $query->whereHas(
                'schoolClass',
                function ($q) use ($request) {
                    $q->where(
                        'academic_year_id',
                        $request->academic_year_id
                    );
                }
            );
        }

        if ($request->filled('organization_id')) {
            $query->where(
                'organization_id',
                $request->organization_id
            );
        }

        if ($request->filled('school_class_id')) {
            $query->where(
                'school_class_id',
                $request->school_class_id
            );
        }

        $subjects = $query
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        return response()->json(
            $subjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ];
            })
        );
    }

    /**
     * ==========================================================
     * EXPORT PDF ABSENSI SISWA
     * ==========================================================
     */
    public function studentPdf(Request $request)
    {
        $user = $request->user();

        $organizationIds =
            Organization::accessibleIdsForUser($user);

        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

        $month = (int) $request->input(
            'month',
            now()->month
        );

        $year = (int) $request->input(
            'year',
            now()->year
        );

        $academicYearId =
            $request->input('academic_year_id');

        $organizationId =
            $request->input('organization_id');

        $schoolClassId =
            $request->input('school_class_id');

        $subjectId =
            $request->input('subject_id');


        /*
    |--------------------------------------------------------------------------
    | Siswa
    |--------------------------------------------------------------------------
    */

        $studentQuery =
            StudentAcademicYear::query()
            ->with([
                'student',
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
            );


        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {
            $studentQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }


        if (
            $organizationId !== null &&
            $organizationId !== 'all'
        ) {
            $studentQuery->where(
                'organization_id',
                $organizationId
            );
        }


        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {
            $studentQuery->where(
                'school_class_id',
                $schoolClassId
            );
        }


        $studentAcademicYears =
            $studentQuery
            ->orderBy('organization_id')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Periode Bulanan
    |--------------------------------------------------------------------------
    */

        $monthStart =
            Carbon::create(
                $year,
                $month,
                1
            )->startOfMonth();

        $monthEnd =
            $monthStart
            ->copy()
            ->endOfMonth();


        /*
    |--------------------------------------------------------------------------
    | Detail Absensi
    |--------------------------------------------------------------------------
    */

        $attendanceDetails =
            \App\Models\AttendanceDetail::query()
            ->with([
                'attendance',
            ])
            ->whereHas(
                'attendance',
                function ($query) use (
                    $monthStart,
                    $monthEnd,
                    $organizationIds,
                    $subjectId
                ) {

                    $query
                        ->whereIn(
                            'organization_id',
                            $organizationIds
                        )
                        ->whereBetween(
                            'date',
                            [
                                $monthStart->toDateString(),
                                $monthEnd->toDateString(),
                            ]
                        );


                    /*
                 * Filter Mata Pelajaran
                 */
                    if (
                        $subjectId !== null &&
                        $subjectId !== 'all'
                    ) {

                        $query->whereHas(
                            'teachingAssignment',
                            function ($q) use (
                                $subjectId
                            ) {

                                $q->where(
                                    'subject_id',
                                    $subjectId
                                );
                            }
                        );
                    }
                }
            )
            ->whereIn(
                'student_academic_year_id',
                $studentAcademicYears->pluck('id')
            )
            ->get()
            ->groupBy(
                'student_academic_year_id'
            );


        /*
    |--------------------------------------------------------------------------
    | Status Harian
    |--------------------------------------------------------------------------
    */

        $dailyStatuses =
            $attendanceDetails->map(
                fn($details) =>
                $this->buildDailyStatuses($details)
            );


        /*
    |--------------------------------------------------------------------------
    | Informasi Filter
    |--------------------------------------------------------------------------
    */

        $academicYear = null;

        if (
            $academicYearId !== null &&
            $academicYearId !== 'all'
        ) {

            $academicYear =
                AcademicYear::find(
                    $academicYearId
                );
        }


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


        $schoolClass = null;

        if (
            $schoolClassId !== null &&
            $schoolClassId !== 'all'
        ) {

            $schoolClass =
                \App\Models\SchoolClass::whereIn(
                    'organization_id',
                    $organizationIds
                )->find(
                    $schoolClassId
                );
        }


        $subject = null;

        if (
            $subjectId !== null &&
            $subjectId !== 'all'
        ) {

            $subject =
                Subject::find(
                    $subjectId
                );
        }


        /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

        $pdf = Pdf::loadView(
            'admin.reports.attendance.exports.student-pdf',
            [
                'students' =>
                $studentAcademicYears,

                'detailsByStudent' =>
                $attendanceDetails,

                'dailyStatuses' =>
                $dailyStatuses,

                'monthStart' =>
                $monthStart,

                'monthEnd' =>
                $monthEnd,

                'academicYear' =>
                $academicYear,

                'organization' =>
                $organization,

                'schoolClass' =>
                $schoolClass,

                'subject' =>
                $subject,
            ]
        );


        $pdf->setPaper(
            'a4',
            'landscape'
        );


        return $pdf->download(
            'laporan-absensi-siswa-' .
                $monthStart->format('Y-m') .
                '.pdf'
        );
    }
}
