<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class UsersImportTemplateExport implements
    FromArray,
    WithHeadings,
    WithColumnFormatting,
    ShouldAutoSize
{
    /**
     * Contoh data.
     */
    public function array(): array
    {
        return [
            [
                'Administrator PMUB',
                'admin@pmub.id',
                'PMUB@123456',
                'Admin Sistem',
                'PMUB',
                'Aktif',
            ],
        ];
    }


    /**
     * Heading Excel.
     */
    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Password',
            'Role',
            'Organisasi',
            'Status',
        ];
    }


    /**
     * Format kolom.
     */
    public function columnFormats(): array
    {
        return [

            // Email sebagai text
            'B' => NumberFormat::FORMAT_TEXT,

            // Password sebagai text
            'C' => NumberFormat::FORMAT_TEXT,

            // Organisasi sebagai text
            'E' => NumberFormat::FORMAT_TEXT,

        ];
    }
}
