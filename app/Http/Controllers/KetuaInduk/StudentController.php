<?php

namespace App\Http\Controllers\KetuaInduk;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Organization;
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
        | Organisasi induk dalam cakupan Ketua Induk
        |--------------------------------------------------------------------------
        */

        $parentOrganizations = $user
            ->organizations()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Unit di bawah organisasi induk
        |--------------------------------------------------------------------------
        |
        | Organisasi induk sendiri tidak dimasukkan.
        |
        */

        $organizationIds = collect();

        foreach ($parentOrganizations as $organization) {

            $organizationIds = $organizationIds->merge(
                $organization->descendantIds()
            );
        }

        $organizationIds = $organizationIds
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Daftar unit untuk filter
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Tahun akademik
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Query siswa
        |--------------------------------------------------------------------------
        |
        | Data unit dan kelas berasal dari StudentAcademicYear.
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
        | Filter pencarian
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
        | Filter tahun akademik
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
        | Filter unit
        |--------------------------------------------------------------------------
        */

        if ($request->filled('organization_id')) {

            /*
             * Pastikan unit yang diminta memang berada
             * dalam cakupan Ketua Induk.
             */

            if (
                $organizationIds->contains(
                    (int) $request->organization_id
                )
            ) {

                $query->whereHas(
                    'studentAcademicYears',
                    function ($q) use ($request) {

                        $q->where(
                            'organization_id',
                            $request->organization_id
                        );
                    }
                );

            } else {

                /*
                 * Jika ID organisasi dimanipulasi dari URL,
                 * jangan tampilkan data apa pun.
                 */

                $query->whereRaw('1 = 0');
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Filter status siswa
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
        | Data siswa
        |--------------------------------------------------------------------------
        */

        $students = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Tentukan record akademik yang ditampilkan
        |--------------------------------------------------------------------------
        |
        | Jika tahun akademik dipilih, gunakan record tahun tersebut.
        | Jika tidak dipilih, gunakan record terbaru dalam cakupan unit.
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

            if ($request->filled('organization_id')) {

                $records = $records->where(
                    'organization_id',
                    $request->organization_id
                );
            }

            $student->displayAcademicRecord = $records
                ->sortByDesc('id')
                ->first();
        }


        return view(
            'ketua-induk.students.index',
            compact(
                'students',
                'organizations',
                'academicYears'
            )
        );
    }
}
