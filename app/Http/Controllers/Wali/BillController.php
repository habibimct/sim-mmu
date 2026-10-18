<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillController extends Controller
{
    public function index(Request $request, int $studentId): View
    {
        $guardian = $request->user()
            ->guardian()
            ->firstOrFail();

        // Pastikan anak terhubung dengan wali yang login.
        $student = $guardian->students()
            ->where('students.id', $studentId)
            ->firstOrFail();

        $bills = StudentBill::query()
            ->with([
                'billType',
                'paymentAllocations.payment',
                'studentAcademicYear.academicYear',
            ])
            ->whereHas('studentAcademicYear', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'cancelled');
            })
            ->get()
            ->sortByDesc('due_date')
            ->values();

        $totalBill = $bills->sum(
            fn($bill) => (float) $bill->amount
        );

        $totalPaid = $bills->sum(
            fn($bill) => $bill->paid_amount
        );

        $totalRemaining = $bills->sum(
            fn($bill) => $bill->remaining_amount
        );

        return view('wali.bills.index', compact(
            'guardian',
            'student',
            'bills',
            'totalBill',
            'totalPaid',
            'totalRemaining'
        ));
    }
}
