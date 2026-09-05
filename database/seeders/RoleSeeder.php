<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'code' => 'admin_sistem',
                'name' => 'Admin Sistem',
            ],
            [
                'code' => 'keuangan_induk',
                'name' => 'Keuangan INDUK',
            ],
            [
                'code' => 'bendahara_unit',
                'name' => 'Bendahara Unit',
            ],
            [
                'code' => 'guru',
                'name' => 'Guru',
            ],
            [
                'code' => 'wali_kelas',
                'name' => 'Wali Kelas',
            ],
            [
                'code' => 'wali',
                'name' => 'Wali/Orang Tua',
            ],
            [
                'code' => 'admin_unit',
                'name' => 'Admin Unit',
            ],
            [
                'code' => 'pengurus_induk',
                'name' => 'Pengurus INDUK',
            ],
            [
                'code' => 'ketua_induk',
                'name' => 'Ketua INDUK',
            ],
            [
                'code' => 'kepala_unit',
                'name' => 'Kepala Unit',
            ],

        ];

        foreach ($roles as $role) {
            Role::create([
                'code' => $role['code'],
                'name' => $role['name'],
                'is_active' => true,
            ]);
        }
    }
}
