<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cari organisasi INDUK
        $induk = Organization::where('code', 'INDUK')->firstOrFail();

        // Cari role Admin Sistem
        $adminRole = Role::where('code', 'admin_sistem')->firstOrFail();

        // Buat user Admin Sistem
        $admin = User::updateOrCreate(
            [
                'email' => 'admin@pmub.test',
            ],
            [
                'name' => 'Administrator PMUB',
                'password' => 'password',
                'is_active' => true,
            ]
        );

        // Hubungkan Admin dengan role
        $admin->roles()->syncWithoutDetaching([
            $adminRole->id,
        ]);

        // Hubungkan Admin dengan organisasi INDUK
        $admin->organizations()->syncWithoutDetaching([
            $induk->id,
        ]);
    }
}
