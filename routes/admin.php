<?php

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
            ->get('/dashboard', function () {
                return view('admin.dashboard');
            })
            ->name('dashboard');


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

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        Route::middleware('permission:users.view')
            ->get('/users', [UserController::class, 'index'])
            ->name('users.index');

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
    });


Route::middleware(['auth', 'organization.scope'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/students', [StudentController::class, 'index'])
            ->name('students.index');

        Route::middleware('permission:students.manage')
            ->get('/students/import', [StudentImportController::class, 'create'])
            ->name('students.import');

        Route::middleware('permission:students.manage')
            ->post('/students/import', [StudentImportController::class, 'store'])
            ->name('students.import.store');

        Route::middleware('permission:students.manage')
            ->post('/students', [StudentController::class, 'store'])
            ->name('students.store');

        Route::middleware('permission:students.view')
            ->get('/students/export', [StudentController::class, 'export'])
            ->name('students.export');

        Route::middleware('permission:students.manage')
            ->get('/students/import/template', [StudentController::class, 'downloadTemplate'])
            ->name('students.import.template');

        Route::put('students/{student}', [StudentController::class, 'update'])
            ->name('students.update');

        Route::patch('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])
            ->name('students.toggle-status');


        Route::get(
            'students/placement',
            [StudentPlacementController::class, 'index']
        )->name('students.placement.index');

        Route::post(
            'students/placement',
            [StudentPlacementController::class, 'store']
        )->name('students.placement.store');


        Route::get(
            'students/promotion',
            [StudentPromotionController::class, 'index']
        )->name('students.promotion.index');

        Route::post(
            'students/promotion',
            [StudentPromotionController::class, 'store']
        )->name('students.promotion.store');




        Route::middleware('permission:teachers.view')
            ->get('/teachers', [TeacherController::class, 'index'])
            ->name('teachers.index');

        Route::middleware('permission:teachers.view')
            ->get(
                'teachers/export',
                [TeacherController::class, 'export']
            )
            ->name('teachers.export');

        Route::middleware('permission:teachers.manage')
            ->get('/teachers/create', [TeacherController::class, 'create'])
            ->name('teachers.create');

        Route::middleware('permission:teachers.manage')
            ->post('/teachers', [TeacherController::class, 'store'])
            ->name('teachers.store');

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

        Route::get('teachers/{teacher}/edit', [TeacherController::class, 'edit'])
            ->name('teachers.edit');

        Route::put('teachers/{teacher}', [TeacherController::class, 'update'])
            ->name('teachers.update');

        Route::patch(
            'teachers/{teacher}/toggle-status',
            [TeacherController::class, 'toggleStatus']
        )->name('teachers.toggle-status');

        Route::middleware('permission:teachers.manage')
            ->patch('teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])
            ->name('teachers.reset-password');

        Route::middleware('permission:teachers.manage')
            ->patch('teachers/{teacher}/create-account', [TeacherController::class, 'createAccount'])
            ->name('teachers.create-account');




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

        Route::delete(
            'students/classes/{schoolClass}',
            [SchoolClassController::class, 'destroy']
        )->name('school-classes.destroy');



        Route::get(
            'students/class-transfer',
            [StudentClassTransferController::class, 'index']
        )->name('students.class-transfer.index');

        Route::post(
            'students/class-transfer',
            [StudentClassTransferController::class, 'store']
        )->name('students.class-transfer.store');






        Route::prefix('finance')
            ->name('finance.')
            ->group(function () {

                Route::get(
                    '/transactions',
                    [FinanceTransactionController::class, 'index']
                )->name('transactions.index');

                Route::post(
                    '/transactions',
                    [FinanceTransactionController::class, 'store']
                )->name('transactions.store');

                Route::post(
                    '/transactions/{transaction}/cancel',
                    [FinanceTransactionController::class, 'cancel']
                )->name(
                    'transactions.cancel'
                );

                Route::get(
                    'deposits',
                    [FinanceDepositController::class, 'index']
                )->name('deposits.index');

                Route::post(
                    'deposits',
                    [FinanceDepositController::class, 'store']
                )->name('deposits.store');

                Route::post(
                    'finance/deposits/{deposit}/confirm',
                    [FinanceDepositController::class, 'confirm']
                )->name(
                    'deposits.confirm'
                );

                Route::post(
                    'finance/deposits/{deposit}/reject',
                    [FinanceDepositController::class, 'reject']
                )->name(
                    'deposits.reject'
                );
            });






        Route::get(
            '/notifications',
            [NotificationController::class, 'index']
        )->name('notifications.index');

        Route::post(
            'notifications/{notification}/read',
            [NotificationController::class, 'markAsRead']
        )->name('notifications.read');

        Route::post(
            'notifications/read-all',
            [NotificationController::class, 'markAllAsRead']
        )->name('notifications.read-all');

        Route::get(
            '/notifications/{notification}/deposit-proof',
            [NotificationController::class, 'depositProof']
        )->name('notifications.deposit-proof');





        /*
        |--------------------------------------------------------------------------
        | Master Jenis Tagihan
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/finance/bill-types',
            [BillTypeController::class, 'index']
        )->name('finance.bill-types.index');

        Route::post(
            '/finance/bill-types',
            [BillTypeController::class, 'store']
        )->name('finance.bill-types.store');

        Route::put(
            '/finance/bill-types/{billType}',
            [BillTypeController::class, 'update']
        )->name('finance.bill-types.update');

        Route::delete(
            '/finance/bill-types/{billType}',
            [BillTypeController::class, 'destroy']
        )->name('finance.bill-types.destroy');


        Route::get(
            '/finance/bills',
            [StudentBillController::class, 'index']
        )->name('finance.bills.index');

        Route::post(
            '/finance/bills',
            [StudentBillController::class, 'store']
        )->name('finance.bills.store');

        Route::put(
            '/finance/bills/{studentBill}',
            [StudentBillController::class, 'update']
        )->name('finance.bills.update');

        Route::post(
            '/finance/bills/{studentBill}/cancel',
            [StudentBillController::class, 'cancel']
        )->name('finance.bills.cancel');





        Route::get(
            'finance/payments',
            [
                PaymentController::class,
                'index',
            ]
        )->name(
            'finance.payments.index'
        );

        Route::get(
            'finance/payments/student/{studentAcademicYear}/bills',
            [
                PaymentController::class,
                'studentBills',
            ]
        )->name(
            'finance.payments.student-bills'
        );

        Route::post(
            'finance/payments',
            [
                PaymentController::class,
                'store',
            ]
        )->name(
            'finance.payments.store'
        );

        Route::get(
            'finance/payments/{payment}/detail',
            [
                PaymentController::class,
                'detail',
            ]
        )->name(
            'finance.payments.detail'
        );

        Route::post(
            'finance/payments/{payment}/confirm',
            [
                PaymentController::class,
                'confirm',
            ]
        )->name(
            'finance.payments.confirm'
        );

        Route::post(
            'finance/payments/{payment}/cancel',
            [
                PaymentController::class,
                'cancel',
            ]
        )->name(
            'finance.payments.cancel'
        );
    });
