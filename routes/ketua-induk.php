<?php

use App\Http\Controllers\KetuaInduk\DashboardController;
use App\Http\Controllers\KetuaInduk\FinanceController;
use App\Http\Controllers\KetuaInduk\TeacherController;
use App\Http\Controllers\KetuaInduk\StudentController;
use App\Http\Controllers\KetuaInduk\KetuaIndukFinanceReportController;
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
        )
            ->middleware('permission:dashboard.view')
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Keuangan
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/keuangan',
            [FinanceController::class, 'index']
        )
            ->middleware('permission:finance.view')
            ->name('finance.index');


        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/notifikasi',
            [NotificationController::class, 'index']
        )
            ->middleware('permission:notifications.view')
            ->name('notifications.index');


        Route::get(
            '/notifikasi/{notification}/bukti-setoran',
            [NotificationController::class, 'depositProof']
        )
            ->middleware('permission:notifications.view')
            ->name('notifications.deposit.proof');


        /*
        |--------------------------------------------------------------------------
        | Guru
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/guru',
            [TeacherController::class, 'index']
        )
            ->middleware('permission:teachers.view')
            ->name('teachers.index');


        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/siswa',
            [StudentController::class, 'index']
        )
            ->middleware('permission:students.view')
            ->name('students.index');


        /*
|--------------------------------------------------------------------------
| Laporan Keuangan
|--------------------------------------------------------------------------
*/

        Route::get(
            '/laporan/keuangan',
            [KetuaIndukFinanceReportController::class, 'index']
        )
            ->middleware('permission:finance.view')
            ->name('reports.finance.index');


        Route::get(
            '/laporan/keuangan/pdf',
            [KetuaIndukFinanceReportController::class, 'pdf']
        )
            ->middleware('permission:finance.view')
            ->name('reports.finance.pdf');
    });
