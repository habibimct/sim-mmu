<?php

use App\Http\Controllers\Guru\AttendanceController;
use App\Http\Controllers\Guru\ClassController;
use App\Http\Controllers\Guru\TeacherAttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', function () {
            return view('guru.dashboard');
        })
            ->middleware('permission:dashboard.view')
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Kelas
        |--------------------------------------------------------------------------
        */

        Route::get('/classes', [ClassController::class, 'index'])
            ->middleware('permission:classes.view')
            ->name('classes.index');


        /*
        |--------------------------------------------------------------------------
        | Absensi Siswa - Lihat
        |--------------------------------------------------------------------------
        */

        Route::get('/attendance', [AttendanceController::class, 'index'])
            ->middleware('permission:attendance.view')
            ->name('attendance.index');

        Route::get('/attendance/filter-options', [
            AttendanceController::class,
            'filterOptions',
        ])
            ->middleware('permission:attendance.view')
            ->name('attendance.filter-options');

        Route::get('/attendance/{teachingAssignment}/students', [
            AttendanceController::class,
            'students',
        ])
            ->middleware('permission:attendance.view')
            ->name('attendance.students');


        /*
        |--------------------------------------------------------------------------
        | Absensi Siswa - Mencatat
        |--------------------------------------------------------------------------
        */

        Route::post('/attendance', [
            AttendanceController::class,
            'store',
        ])
            ->middleware('permission:attendance.create')
            ->name('attendance.store');


        /*
        |--------------------------------------------------------------------------
        | Absensi Guru
        |--------------------------------------------------------------------------
        */

        Route::get('/teacher-attendance', [
            TeacherAttendanceController::class,
            'index',
        ])
            ->middleware('permission:attendance.view')
            ->name('teacher-attendance.index');

        Route::get('/teacher-attendance/filter-options', [
            TeacherAttendanceController::class,
            'filterOptions',
        ])
            ->middleware('permission:attendance.view')
            ->name('teacher-attendance.filter-options');


        /*
        |--------------------------------------------------------------------------
        | Absensi Siswa - Mengubah
        |--------------------------------------------------------------------------
        */

        Route::get('/attendance/{attendance}/edit', [
            AttendanceController::class,
            'edit',
        ])
            ->middleware('permission:attendance.update')
            ->name('attendance.edit');

        Route::put('/attendance/{attendance}', [
            AttendanceController::class,
            'update',
        ])
            ->middleware('permission:attendance.update')
            ->name('attendance.update');


        /*
        |--------------------------------------------------------------------------
        | Absensi Siswa - Menghapus
        |--------------------------------------------------------------------------
        |
        | Belum ada permission attendance.delete.
        | Untuk sementara kita pertahankan route dan akan menentukan
        | aturan penghapusannya setelah melihat business logic/controller.
        |
        */

        Route::delete('/attendance/{attendance}', [
            AttendanceController::class,
            'destroy',
        ])
            ->middleware('permission:attendance.update')
            ->name('attendance.destroy');

    });
