<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildrenController extends Controller
{
    public function index(Request $request): View
    {
        $guardian = $request->user()
            ->guardian()
            ->with([
                'organization',
                'students.studentAcademicYears.schoolClass',
            ])
            ->firstOrFail();

        $students = $guardian->students;

        return view('wali.children.index', compact(
            'guardian',
            'students'
        ));
    }
}
