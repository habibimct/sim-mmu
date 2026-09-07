<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    /**
     * Data User yang akan diexport.
     */
    public function collection(): Collection
    {
        return User::with([
            'roles',
            'organizations',
        ])
            ->orderBy('name')
            ->get();
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
     * Mapping data User ke Excel.
     */
    public function map($user): array
    {
        return [
            $user->name,

            $user->email,

            $user->initial_password,

            $user->roles
                ->pluck('name')
                ->implode(', '),

            $user->organizations
                ->pluck('code')
                ->implode(', '),

            $user->is_active
                ? 'Aktif'
                : 'Tidak Aktif',
        ];
    }
}
