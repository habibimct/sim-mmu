<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Daftar notifikasi
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )
        ->middleware('permission:notifications.view')
        ->name('notifications.index');


    /*
    |--------------------------------------------------------------------------
    | Tandai satu notifikasi sudah dibaca
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/notifications/{notification}/read',
        [NotificationController::class, 'markAsRead']
    )
        ->middleware('permission:notifications.view')
        ->name('notifications.read');


    /*
    |--------------------------------------------------------------------------
    | Tandai semua notifikasi sudah dibaca
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'markAllAsRead']
    )
        ->middleware('permission:notifications.view')
        ->name('notifications.read-all');


    /*
    |--------------------------------------------------------------------------
    | Lihat slip setoran
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notifications/{notification}/deposit-proof',
        [NotificationController::class, 'depositProof']
    )
        ->middleware('permission:notifications.view')
        ->name('notifications.deposit-proof');

});
