<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exports\TeacherReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class TeacherReportController extends Controller
{
    public function index(Request $request): View
    {
        $organizationIds = Organization::accessibleIdsForUser();

        $organizationId = $request->integer('organization_id');
        $status = $request->input('status');
        $gender = $request->input('gender');

        $query = Teacher::query()
            ->with([
                'organizations',
            ])
            ->whereHas('organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });

        /*
        |--------------------------------------------------------------------------
        | Filter Unit
        |--------------------------------------------------------------------------
        */

        if (
            $organizationId
            && $organizationIds->contains($organizationId)
        ) {
            $query->whereHas('organizations', function ($q) use ($organizationId) {
                $q->where('organizations.id', $organizationId);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where(
                'is_active',
                $status === 'active'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jenis Kelamin
        |--------------------------------------------------------------------------
        */

        if (in_array($gender, ['male', 'female'], true)) {
            $query->where(
                'gender',
                $gender
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Data Guru
        |--------------------------------------------------------------------------
        */

        $teachers = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Rekap
        |--------------------------------------------------------------------------
        */

        $summaryTeachers = (clone $query)->get();

        $totalTeachers = $summaryTeachers->count();

        $totalMale = $summaryTeachers
            ->where('gender', 'male')
            ->count();

        $totalFemale = $summaryTeachers
            ->where('gender', 'female')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Rekap per Unit
        |--------------------------------------------------------------------------
        */

        $organizationSummary = collect();

        foreach ($summaryTeachers as $teacher) {

            foreach ($teacher->organizations as $organization) {

                if (! $organizationIds->contains($organization->id)) {
                    continue;
                }

                if (
                    $organizationId
                    && $organization->id !== $organizationId
                ) {
                    continue;
                }

                if (! $organizationSummary->has($organization->id)) {
                    $organizationSummary->put(
                        $organization->id,
                        [
                            'organization' => $organization,
                            'total' => 0,
                            'male' => 0,
                            'female' => 0,
                        ]
                    );
                }

                $summary = $organizationSummary->get(
                    $organization->id
                );

                $summary['total']++;

                if ($teacher->gender === 'male') {
                    $summary['male']++;
                }

                if ($teacher->gender === 'female') {
                    $summary['female']++;
                }

                $organizationSummary->put(
                    $organization->id,
                    $summary
                );
            }
        }

        $organizationSummary = $organizationSummary
            ->sortBy(
                fn($summary) =>
                $summary['organization']->name ?? ''
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Filter Unit
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

        return view(
            'admin.reports.teachers.index',
            compact(
                'teachers',
                'organizations',
                'organizationSummary',
                'totalTeachers',
                'totalMale',
                'totalFemale',
                'organizationId',
                'status',
                'gender'
            )
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

        if (
            $organizationId
            && ! $organizationIds->contains($organizationId)
        ) {
            abort(403);
        }

        return Excel::download(
            new TeacherReportExport(
                organizationId: $organizationId,
                status: $request->input('status'),
                gender: $request->input('gender'),
            ),
            'laporan-guru.xlsx'
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

        if (
            $organizationId
            && ! $organizationIds->contains($organizationId)
        ) {
            abort(403);
        }

        $query = Teacher::query()
            ->with([
                'organizations',
            ])
            ->whereHas('organizations', function ($q) use ($organizationIds) {
                $q->whereIn('organizations.id', $organizationIds);
            });

        /*
    |--------------------------------------------------------------------------
    | Filter Unit
    |--------------------------------------------------------------------------
    */

        if ($organizationId) {
            $query->whereHas('organizations', function ($q) use ($organizationId) {
                $q->where(
                    'organizations.id',
                    $organizationId
                );
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Status
    |--------------------------------------------------------------------------
    */

        $status = $request->input('status');

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where(
                'is_active',
                $status === 'active'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Jenis Kelamin
    |--------------------------------------------------------------------------
    */

        $gender = $request->input('gender');

        if (in_array($gender, ['male', 'female'], true)) {
            $query->where(
                'gender',
                $gender
            );
        }

        $teachers = $query
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView(
            'admin.reports.teachers.exports.pdf',
            compact('teachers')
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'laporan-guru.pdf'
        );
    }
}
