<?php

use App\Http\Controllers\Guru\AttendanceController;
use App\Http\Controllers\Guru\ClassController;
use App\Http\Controllers\Guru\TeacherAttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', function () {
        return view('guru.dashboard');
    })->name('dashboard');

    Route::get('/classes', [ClassController::class, 'index'])
        ->name('classes.index');

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::get('/attendance/filter-options', [AttendanceController::class, 'filterOptions'])
        ->name('attendance.filter-options');

    Route::get('/attendance/{teachingAssignment}/students', [
        AttendanceController::class,
        'students',
    ])->name('attendance.students');

    Route::post('/attendance', [
        AttendanceController::class,
        'store',
    ])->name('attendance.store');

    Route::get('/teacher-attendance', [
        TeacherAttendanceController::class,
        'index',
    ])->name('teacher-attendance.index');

    Route::get('/teacher-attendance/filter-options', [
        TeacherAttendanceController::class,
        'filterOptions',
    ])->name('teacher-attendance.filter-options');

    Route::get('/attendance/{attendance}/edit', [
        AttendanceController::class,
        'edit',
    ])->name('attendance.edit');

    Route::put('/attendance/{attendance}', [
        AttendanceController::class,
        'update',
    ])->name('attendance.update');

    Route::delete('/attendance/{attendance}', [
        AttendanceController::class,
        'destroy',
    ])->name('attendance.destroy');
});
