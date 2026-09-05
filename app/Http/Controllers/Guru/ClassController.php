<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TeachingAssignment;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $teacher = auth()->user()->teacher;

        abort_unless($teacher, 403, 'Akun Guru belum terhubung dengan data Guru.');

        $assignments = TeachingAssignment::query()
            ->with([
                'schoolClass.organization',
                'schoolClass.academicYear',
                'subject',
            ])
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->get();

        return view('guru.classes.index', compact('assignments'));
    }
}
