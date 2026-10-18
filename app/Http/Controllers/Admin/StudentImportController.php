<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
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

        // Organisasi contoh mengikuti organisasi pertama yang tersedia
        $exampleOrganization = $organizations->first();

        // Tahun akademik aktif terbaru
        $exampleAcademicYear = AcademicYear::query()
            ->where('is_active', true)
            ->orderByDesc('start_date')
            ->first();

        // Contoh kelas aktif dari organisasi dan tahun akademik tersebut
        $exampleClass = null;

        if ($exampleOrganization && $exampleAcademicYear) {
            $exampleClass = SchoolClass::query()
                ->where('organization_id', $exampleOrganization->id)
                ->where('academic_year_id', $exampleAcademicYear->id)
                ->where('is_active', true)
                ->where('is_alumni', false)
                ->orderBy('level')
                ->orderBy('name')
                ->first();
        }

        return view(
            'admin.students.import',
            compact(
                'organizations',
                'exampleOrganization',
                'exampleAcademicYear',
                'exampleClass'
            )
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
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.students.index')
                ->with('import_error', $e->getMessage());
        }
    }
}
