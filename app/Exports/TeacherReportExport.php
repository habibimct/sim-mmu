<?php

namespace App\Exports;

use App\Models\Organization;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TeacherReportExport implements FromView
{
    public function __construct(
        protected $organizationId = null,
        protected $status = null,
        protected $gender = null,
    ) {
    }

    public function view(): View
    {
        $organizationIds = Organization::accessibleIdsForUser();

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
            $this->organizationId
            && $organizationIds->contains($this->organizationId)
        ) {
            $query->whereHas('organizations', function ($q) {
                $q->where(
                    'organizations.id',
                    $this->organizationId
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Status
        |--------------------------------------------------------------------------
        */

        if (in_array($this->status, ['active', 'inactive'], true)) {
            $query->where(
                'is_active',
                $this->status === 'active'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Jenis Kelamin
        |--------------------------------------------------------------------------
        */

        if (in_array($this->gender, ['male', 'female'], true)) {
            $query->where(
                'gender',
                $this->gender
            );
        }

        $teachers = $query
            ->orderBy('name')
            ->get();

        return view(
            'admin.reports.teachers.exports.excel',
            compact('teachers')
        );
    }
}
