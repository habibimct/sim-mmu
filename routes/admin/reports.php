<?php

use App\Http\Controllers\Admin\StudentReportController;
use App\Http\Controllers\Admin\TeacherReportController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\Reports\FinanceReportController;
use App\Http\Controllers\Admin\Reports\BillReportController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'can:reports.view',
])
    ->prefix('admin/laporan')
    ->name('admin.reports.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Laporan Siswa
        |--------------------------------------------------------------------------
        */

        Route::get('/siswa', [StudentReportController::class, 'index'])
            ->name('students.index');

        Route::get('/siswa/kelas', [StudentReportController::class, 'classes'])
            ->name('students.classes');

        Route::get('/siswa/excel', [StudentReportController::class, 'excel'])
            ->name('students.excel');

        Route::get('/siswa/pdf', [StudentReportController::class, 'pdf'])
            ->name('students.pdf');



        // Laporan Guru
        Route::get('/guru', [TeacherReportController::class, 'index'])
            ->name('teachers.index');

        Route::get('/guru/excel', [TeacherReportController::class, 'excel'])
            ->name('teachers.excel');

        Route::get('/guru/pdf', [TeacherReportController::class, 'pdf'])
            ->name('teachers.pdf');



        // Laporan Absensi
        Route::get('/absensi', [AttendanceReportController::class, 'index'])
            ->name('attendance.index');

        Route::get('/absensi/excel', [AttendanceReportController::class, 'excel'])
            ->name('attendance.excel');

        Route::get('/absensi/pdf', [AttendanceReportController::class, 'pdf'])
            ->name('attendance.pdf');

        Route::get('/absensi/kelas', [AttendanceReportController::class, 'classes'])
            ->name('attendance.classes');

        Route::get('/absensi/siswa/excel', [AttendanceReportController::class, 'studentExcel'])
            ->name('attendance.student.excel');

        Route::get('/absensi/mapel', [AttendanceReportController::class, 'subjects'])
            ->name('attendance.subjects');

        Route::get('/absensi/siswa/pdf', [AttendanceReportController::class, 'studentPdf'])
            ->name('attendance.student.pdf');



        Route::get('/keuangan', [FinanceReportController::class, 'index'])
            ->name('finance.index');

        Route::get('/keuangan/excel', [FinanceReportController::class, 'excel'])
            ->name('finance.excel');

        Route::get('/keuangan/pdf', [FinanceReportController::class, 'pdf'])
            ->name('finance.pdf');

        Route::get('/keuangan/kategori', [FinanceReportController::class, 'categories'])
            ->name('finance.categories');



        Route::get('/tagihan', [BillReportController::class, 'index'])
            ->name('bills.index');

        Route::get('/tagihan/excel', [BillReportController::class, 'excel'])
            ->name('bills.excel');

        Route::get('/tagihan/pdf', [BillReportController::class, 'pdf'])
            ->name('bills.pdf');

        Route::get('/tagihan/jenis', [BillReportController::class, 'billTypes'])
            ->name('bills.bill-types');

        Route::get('/tagihan/periode', [BillReportController::class, 'periods'])
            ->name('bills.periods');
    });
