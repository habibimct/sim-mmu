<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherImportController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\StudentClassTransferController;
use App\Http\Controllers\Admin\StudentPlacementController;
use App\Http\Controllers\Admin\StudentPromotionController;
use App\Http\Controllers\Admin\FinanceTransactionController;
use App\Http\Controllers\Admin\FinanceDepositController;
use App\Http\Controllers\Admin\BillTypeController;
use App\Http\Controllers\Admin\StudentBillController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:dashboard.view')
            ->get('/dashboard', [
                AdminDashboardController::class,
                'index',
            ])
            ->name('dashboard');

        Route::middleware('permission:dashboard.view')
            ->get('/activity-logs/{activity}', [
                AdminDashboardController::class,
                'activityDetail',
            ])
            ->name('activity-logs.detail');

        Route::middleware('permission:dashboard.view')
            ->get('/activity-logs', [
                AdminDashboardController::class,
                'activityLogs',
            ])
            ->name('activity-logs.index');


        /*
        |--------------------------------------------------------------------------
        | Organisasi
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:organizations.view')
            ->get('/organizations', [OrganizationController::class, 'index'])
            ->name('organizations.index');

        Route::middleware('permission:organizations.manage')
            ->get('/organizations/create', [OrganizationController::class, 'create'])
            ->name('organizations.create');

        Route::middleware('permission:organizations.manage')
            ->post('/organizations', [OrganizationController::class, 'store'])
            ->name('organizations.store');

        Route::middleware('permission:organizations.manage')
            ->get('/organizations/{organization}/edit', [OrganizationController::class, 'edit'])
            ->name('organizations.edit');

        Route::middleware('permission:organizations.manage')
            ->put('/organizations/{organization}', [OrganizationController::class, 'update'])
            ->name('organizations.update');

        Route::middleware('permission:organizations.manage')
            ->patch(
                '/organizations/{organization}/toggle-status',
                [OrganizationController::class, 'toggleStatus']
            )
            ->name('organizations.toggle-status');

        Route::delete('/organizations/{organization}', [
            OrganizationController::class,
            'destroy',
        ])->name('organizations.destroy');

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:users.view')
            ->get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/export', [UserController::class, 'export'])
            ->name('users.export');

        Route::get('/users/template', [UserController::class, 'downloadTemplate'])
            ->name('users.template');

        Route::post('/users/import', [UserController::class, 'import'])
            ->name('users.import');

        Route::middleware('permission:users.manage')
            ->get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::middleware('permission:users.manage')
            ->post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::middleware('permission:users.manage')
            ->get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::middleware('permission:users.manage')
            ->put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::patch(
            '/users/{user}/reset-password',
            [UserController::class, 'resetPassword']
        )->name('users.reset-password');

        Route::delete(
            '/users/{user}',
            [UserController::class, 'destroy']
        )->name('users.destroy');
    });


Route::middleware(['auth', 'organization.scope'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Siswa - Melihat
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.view')
            ->get('/students', [StudentController::class, 'index'])
            ->name('students.index');


        /*
        |--------------------------------------------------------------------------
        | Siswa - Import
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.manage')
            ->get('/students/import', [StudentImportController::class, 'create'])
            ->name('students.import');

        Route::middleware('permission:students.manage')
            ->post('/students/import', [StudentImportController::class, 'store'])
            ->name('students.import.store');


        /*
        |--------------------------------------------------------------------------
        | Siswa - Tambah
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.manage')
            ->post('/students', [StudentController::class, 'store'])
            ->name('students.store');


        /*
        |--------------------------------------------------------------------------
        | Siswa - Export
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.view')
            ->get('/students/export', [StudentController::class, 'export'])
            ->name('students.export');


        /*
        |--------------------------------------------------------------------------
        | Siswa - Template Import
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.manage')
            ->get('/students/import/template', [StudentController::class, 'downloadTemplate'])
            ->name('students.import.template');


        /*
        |--------------------------------------------------------------------------
        | Siswa - Ubah
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.manage')
            ->put('/students/{student}', [StudentController::class, 'update'])
            ->name('students.update');


        /*
        |--------------------------------------------------------------------------
        | Siswa - Aktif / Nonaktif
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.manage')
            ->patch('/students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])
            ->name('students.toggle-status');


        /*
        |--------------------------------------------------------------------------
        | Siswa Berdasarkan Kelas
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.view')
            ->get(
                '/students/class/{schoolClass}/students',
                [StudentController::class, 'studentsByClass']
            )
            ->name('students.class-students');


        /*
        |--------------------------------------------------------------------------
        | Penempatan Siswa
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.manage')
            ->get(
                '/students/placement',
                [StudentPlacementController::class, 'index']
            )
            ->name('students.placement.index');

        Route::middleware('permission:students.manage')
            ->post(
                '/students/placement',
                [StudentPlacementController::class, 'store']
            )
            ->name('students.placement.store');


        /*
        |--------------------------------------------------------------------------
        | Kenaikan Siswa
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:students.manage')
            ->get(
                '/students/promotion',
                [StudentPromotionController::class, 'index']
            )
            ->name('students.promotion.index');

        Route::middleware('permission:students.manage')
            ->post(
                '/students/promotion',
                [StudentPromotionController::class, 'store']
            )
            ->name('students.promotion.store');



        Route::middleware('permission:teachers.view')
            ->get('/teachers', [TeacherController::class, 'index'])
            ->name('teachers.index');

        Route::middleware('permission:teachers.view')
            ->get(
                'teachers/export',
                [TeacherController::class, 'export']
            )
            ->name('teachers.export');


        /*
|--------------------------------------------------------------------------
| Guru - Tambah
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:teachers.manage')
            ->get('/teachers/create', [TeacherController::class, 'create'])
            ->name('teachers.create');

        Route::middleware('permission:teachers.manage')
            ->post('/teachers', [TeacherController::class, 'store'])
            ->name('teachers.store');


        /*
|--------------------------------------------------------------------------
| Guru - Import
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:teachers.manage')
            ->get(
                'teachers/import',
                [TeacherImportController::class, 'create']
            )
            ->name('teachers.import');

        Route::middleware('permission:teachers.manage')
            ->post(
                'teachers/import',
                [TeacherController::class, 'storeImport']
            )
            ->name('teachers.import.store');

        Route::middleware('permission:teachers.manage')
            ->get(
                'teachers/import/template',
                [TeacherController::class, 'downloadTemplate']
            )
            ->name('teachers.import.template');


        /*
|--------------------------------------------------------------------------
| Guru - Ubah
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:teachers.manage')
            ->get(
                'teachers/{teacher}/edit',
                [TeacherController::class, 'edit']
            )
            ->name('teachers.edit');

        Route::middleware('permission:teachers.manage')
            ->put(
                'teachers/{teacher}',
                [TeacherController::class, 'update']
            )
            ->name('teachers.update');


        /*
|--------------------------------------------------------------------------
| Guru - Aktif / Nonaktif
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:teachers.manage')
            ->patch(
                'teachers/{teacher}/toggle-status',
                [TeacherController::class, 'toggleStatus']
            )
            ->name('teachers.toggle-status');


        /*
|--------------------------------------------------------------------------
| Guru - Akun
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:teachers.manage')
            ->patch(
                'teachers/{teacher}/reset-password',
                [TeacherController::class, 'resetPassword']
            )
            ->name('teachers.reset-password');

        Route::middleware('permission:teachers.manage')
            ->patch(
                'teachers/{teacher}/create-account',
                [TeacherController::class, 'createAccount']
            )
            ->name('teachers.create-account');


        /*
|--------------------------------------------------------------------------
| Guru - Hapus
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:teachers.manage')
            ->delete(
                '/teachers/{teacher}',
                [TeacherController::class, 'destroy']
            )
            ->name('teachers.destroy');



        /*
|--------------------------------------------------------------------------
| Tahun Ajaran
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:academic_years.view')
            ->get(
                '/academic-years',
                [AcademicYearController::class, 'index']
            )
            ->name('academic-years.index');

        Route::middleware('permission:academic_years.manage')
            ->get(
                '/academic-years/create',
                [AcademicYearController::class, 'create']
            )
            ->name('academic-years.create');

        Route::middleware('permission:academic_years.manage')
            ->post(
                '/academic-years',
                [AcademicYearController::class, 'store']
            )
            ->name('academic-years.store');

        Route::middleware('permission:academic_years.manage')
            ->get(
                '/academic-years/{academicYear}/edit',
                [AcademicYearController::class, 'edit']
            )
            ->name('academic-years.edit');

        Route::middleware('permission:academic_years.manage')
            ->put(
                '/academic-years/{academicYear}',
                [AcademicYearController::class, 'update']
            )
            ->name('academic-years.update');

        Route::middleware('permission:academic_years.manage')
            ->patch(
                '/academic-years/{academicYear}/toggle-status',
                [AcademicYearController::class, 'toggleStatus']
            )
            ->name('academic-years.toggle-status');

        Route::middleware('permission:academic_years.manage')
            ->delete(
                '/academic-years/{academicYear}',
                [AcademicYearController::class, 'destroy']
            )->name('academic-years.destroy');


        /*
|--------------------------------------------------------------------------
| Kelas
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:classes.view')
            ->get(
                '/school-classes',
                [SchoolClassController::class, 'index']
            )
            ->name('school-classes.index');

        Route::middleware('permission:classes.manage')
            ->get(
                '/school-classes/create',
                [SchoolClassController::class, 'create']
            )
            ->name('school-classes.create');

        Route::middleware('permission:classes.manage')
            ->post(
                '/school-classes',
                [SchoolClassController::class, 'store']
            )
            ->name('school-classes.store');

        Route::middleware('permission:classes.manage')
            ->get(
                'school-classes/bulk-create',
                [SchoolClassController::class, 'bulkCreate']
            )->name('school-classes.bulk-create');

        Route::middleware('permission:classes.manage')
            ->post(
                'school-classes/bulk-store',
                [SchoolClassController::class, 'bulkStore']
            )->name('school-classes.bulk-store');

        Route::middleware('permission:classes.manage')
            ->get(
                '/school-classes/{schoolClass}/edit',
                [SchoolClassController::class, 'edit']
            )
            ->name('school-classes.edit');

        Route::middleware('permission:classes.manage')
            ->put(
                '/school-classes/{schoolClass}',
                [SchoolClassController::class, 'update']
            )
            ->name('school-classes.update');


        Route::middleware('permission:classes.manage')
            ->patch(
                '/school-classes/{schoolClass}/toggle-status',
                [SchoolClassController::class, 'toggleStatus']
            )
            ->name('school-classes.toggle-status');


        Route::middleware('permission:classes.manage')
            ->delete(
                '/school-classes/bulk-destroy',
                [SchoolClassController::class, 'bulkDestroy']
            )
            ->name('school-classes.bulk-destroy');


        Route::middleware('permission:classes.manage')
            ->delete(
                'students/classes/{schoolClass}',
                [SchoolClassController::class, 'destroy']
            )
            ->name('school-classes.destroy');



        /*
|--------------------------------------------------------------------------
| Pemindahan Siswa Antar Kelas
|--------------------------------------------------------------------------
*/

        Route::middleware('permission:students.manage')
            ->get(
                'students/class-transfer',
                [StudentClassTransferController::class, 'index']
            )
            ->name('students.class-transfer.index');

        Route::middleware('permission:students.manage')
            ->post(
                'students/class-transfer',
                [StudentClassTransferController::class, 'store']
            )
            ->name('students.class-transfer.store');





        Route::prefix('finance')
            ->name('finance.')
            ->group(function () {

                Route::get(
                    '/transactions',
                    [FinanceTransactionController::class, 'index']
                )
                    ->middleware('permission:finance.view')
                    ->name('transactions.index');

                Route::post(
                    '/transactions',
                    [FinanceTransactionController::class, 'store']
                )
                    ->middleware('permission:finance.manage')
                    ->name('transactions.store');

                Route::post(
                    '/transactions/{transaction}/cancel',
                    [FinanceTransactionController::class, 'cancel']
                )
                    ->middleware('permission:finance.manage')
                    ->name('transactions.cancel');

                Route::get(
                    '/deposits',
                    [FinanceDepositController::class, 'index']
                )
                    ->middleware('permission:finance.view')
                    ->name('deposits.index');

                Route::post(
                    '/deposits',
                    [FinanceDepositController::class, 'store']
                )
                    ->middleware('permission:finance.manage')
                    ->name('deposits.store');

                Route::post(
                    '/deposits/{deposit}/confirm',
                    [FinanceDepositController::class, 'confirm']
                )
                    ->middleware('permission:finance.approve')
                    ->name('deposits.confirm');

                Route::post(
                    '/deposits/{deposit}/reject',
                    [FinanceDepositController::class, 'reject']
                )
                    ->middleware('permission:finance.approve')
                    ->name('deposits.reject');
            });






        Route::get(
            '/notifications',
            [NotificationController::class, 'index']
        )
            ->middleware('permission:notifications.view')
            ->name('notifications.index');

        Route::post(
            'notifications/{notification}/read',
            [NotificationController::class, 'markAsRead']
        )
            ->middleware('permission:notifications.view')
            ->name('notifications.read');

        Route::post(
            'notifications/read-all',
            [NotificationController::class, 'markAllAsRead']
        )
            ->middleware('permission:notifications.view')
            ->name('notifications.read-all');

        Route::get(
            '/notifications/{notification}/deposit-proof',
            [NotificationController::class, 'depositProof']
        )
            ->middleware('permission:notifications.view')
            ->name('notifications.deposit-proof');





        /*
        |--------------------------------------------------------------------------
        | Master Jenis Tagihan
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/finance/bill-types',
            [BillTypeController::class, 'index']
        )
            ->middleware('permission:bills.view')
            ->name('finance.bill-types.index');

        Route::post(
            '/finance/bill-types',
            [BillTypeController::class, 'store']
        )
            ->middleware('permission:bills.manage')
            ->name('finance.bill-types.store');

        Route::put(
            '/finance/bill-types/{billType}',
            [BillTypeController::class, 'update']
        )
            ->middleware('permission:bills.manage')
            ->name('finance.bill-types.update');

        Route::delete(
            '/finance/bill-types/{billType}',
            [BillTypeController::class, 'destroy']
        )
            ->middleware('permission:bills.manage')
            ->name('finance.bill-types.destroy');


        Route::get(
            '/finance/bills',
            [StudentBillController::class, 'index']
        )
            ->middleware('permission:bills.view')
            ->name('finance.bills.index');

        Route::post(
            '/finance/bills',
            [StudentBillController::class, 'store']
        )
            ->middleware('permission:bills.manage')
            ->name('finance.bills.store');

        Route::put(
            '/finance/bills/{studentBill}',
            [StudentBillController::class, 'update']
        )
            ->middleware('permission:bills.manage')
            ->name('finance.bills.update');

        Route::post(
            '/finance/bills/{studentBill}/cancel',
            [StudentBillController::class, 'cancel']
        )
            ->middleware('permission:bills.manage')
            ->name('finance.bills.cancel');





        Route::get(
            'finance/payments',
            [PaymentController::class, 'index']
        )
            ->middleware('permission:payments.view')
            ->name('finance.payments.index');

        Route::get(
            'finance/payments/student/{studentAcademicYear}/bills',
            [PaymentController::class, 'studentBills']
        )
            ->middleware('permission:payments.view')
            ->name('finance.payments.student-bills');

        Route::post(
            'finance/payments',
            [PaymentController::class, 'store']
        )
            ->middleware('permission:payments.create')
            ->name('finance.payments.store');

        Route::get(
            'finance/payments/{payment}/detail',
            [PaymentController::class, 'detail']
        )
            ->middleware('permission:payments.view')
            ->name('finance.payments.detail');

        Route::post(
            'finance/payments/{payment}/confirm',
            [PaymentController::class, 'confirm']
        )
            ->middleware('permission:payments.confirm')
            ->name('finance.payments.confirm');

        Route::post(
            'finance/payments/{payment}/cancel',
            [PaymentController::class, 'cancel']
        )
            ->middleware('permission:payments.cancel')
            ->name('finance.payments.cancel');
    });
