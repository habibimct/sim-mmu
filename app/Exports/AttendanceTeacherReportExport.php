<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceTeacherReportExport implements FromView
{
    public function __construct(
        protected $weeklyAttendances,
        protected $weekStart,
        protected $weekEnd,
        protected $teacherAttendanceHours,
        protected int $teacherAttendanceInterval,
        protected $academicYear,
        protected $organization,
        protected $schoolClass,
        protected $subject,
    ) {
    }

    public function view(): View
    {
        return view(
            'admin.reports.attendance.exports.teacher-excel',
            [
                'weeklyAttendances' => $this->weeklyAttendances,
                'weekStart' => $this->weekStart,
                'weekEnd' => $this->weekEnd,
                'teacherAttendanceHours' => $this->teacherAttendanceHours,
                'teacherAttendanceInterval' => $this->teacherAttendanceInterval,
                'academicYear' => $this->academicYear,
                'organization' => $this->organization,
                'schoolClass' => $this->schoolClass,
                'subject' => $this->subject,
            ]
        );
    }
}
