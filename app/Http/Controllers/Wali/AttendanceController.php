<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request, int $studentId): View
    {
        $guardian = $request->user()
            ->guardian()
            ->firstOrFail();

        // Pastikan siswa benar-benar terhubung dengan wali ini.
        $student = $guardian->students()
            ->where('students.id', $studentId)
            ->with([
                'studentAcademicYears.schoolClass',
            ])
            ->firstOrFail();

        $attendanceDetails = AttendanceDetail::query()
            ->with([
                'attendance.teachingAssignment.subject',
            ])
            ->whereHas('studentAcademicYear', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->whereHas('attendance')
            ->whereHas('studentAcademicYear.student', function ($query) use ($student) {
                $query->where('id', $student->id);
            })
            ->get()
            ->sortByDesc(fn($detail) => $detail->attendance?->date)
            ->values();

        $attendanceSummary = [
            'present' => $attendanceDetails->where('status', 'present')->count(),
            'sick' => $attendanceDetails->where('status', 'sick')->count(),
            'permission' => $attendanceDetails->where('status', 'permission')->count(),
            'absent' => $attendanceDetails->where('status', 'absent')->count(),
        ];

        return view('wali.attendance.index', compact(
            'guardian',
            'student',
            'attendanceDetails',
            'attendanceSummary'
        ));
    }
}
