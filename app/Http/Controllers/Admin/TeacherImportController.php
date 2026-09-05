<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TeachersImportTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\TeachersImport;
use App\Models\Organization;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeacherImportController extends Controller
{
    /**
     * Memproses file import Guru.
     */
    public function store(Request $request)
    {
        $organizationIds = Teacher::organizationIdsForUser();

        $validated = $request->validate([
            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id',

                function ($attribute, $value, $fail) use ($organizationIds) {
                    if (! $organizationIds->contains((int) $value)) {
                        $fail(
                            'Anda tidak memiliki akses ke unit tersebut.'
                        );
                    }
                },
            ],

            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240',
            ],
        ]);

        Excel::import(
            new TeachersImport(
                (int) $validated['organization_id']
            ),
            $request->file('file')
        );

        return redirect()
            ->route('admin.teachers.index')
            ->with(
                'success',
                'Data Guru berhasil diimport.'
            );
    }

    /**
     * Download template import Guru.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new TeachersImportTemplateExport(),
            'template-import-guru.xlsx'
        );
    }
}
