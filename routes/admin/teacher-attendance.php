<?php

use App\Http\Controllers\Admin\TeacherAttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin/teacher-attendance')
    ->name('admin.teacher-attendance.')
    ->group(function () {

        Route::get('/', [
            TeacherAttendanceController::class,
            'index',
        ])->name('index');

        Route::get('/teacher-attendance/filter-options', [
            TeacherAttendanceController::class,
            'filterOptions',
        ])->name('filter-options');

        Route::get(
            '/{attendance}/detail',
            [TeacherAttendanceController::class, 'detail']
        )->name('detail');
    });
