
<?php

use App\Http\Controllers\Wali\DashboardController;
use App\Http\Controllers\Wali\AttendanceController;
use App\Http\Controllers\Wali\BillController;
use App\Http\Controllers\Wali\ChildrenController;
use App\Http\Controllers\Wali\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('wali')
    ->name('wali.')
    ->group(function () {
        Route::get('/dashboard', [
            DashboardController::class,
            'index',
        ])->name('dashboard');

        Route::get(
            '/attendance/{studentId}',
            [AttendanceController::class, 'index']
        )->whereNumber('studentId')
            ->name('attendance.index');

        Route::get(
            '/bills/{studentId}',
            [BillController::class, 'index']
        )->whereNumber('studentId')
            ->name('bills.index');

        Route::get('/children', [
            ChildrenController::class,
            'index',
        ])->name('children.index');

        Route::get('/profile', [
            ProfileController::class,
            'index',
        ])->name('profile');

        Route::put('/profile', [
            ProfileController::class,
            'update',
        ])->name('profile.update');

        Route::put('/profile/password', [
            ProfileController::class,
            'updatePassword',
        ])->name('profile.password.update');
    });
