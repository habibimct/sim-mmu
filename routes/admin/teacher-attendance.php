<?php

use App\Http\Controllers\Admin\TeacherAttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'permission:attendance.view',
])
    ->prefix('admin/teacher-attendance')
    ->name('admin.teacher-attendance.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Daftar Absensi Guru
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            TeacherAttendanceController::class,
            'index',
        ])->name('index');


        /*
        |--------------------------------------------------------------------------
        | Filter Options
        |--------------------------------------------------------------------------
        */

        Route::get('/filter-options', [
            TeacherAttendanceController::class,
            'filterOptions',
        ])->name('filter-options');


        /*
        |--------------------------------------------------------------------------
        | Detail Absensi
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{attendance}/detail',
            [TeacherAttendanceController::class, 'detail']
        )->name('detail');

    });
