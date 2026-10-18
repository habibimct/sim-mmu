<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDetail;
use App\Models\StudentBill;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $guardian = Guardian::query()
            ->with([
                'organization',
                'students',
            ])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $students = $guardian->students;

        $attendanceSummary = [];

        foreach ($students as $student) {
            $attendanceSummary[$student->id] = [
                'present' => 0,
                'sick' => 0,
                'permission' => 0,
                'absent' => 0,
            ];

            $details = AttendanceDetail::query()
                ->whereHas('studentAcademicYear', function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                })
                ->whereHas('attendance', function ($query) {
                    $query->whereDate('date', today());
                })
                ->get(['status']);

            foreach ($details as $detail) {
                if (isset($attendanceSummary[$student->id][$detail->status])) {
                    $attendanceSummary[$student->id][$detail->status]++;
                }
            }
        }

        $billSummary = [];

        foreach ($students as $student) {
            $bills = StudentBill::query()
                ->whereHas('studentAcademicYear', function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                })
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', '!=', 'cancelled');
                })
                ->with('paymentAllocations.payment')
                ->get();

            $billSummary[$student->id] = $bills->sum(
                fn(StudentBill $bill) => $bill->remaining_amount
            );
        }

        return view('wali.dashboard', compact(
            'guardian',
            'students',
            'attendanceSummary',
            'billSummary'
        ));
    }
}
