<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\SchoolClass;
use App\Models\StudentAcademicYear;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exports\StudentReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class StudentReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Validasi akses
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $user->is_active
                && $user->can('reports.view'),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Organisasi yang dapat diakses user
    |--------------------------------------------------------------------------
    */

        $organizationIds = Organization::accessibleIdsForUser();

        /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

        $academicYearId = $request->integer('academic_year_id');

        $organizationId = $request->integer('organization_id');

        $schoolClassId = $request->integer('school_class_id');

        $status = $request->input('status');

        /*
    |--------------------------------------------------------------------------
    | Query dasar laporan
    |--------------------------------------------------------------------------
    |
    | StudentAcademicYear digunakan sebagai sumber penempatan siswa
    | berdasarkan tahun ajaran, unit, dan kelas.
    |
    */

        $reportQuery = StudentAcademicYear::query()
            ->with([
                'student',
                'organization',
                'academicYear',
                'schoolClass',
            ])
            ->whereIn('organization_id', $organizationIds)
            ->where('status', 'active')
            ->whereHas('student');

        /*
    |--------------------------------------------------------------------------
    | Filter Tahun Ajaran
    |--------------------------------------------------------------------------
    */

        if ($academicYearId) {
            $reportQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Organisasi
    |--------------------------------------------------------------------------
    */

        if (
            $organizationId
            && $organizationIds->contains($organizationId)
        ) {
            $reportQuery->where(
                'organization_id',
                $organizationId
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Kelas
    |--------------------------------------------------------------------------
    */

        if ($schoolClassId) {
            $reportQuery->where(
                'school_class_id',
                $schoolClassId
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Status Siswa
    |--------------------------------------------------------------------------
    */

        if (in_array($status, ['active', 'inactive'], true)) {

            $reportQuery->whereHas('student', function ($q) use ($status) {

                $q->where(
                    'is_active',
                    $status === 'active'
                );
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Data untuk summary
    |--------------------------------------------------------------------------
    |
    | Summary menghitung seluruh data hasil filter,
    | bukan hanya data pada halaman aktif.
    |
    */

        $summaryStudents = (clone $reportQuery)
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Ringkasan total siswa
    |--------------------------------------------------------------------------
    */

        $totalStudents = $summaryStudents->count();

        $totalMale = $summaryStudents
            ->filter(
                fn($item) =>
                $item->student?->gender === 'L'
            )
            ->count();

        $totalFemale = $summaryStudents
            ->filter(
                fn($item) =>
                $item->student?->gender === 'P'
            )
            ->count();

        /*
    |--------------------------------------------------------------------------
    | Rekap per organisasi
    |--------------------------------------------------------------------------
    */

        $organizationSummary = $summaryStudents
            ->groupBy('organization_id')
            ->map(function ($items) {

                $organization = $items
                    ->first()
                    ->organization;

                return [
                    'organization' => $organization,

                    'total' => $items->count(),

                    'male' => $items
                        ->filter(
                            fn($item) =>
                            $item->student?->gender === 'L'
                        )
                        ->count(),

                    'female' => $items
                        ->filter(
                            fn($item) =>
                            $item->student?->gender === 'P'
                        )
                        ->count(),
                ];
            })
            ->sortBy(function ($summary) {
                return $summary['organization']->name ?? '';
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Data filter organisasi
    |--------------------------------------------------------------------------
    */

        $organizations = Organization::query()
            ->whereIn('id', $organizationIds)
            ->where('is_active', true)
            ->orderByRaw(
                "CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END"
            )
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Data filter tahun ajaran
    |--------------------------------------------------------------------------
    */

        $academicYears = AcademicYear::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Data filter kelas
    |--------------------------------------------------------------------------
    */

        $schoolClasses = SchoolClass::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where('is_active', true)
            ->with([
                'academicYear',
                'organization',
            ])
            ->orderBy('academic_year_id')
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Data detail dengan pagination
    |--------------------------------------------------------------------------
    */

        $studentAcademicYears = (clone $reportQuery)
            ->orderBy('organization_id')
            ->orderBy('school_class_id')
            ->orderBy('student_id')
            ->paginate(15)
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.reports.students.index',
            compact(
                'studentAcademicYears',
                'organizations',
                'academicYears',
                'schoolClasses',
                'organizationSummary',
                'totalStudents',
                'totalMale',
                'totalFemale',
                'academicYearId',
                'organizationId',
                'schoolClassId',
                'status'
            )
        );
    }

    public function classes(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user->is_active
                && $user->can('reports.view'),
            403
        );

        $organizationIds = Organization::accessibleIdsForUser();

        $query = SchoolClass::query()
            ->whereIn('organization_id', $organizationIds)
            ->where('is_active', true)
            ->with('organization')
            ->orderBy('level')
            ->orderBy('name');

        /*
    |--------------------------------------------------------------------------
    | Filter Tahun Ajaran
    |--------------------------------------------------------------------------
    */

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->integer('academic_year_id')
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Unit
    |--------------------------------------------------------------------------
    */

        if ($request->filled('organization_id')) {

            $organizationId = $request->integer('organization_id');

            abort_unless(
                $organizationIds->contains($organizationId),
                403
            );

            $query->where(
                'organization_id',
                $organizationId
            );
        }

        return response()->json(
            $query->get()->map(function ($schoolClass) {
                return [
                    'id' => $schoolClass->id,
                    'name' => $schoolClass->name,
                    'level' => $schoolClass->level,
                    'organization_id' => $schoolClass->organization_id,
                    'academic_year_id' => $schoolClass->academic_year_id,
                    'organization_name' => $schoolClass->organization?->name,
                ];
            })
        );
    }

    public function excel(Request $request): BinaryFileResponse
    {
        $user = $request->user();

        abort_unless(
            $user->is_active
                && $user->can('reports.view'),
            403
        );

        $organizationIds = Organization::accessibleIdsForUser();

        $organizationId = $request->integer('organization_id');

        /*
    |--------------------------------------------------------------------------
    | Pastikan organisasi yang diminta memang boleh diakses
    |--------------------------------------------------------------------------
    */

        if (
            $organizationId
            && ! $organizationIds->contains($organizationId)
        ) {
            abort(403);
        }

        return Excel::download(
            new StudentReportExport(
                academicYearId: $request->integer('academic_year_id'),
                organizationId: $organizationId,
                schoolClassId: $request->integer('school_class_id'),
                status: $request->input('status'),
            ),
            'laporan-siswa.xlsx'
        );
    }

       public function pdf(Request $request): Response
    {
        $user = $request->user();

        abort_unless(
            $user->is_active
                && $user->can('reports.view'),
            403
        );

        $organizationIds = Organization::accessibleIdsForUser();

        $organizationId = $request->integer('organization_id');

        /*
    |--------------------------------------------------------------------------
    | Pastikan organisasi yang diminta memang boleh diakses
    |--------------------------------------------------------------------------
    */

        if (
            $organizationId
            && ! $organizationIds->contains($organizationId)
        ) {
            abort(403);
        }

        $query = StudentAcademicYear::query()
            ->with([
                'student',
                'organization',
                'academicYear',
                'schoolClass',
            ])
            ->whereIn('organization_id', $organizationIds)
            ->where('status', 'active')
            ->whereHas('student');

        /*
    |--------------------------------------------------------------------------
    | Filter Tahun Ajaran
    |--------------------------------------------------------------------------
    */

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->integer('academic_year_id')
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Organisasi
    |--------------------------------------------------------------------------
    */

        if ($organizationId) {
            $query->where(
                'organization_id',
                $organizationId
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Kelas
    |--------------------------------------------------------------------------
    */

        if ($request->filled('school_class_id')) {
            $query->where(
                'school_class_id',
                $request->integer('school_class_id')
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Status
    |--------------------------------------------------------------------------
    */

        $status = $request->input('status');

        if (in_array($status, ['active', 'inactive'], true)) {

            $query->whereHas('student', function ($q) use ($status) {

                $q->where(
                    'is_active',
                    $status === 'active'
                );
            });
        }

        $studentAcademicYears = $query
            ->orderBy('organization_id')
            ->orderBy('school_class_id')
            ->orderBy('student_id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

        $pdf = Pdf::loadView(
            'admin.reports.students.exports.pdf',
            compact('studentAcademicYears')
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'laporan-siswa.pdf'
        );
    }
}
