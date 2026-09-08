<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi Kepala Unit
        |--------------------------------------------------------------------------
        |
        | Kepala Unit hanya melihat siswa pada unit
        | yang menjadi organisasinya.
        |
        */

        $organizations = $user
            ->organizations()
            ->where('is_active', true)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ID organisasi yang menjadi cakupan
        |--------------------------------------------------------------------------
        */

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
        | Query Siswa
        |--------------------------------------------------------------------------
        |
        | Unit dan kelas siswa diambil melalui
        | StudentAcademicYear.
        |
        */

        $query = Student::query()
            ->with([
                'studentAcademicYears' => function ($q) use ($organizationIds) {

                    $q->whereIn(
                        'organization_id',
                        $organizationIds
                    )
                    ->with([
                        'academicYear',
                        'organization',
                        'schoolClass',
                    ])
                    ->orderByDesc('id');
                },
            ])
            ->whereHas(
                'studentAcademicYears',
                function ($q) use ($organizationIds) {

                    $q->whereIn(
                        'organization_id',
                        $organizationIds
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'nis',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'name',
                    'like',
                    "%{$search}%"
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Tahun Akademik
        |--------------------------------------------------------------------------
        */

        if ($request->filled('academic_year_id')) {

            $query->whereHas(
                'studentAcademicYears',
                function ($q) use (
                    $request,
                    $organizationIds
                ) {

                    $q->where(
                        'academic_year_id',
                        $request->academic_year_id
                    )
                    ->whereIn(
                        'organization_id',
                        $organizationIds
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'active') {

            $query->where(
                'is_active',
                true
            );

        } elseif ($request->status === 'inactive') {

            $query->where(
                'is_active',
                false
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Data Siswa
        |--------------------------------------------------------------------------
        */

        $students = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Tentukan Record Akademik yang Ditampilkan
        |--------------------------------------------------------------------------
        |
        | Jika tahun akademik dipilih:
        | gunakan record tahun tersebut.
        |
        | Jika tidak dipilih:
        | gunakan record terbaru.
        |
        */

        foreach ($students as $student) {

            $records = $student->studentAcademicYears;

            if ($request->filled('academic_year_id')) {

                $records = $records->where(
                    'academic_year_id',
                    $request->academic_year_id
                );
            }

            $student->displayAcademicRecord = $records
                ->sortByDesc('id')
                ->first();
        }


        return view(
            'kepala-unit.students.index',
            compact(
                'students',
                'organizations',
                'academicYears'
            )
        );
    }
}
