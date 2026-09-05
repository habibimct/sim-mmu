<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Student;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentImportController extends Controller
{
    public function create(): View
    {
        $organizationIds = Student::organizationIdsForUser();

        $organizations = Organization::whereIn(
            'id',
            $organizationIds
        )
            ->where('is_active', true)
            ->orderByRaw(
                "CASE WHEN type = 'INDUK' THEN 0 ELSE 1 END"
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.students.import',
            compact('organizations')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id',
            ],

            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:5120',
            ],
        ]);

        try {
            Excel::import(
                new StudentsImport(
                    (int) $request->organization_id
                ),
                $request->file('file')
            );

            return back()->with(
                'import_success',
                'Data siswa berhasil diimpor ke database.'
            );
        } catch (ValidationException $e) {

            $failures = $e->failures();

            return back()
                ->with('import_failures', $failures)
                ->with(
                    'error',
                    'File Excel memiliki data yang tidak valid.'
                );
        }
    }
}
