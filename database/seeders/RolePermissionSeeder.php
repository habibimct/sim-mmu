<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = Permission::pluck('id', 'code');

        $rolePermissions = [

            // =====================================================
            // ADMIN SISTEM
            // =====================================================
            'admin_sistem' => [
                'dashboard.view',

                // Organisasi
                'organizations.view',
                'organizations.manage',

                // Tahun Ajaran
                'academic_years.view',
                'academic_years.manage',

                // User
                'users.view',
                'users.manage',

                // Siswa
                'students.view',
                'students.manage',

                // Guru
                'teachers.view',
                'teachers.manage',

                // Kelas
                'classes.view',
                'classes.manage',

                // Mata Pelajaran
                // 'subjects.view',
                // 'subjects.manage',

                'attendance.view',
                'bills.view',
                'payments.view',
                'finance.view',
                'reports.view',
                'audit.view',
                'settings.manage',
            ],

            // =====================================================
            // KEUANGAN INDUK
            // =====================================================
            'keuangan_induk' => [
                'dashboard.view',
                'students.view',
                'bills.view',
                'bills.manage',
                'payments.view',
                'payments.create',
                'finance.view',
                'finance.manage',
                'reports.view',
            ],

            // =====================================================
            // BENDAHARA UNIT
            // =====================================================
            'bendahara_unit' => [
                'dashboard.view',
                'students.view',
                'bills.view',
                'bills.manage',
                'payments.view',
                'payments.create',
                'finance.view',
                'finance.manage',
                'reports.view',
            ],

            // =====================================================
            // GURU
            // =====================================================
            'guru' => [
                'dashboard.view',
                'students.view',
                'classes.view',
                'subjects.view',
                'attendance.view',
                'attendance.create',
                'attendance.update',
            ],

            // =====================================================
            // WALI KELAS
            // =====================================================
            'wali_kelas' => [
                'dashboard.view',
                'students.view',
                'classes.view',
                'subjects.view',
                'attendance.view',
                'reports.view',
            ],

            // =====================================================
            // WALI / ORANG TUA
            // =====================================================
            'wali' => [
                'dashboard.view',
                'students.view',
                'bills.view',
                'payments.view',
            ],

            // =====================================================
            // ADMIN UNIT
            // =====================================================
            'admin_unit' => [
                'dashboard.view',

                // Siswa
                'students.view',
                'students.manage',

                // Guru
                'teachers.view',
                'teachers.manage',

                // Kelas
                'classes.view',
                'classes.manage',

                // Mata Pelajaran
                'subjects.view',
                'subjects.manage',

                'teaching_assignments.view',
                'teaching_assignments.manage',

                // Absensi
                'attendance.view',
                'attendance.create',
                'attendance.update',

                // Tagihan
                'bills.view',
                'bills.manage',

                // Pembayaran
                'payments.view',
                'payments.create',

                // Keuangan
                'finance.view',

                // Laporan
                'reports.view',
            ],

            // =====================================================
            // PENGURUS INDUK
            // =====================================================
            'pengurus_induk' => [
                'dashboard.view',
                'students.view',
                'teachers.view',
                'attendance.view',
                'bills.view',
                'payments.view',
                'finance.view',
                'reports.view',
                'audit.view',
            ],

            // =====================================================
            // KETUA INDUK
            // =====================================================
            'ketua_induk' => [
                'dashboard.view',
                'students.view',
                'teachers.view',
                'attendance.view',
                'bills.view',
                'payments.view',
                'finance.view',
                'finance.approve',
                'reports.view',
                'audit.view',
            ],

            // =====================================================
            // KEPALA UNIT
            // =====================================================
            'kepala_unit' => [
                'dashboard.view',
                'students.view',
                'teachers.view',
                'classes.view',
                'subjects.view',
                'attendance.view',
                'bills.view',
                'payments.view',
                'payments.confirm',
                'payments.cancel',
                'finance.view',
                'reports.view',
            ],
        ];

        foreach ($rolePermissions as $roleCode => $permissionCodes) {
            $role = Role::where('code', $roleCode)->first();

            if (! $role) {
                continue;
            }

            $permissionIds = collect($permissionCodes)
                ->map(fn($code) => $permissions[$code] ?? null)
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
