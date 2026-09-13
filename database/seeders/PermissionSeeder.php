<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['code' => 'dashboard.view', 'name' => 'Melihat Dashboard', 'module' => 'dashboard'],

            // Organisasi
            ['code' => 'organizations.view', 'name' => 'Melihat Organisasi', 'module' => 'organizations'],
            ['code' => 'organizations.manage', 'name' => 'Mengelola Organisasi', 'module' => 'organizations'],

            // Tahun Ajaran
            ['code' => 'academic_years.view', 'name' => 'Melihat Tahun Ajaran', 'module' => 'academic_years'],
            ['code' => 'academic_years.manage', 'name' => 'Mengelola Tahun Ajaran', 'module' => 'academic_years'],

            // User
            ['code' => 'users.view', 'name' => 'Melihat User', 'module' => 'users'],
            ['code' => 'users.manage', 'name' => 'Mengelola User', 'module' => 'users'],

            // Siswa
            ['code' => 'students.view', 'name' => 'Melihat Siswa', 'module' => 'students'],
            ['code' => 'students.manage', 'name' => 'Mengelola Siswa', 'module' => 'students'],

            // Guru
            ['code' => 'teachers.view', 'name' => 'Melihat Guru', 'module' => 'teachers'],
            ['code' => 'teachers.manage', 'name' => 'Mengelola Guru', 'module' => 'teachers'],

            // Kelas
            ['code' => 'classes.view', 'name' => 'Melihat Kelas', 'module' => 'classes'],
            ['code' => 'classes.manage', 'name' => 'Mengelola Kelas', 'module' => 'classes'],

            // Mata Pelajaran
            ['code' => 'subjects.view', 'name' => 'Melihat Mata Pelajaran', 'module' => 'subjects'],
            ['code' => 'subjects.manage', 'name' => 'Mengelola Mata Pelajaran', 'module' => 'subjects'],

            ['code' => 'teaching_assignments.view', 'name' => 'Melihat Penugasan Mengajar', 'module' => 'teaching_assignments'],
            ['code' => 'teaching_assignments.manage', 'name' => 'Mengelola Penugasan Mengajar', 'module' => 'teaching_assignments'],

            // Absensi
            ['code' => 'attendance.view', 'name' => 'Melihat Absensi', 'module' => 'attendance'],
            ['code' => 'attendance.create', 'name' => 'Mengisi Absensi', 'module' => 'attendance'],
            ['code' => 'attendance.update', 'name' => 'Mengubah Absensi', 'module' => 'attendance'],

            // Tagihan
            ['code' => 'bills.view', 'name' => 'Melihat Tagihan', 'module' => 'bills'],
            ['code' => 'bills.manage', 'name' => 'Mengelola Tagihan', 'module' => 'bills'],

            // Pembayaran
            ['code' => 'payments.view', 'name' => 'Melihat Pembayaran', 'module' => 'payments'],
            ['code' => 'payments.create', 'name' => 'Mencatat Pembayaran', 'module' => 'payments'],
            ['code' => 'payments.confirm', 'name' => 'Mengonfirmasi pembayaran', 'module' => 'payments'],
            ['code' => 'payments.cancel', 'name' => 'Membatalkan pembayaran', 'module' => 'payments'],

            // Notifikasi
            ['code' => 'notifications.view', 'name' => 'Melihat Notifikasi', 'module' => 'notifications'],

            // Keuangan
            ['code' => 'finance.view', 'name' => 'Melihat Keuangan', 'module' => 'finance'],
            ['code' => 'finance.manage', 'name' => 'Mengelola Keuangan', 'module' => 'finance'],
            ['code' => 'finance.approve', 'name' => 'Menyetujui Keuangan', 'module' => 'finance'],

            // Laporan
            ['code' => 'reports.view', 'name' => 'Melihat Laporan', 'module' => 'reports'],

            // Audit
            ['code' => 'audit.view', 'name' => 'Melihat Audit Log', 'module' => 'audit'],

            // Pengaturan
            ['code' => 'settings.manage', 'name' => 'Mengelola Pengaturan', 'module' => 'settings'],
            ['code' => 'profile_induk.manage', 'name' => 'Mengelola Profil Induk', 'module' => 'settings'],
            ['code' => 'profile_unit.manage', 'name' => 'Mengelola Profil Unit', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'code' => $permission['code'],
                ],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                ]
            );
        }
    }
}
