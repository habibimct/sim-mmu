<?php

use App\Http\Controllers\Api\OfflineSyncController;
use App\Http\Controllers\Api\OfflineSyncTestController;
use App\Http\Controllers\Api\OfflineAttendanceSyncController;
use Illuminate\Support\Facades\Route;

Route::post('/offline/sync-test', [OfflineSyncTestController::class, 'store']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/offline/sync', [OfflineSyncController::class, 'store']);

    Route::post(
        '/offline/attendance/sync',
        [OfflineAttendanceSyncController::class, 'store']
    );
});
