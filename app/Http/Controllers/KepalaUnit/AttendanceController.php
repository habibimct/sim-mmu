<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
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
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Tahun Akademik Terpilih
        |--------------------------------------------------------------------------
        |
        | Default = tahun akademik terbaru.
        |
        */

        $academicYearId = $request->input(
            'academic_year_id',
            $academicYears->first()?->id
        );


        /*
        |--------------------------------------------------------------------------
        | Tanggal
        |--------------------------------------------------------------------------
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
        | Hanya kelas:
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
        | Query Absensi Guru
        |--------------------------------------------------------------------------
        */

        $query = Attendance::query()
            ->with([
                'teachingAssignment.teacher',
                'teachingAssignment.schoolClass',
                'teachingAssignment.subject',
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
             * Pastikan kelas memang berada dalam daftar
             * kelas yang boleh dilihat Kepala Unit.
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

                /*
                 * Kelas di luar cakupan atau kelas alumni.
                 */

                $query->whereRaw('1 = 0');
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
            ->paginate(20)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Ringkasan
        |--------------------------------------------------------------------------
        */

        $total = Attendance::query()
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
            )
            ->count();


        return view(
            'kepala-unit.attendances.index',
            compact(
                'attendances',
                'classes',
                'academicYears',
                'academicYearId',
                'date',
                'organizations',
                'total'
            )
        );
    }
}
