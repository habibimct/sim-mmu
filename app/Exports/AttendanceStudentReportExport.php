<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceStudentReportExport implements FromView
{
    public function __construct(
        public $students,
        public $detailsByStudent,
        public $dailyStatuses,
        public $monthStart,
        public $monthEnd,
        public $academicYear,
        public $organization,
        public $schoolClass,
        public $subject,
    ) {}

    public function view(): View
    {
        return view('admin.reports.attendance.exports.student-excel', [
            'students' => $this->students,
            'detailsByStudent' => $this->detailsByStudent,
            'dailyStatuses' => $this->dailyStatuses,
            'monthStart' => $this->monthStart,
            'monthEnd' => $this->monthEnd,
            'academicYear' => $this->academicYear,
            'organization' => $this->organization,
            'schoolClass' => $this->schoolClass,
            'subject' => $this->subject,
        ]);
    }
}
