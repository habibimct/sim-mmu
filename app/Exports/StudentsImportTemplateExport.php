<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsImportTemplateExport implements
    FromArray,
    WithHeadings
{
    public function headings(): array
    {
        return [
            'NIS',
            'Nama',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Organisasi',
            'Tahun Akademik',
            'Kelas',
        ];
    }

    public function array(): array
    {
        return [];
    }
}
