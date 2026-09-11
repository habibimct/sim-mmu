<?php

use App\Http\Controllers\Admin\StudentReportController;
use App\Http\Controllers\Admin\TeacherReportController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\Reports\FinanceReportController;
use App\Http\Controllers\Admin\Reports\BillReportController;
use App\Http\Controllers\Admin\Reports\PaymentReportController;
use App\Http\Controllers\Admin\Reports\DepositReportController;
use App\Http\Controllers\Admin\Reports\AuditReportController;
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




        Route::get('/pembayaran', [PaymentReportController::class, 'index'])
            ->name('payments');

        Route::get('/pembayaran/{payment}/detail', [PaymentReportController::class, 'detail'])
            ->name('payments.detail');

        Route::get('/pembayaran/export-excel', [PaymentReportController::class, 'exportExcel'])
            ->name('payments.export-excel');

        Route::get('/pembayaran/export-pdf', [PaymentReportController::class, 'exportPdf'])
            ->name('payments.export-pdf');





        Route::get('/setoran', [DepositReportController::class, 'index'])
            ->name('deposits');

        Route::get('/setoran/{deposit}/detail', [DepositReportController::class, 'detail'])
            ->name('deposits.detail');

        Route::get('/setoran/export-excel', [DepositReportController::class, 'exportExcel'])
            ->name('deposits.export-excel');

        Route::get('/setoran/export-pdf', [DepositReportController::class, 'exportPdf'])
            ->name('deposits.export-pdf');





        Route::get('/audit', [AuditReportController::class, 'index'])
            ->name('audit');

        Route::get('/audit/{activity}/detail', [AuditReportController::class, 'detail'])
            ->name('audit.detail');

        Route::get('/audit/export-excel', [AuditReportController::class, 'exportExcel'])
            ->name('audit.export-excel');

        Route::get('/audit/export-pdf',[AuditReportController::class, 'exportPdf'])
            ->name('audit.export-pdf');
    });
