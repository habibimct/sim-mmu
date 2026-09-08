<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi Kepala Unit
        |--------------------------------------------------------------------------
        */

        $organizations = $user
            ->organizations()
            ->where('is_active', true)
            ->whereNotNull('parent_id')
            ->get();

        $organizationIds = $organizations
            ->pluck('id')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Tahun Akademik
        |--------------------------------------------------------------------------
        |
        | Default = tahun akademik terbaru.
        |
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        $academicYearId = $request->input(
            'academic_year_id',
            $academicYears->first()?->id
        );


        /*
        |--------------------------------------------------------------------------
        | Tanggal
        |--------------------------------------------------------------------------
        |
        | Default = hari ini.
        |
        */

        $date = $request->input(
            'date',
            now()->toDateString()
        );


        /*
        |--------------------------------------------------------------------------
        | Daftar Kelas
        |--------------------------------------------------------------------------
        |
        | Hanya:
        | - milik unit Kepala Unit
        | - tahun akademik terpilih
        | - aktif
        | - bukan alumni
        |
        */

        $classes = SchoolClass::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'academic_year_id',
                $academicYearId
            )
            ->where(
                'is_active',
                true
            )
            ->where(
                'is_alumni',
                false
            )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Query Absensi Siswa
        |--------------------------------------------------------------------------
        |
        | Satu Attendance = satu pertemuan.
        | AttendanceDetail = daftar siswa beserta statusnya.
        |
        */

        $query = Attendance::query()
            ->with([
                'teachingAssignment.teacher',
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',

                'details.studentAcademicYear.student',
            ])
            ->whereDate(
                'date',
                $date
            )
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->whereHas(
                'teachingAssignment.schoolClass',
                function ($q) use ($academicYearId) {

                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    )
                    ->where(
                        'is_alumni',
                        false
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Filter Kelas
        |--------------------------------------------------------------------------
        */

        if ($request->filled('school_class_id')) {

            $classId = (int) $request->school_class_id;

            /*
             * Pastikan kelas memang berada
             * dalam cakupan Kepala Unit.
             */

            if (
                $classes->pluck('id')->contains($classId)
            ) {

                $query->whereHas(
                    'teachingAssignment',
                    function ($q) use ($classId) {

                        $q->where(
                            'school_class_id',
                            $classId
                        );
                    }
                );

            } else {

                $query->whereRaw('1 = 0');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Status Siswa
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $status = $request->status;

            if (
                in_array(
                    $status,
                    [
                        'present',
                        'sick',
                        'permission',
                        'absent',
                    ],
                    true
                )
            ) {

                $query->whereHas(
                    'details',
                    function ($q) use ($status) {

                        $q->where(
                            'status',
                            $status
                        );
                    }
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Data Absensi
        |--------------------------------------------------------------------------
        */

        $attendances = $query
            ->orderBy('meeting_number')
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $attendanceIds = $attendances
            ->getCollection()
            ->pluck('id');


        $details = collect();

        if ($attendanceIds->isNotEmpty()) {

            $details = \App\Models\AttendanceDetail::query()
                ->whereIn(
                    'attendance_id',
                    $attendanceIds
                )
                ->get();
        }


        $statistics = [
            'present' => $details
                ->where('status', 'present')
                ->count(),

            'sick' => $details
                ->where('status', 'sick')
                ->count(),

            'permission' => $details
                ->where('status', 'permission')
                ->count(),

            'absent' => $details
                ->where('status', 'absent')
                ->count(),
        ];


        return view(
            'kepala-unit.student-attendances.index',
            compact(
                'attendances',
                'academicYears',
                'academicYearId',
                'classes',
                'date',
                'organizations',
                'statistics'
            )
        );
    }
}
