<?php

use App\Http\Controllers\Admin\StudentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/student-attendance/filter-options', [
            StudentAttendanceController::class,
            'filterOptions',
        ])->name('student-attendance.filter-options');

        Route::get('/student-attendance', [
            StudentAttendanceController::class,
            'index',
        ])->name('student-attendance.index');

    });
