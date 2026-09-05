<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection(): Collection
    {
        return $this->query
            ->with([
                'organization',
                'studentAcademicYears.academicYear',
                'studentAcademicYears.schoolClass',
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {

                /*
                |--------------------------------------------------------------------------
                | Ambil penempatan akademik aktif
                |--------------------------------------------------------------------------
                */

                $academicPlacement =
                    $student->studentAcademicYears
                        ->firstWhere(
                            'status',
                            'active'
                        );

                return [
                    'nis' =>
                        $student->nis,

                    'name' =>
                        $student->name,

                    'gender' =>
                        $student->gender,

                    'birth_place' =>
                        $student->birth_place,

                    'birth_date' =>
                        $student->birth_date
                            ? $student->birth_date->format('Y-m-d')
                            : null,

                    'organization' =>
                        $academicPlacement?->organization?->name
                        ?? $student->organization?->name
                        ?? null,

                    'academic_year' =>
                        $academicPlacement?->academicYear?->name
                        ?? null,

                    'school_class' =>
                        $academicPlacement?->schoolClass?->name
                        ?? null,

                    'status' =>
                        $student->is_active
                            ? 'Aktif'
                            : 'Nonaktif',
                ];
            });
    }

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
            'Status',
        ];
    }
}
