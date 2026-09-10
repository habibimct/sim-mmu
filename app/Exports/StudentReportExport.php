<?php

namespace App\Exports;

use App\Models\Organization;
use App\Models\StudentAcademicYear;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StudentReportExport implements FromView
{
    public function __construct(
        protected $academicYearId = null,
        protected $organizationId = null,
        protected $schoolClassId = null,
        protected $status = null,
    ) {}

    public function view(): View
    {
        $organizationIds = Organization::accessibleIdsForUser();

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
        | Tahun Ajaran
        |--------------------------------------------------------------------------
        */

        if ($this->academicYearId) {
            $query->where(
                'academic_year_id',
                $this->academicYearId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Organisasi
        |--------------------------------------------------------------------------
        */

        if (
            $this->organizationId
            && $organizationIds->contains($this->organizationId)
        ) {
            $query->where(
                'organization_id',
                $this->organizationId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Kelas
        |--------------------------------------------------------------------------
        */

        if ($this->schoolClassId) {
            $query->where(
                'school_class_id',
                $this->schoolClassId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status Siswa
        |--------------------------------------------------------------------------
        */

        if (in_array($this->status, ['active', 'inactive'], true)) {

            $query->whereHas('student', function ($q) {

                $q->where(
                    'is_active',
                    $this->status === 'active'
                );
            });
        }

        $studentAcademicYears = $query
            ->orderBy('organization_id')
            ->orderBy('school_class_id')
            ->orderBy('student_id')
            ->get();

        return view(
            'admin.reports.students.exports.excel',
            compact('studentAcademicYears')
        );
    }
}
