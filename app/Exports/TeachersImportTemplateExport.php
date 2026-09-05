<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TeachersImportTemplateExport implements
    FromArray,
    WithHeadings,
    WithColumnFormatting
{
    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'No. HP',
            'Email',
            'Status',
        ];
    }

    public function array(): array
    {
        return [];
    }

    public function columnFormats(): array
    {
        return [
            // NIK sebagai teks
            'A2:A1000' => NumberFormat::FORMAT_TEXT,

            // Tanggal lahir
            'E2:E1000' => 'dd/mm/yyyy',

            // No. HP sebagai teks
            'F2:F1000' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
