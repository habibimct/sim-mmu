<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\KepalaUnit\FinanceTransactionController;
use App\Http\Controllers\KepalaUnit\FinanceSummaryController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',])
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
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Notifikasi
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [
        NotificationController::class,
        'index',
    ])->name('notifications.index');

    Route::post('/notifications/read-all', [
        NotificationController::class,
        'markAllAsRead',
    ])->name('notifications.read-all');

    Route::post('/notifications/{notification}/read', [
        NotificationController::class,
        'markAsRead',
    ])->name('notifications.read');

    Route::get('/notifications/{notification}/deposit-proof', [
        NotificationController::class,
        'depositProof',
    ])->name('notifications.deposit-proof');




    Route::get('/finance/transactions', [
        FinanceTransactionController::class,
        'index',
    ])->name('finance.transactions.index');

    Route::get('/finance/summary', [
        FinanceSummaryController::class,
        'index',
    ])->name('finance.summary.index');
});
