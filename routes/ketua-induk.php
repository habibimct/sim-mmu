<?php

use App\Http\Controllers\KetuaInduk\DashboardController;
use App\Http\Controllers\KetuaInduk\FinanceController;
use App\Http\Controllers\KetuaInduk\TeacherController;
use App\Http\Controllers\KetuaInduk\StudentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
])
    ->prefix('ketua-induk')
    ->name('ketua-induk.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Keuangan
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/keuangan',
            [FinanceController::class, 'index']
        )->name('finance.index');


        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [NotificationController::class, 'index']
        )->name('notifications.index');




        Route::get(
            '/notifikasi/{notification}/bukti-setoran',
            [NotificationController::class, 'depositProof']
        )->name('notifications.deposit.proof');


        /*
        |--------------------------------------------------------------------------
        | Guru
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/guru',
            [TeacherController::class, 'index']
        )->name('teachers.index');


        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/siswa',
            [StudentController::class, 'index']
        )->name('students.index');
    });
