<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TeachersExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithColumnFormatting
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Query data Guru.
     */
    public function query(): Builder
    {
        return $this->query
            ->with('organizations')
            ->orderBy('name');
    }

    /**
     * Header Excel.
     */
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
            'Unit',
            'Status',
        ];
    }

    /**
     * Mapping data Guru ke Excel.
     */
    public function map($teacher): array
    {
        return [
            $teacher->nik,

            $teacher->name,

            $teacher->gender === 'male'
                ? 'Laki-laki'
                : 'Perempuan',

            $teacher->birth_place ?? '',

            $teacher->birth_date
                ? $teacher->birth_date->format('d/m/Y')
                : '',

            $teacher->phone ?? '',

            $teacher->email ?? '',

            $teacher->organizations
                ->pluck('name')
                ->join(', '),

            $teacher->is_active
                ? 'Aktif'
                : 'Nonaktif',
        ];
    }

    /**
     * Format kolom Excel.
     */
    public function columnFormats(): array
    {
        return [
            // NIK
            'A' => NumberFormat::FORMAT_TEXT,

            // Tanggal lahir
            'E' => 'dd/mm/yyyy',

            // No. HP
            'F' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
