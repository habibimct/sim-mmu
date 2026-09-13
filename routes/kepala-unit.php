<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\KepalaUnit\FinanceTransactionController;
use App\Http\Controllers\KepalaUnit\FinanceSummaryController;
use App\Http\Controllers\KepalaUnit\TeacherController;
use App\Http\Controllers\KepalaUnit\StudentController;
use App\Http\Controllers\KepalaUnit\AttendanceController;
use App\Http\Controllers\KepalaUnit\StudentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
])
    ->prefix('kepala-unit')
    ->name('kepala-unit.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', function () {
            return view('kepala-unit.dashboard');
        })
            ->middleware('permission:dashboard.view')
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Notifikasi
        |--------------------------------------------------------------------------
        */

        Route::get('/notifications', [
            NotificationController::class,
            'index',
        ])
            ->middleware('permission:notifications.view')
            ->name('notifications.index');

        Route::post('/notifications/read-all', [
            NotificationController::class,
            'markAllAsRead',
        ])
            ->middleware('permission:notifications.view')
            ->name('notifications.read-all');

        Route::post('/notifications/{notification}/read', [
            NotificationController::class,
            'markAsRead',
        ])
            ->middleware('permission:notifications.view')
            ->name('notifications.read');

        Route::get('/notifications/{notification}/deposit-proof', [
            NotificationController::class,
            'depositProof',
        ])
            ->middleware('permission:notifications.view')
            ->name('notifications.deposit-proof');


        /*
        |--------------------------------------------------------------------------
        | Keuangan
        |--------------------------------------------------------------------------
        */

        Route::get('/finance/transactions', [
            FinanceTransactionController::class,
            'index',
        ])
            ->middleware('permission:finance.view')
            ->name('finance.transactions.index');

        Route::get('/finance/summary', [
            FinanceSummaryController::class,
            'index',
        ])
            ->middleware('permission:finance.view')
            ->name('finance.summary.index');


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
        | Absensi Guru
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/absensi-guru',
            [AttendanceController::class, 'index']
        )
            ->middleware('permission:attendance.view')
            ->name('attendances.index');


        /*
        |--------------------------------------------------------------------------
        | Absensi Siswa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/absensi-siswa',
            [StudentAttendanceController::class, 'index']
        )
            ->middleware('permission:attendance.view')
            ->name('student-attendances.index');
    });
