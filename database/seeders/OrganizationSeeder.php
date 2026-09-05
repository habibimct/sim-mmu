<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat organisasi utama INDUK
        $induk = Organization::create([
            'parent_id' => null,
            'code' => 'INDUK',
            'name' => 'INDUK',
            'type' => 'induk',
            'is_active' => true,
        ]);

        // Membuat unit-unit di bawah INDUK
        $units = [
            [
                'code' => 'PAUD',
                'name' => 'PAUD',
            ],
            [
                'code' => 'TK',
                'name' => 'TK',
            ],
            [
                'code' => 'MI',
                'name' => 'MI',
            ],
            [
                'code' => 'MTS',
                'name' => 'MTs',
            ],
            [
                'code' => 'MA',
                'name' => 'MA',
            ],
        ];

        foreach ($units as $unit) {
            Organization::create([
                'parent_id' => $induk->id,
                'code' => $unit['code'],
                'name' => $unit['name'],
                'type' => 'unit',
                'is_active' => true,
            ]);
        }
    }
}
