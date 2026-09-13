<?php

use App\Http\Controllers\Admin\Settings\IndukProfileController;
use App\Http\Controllers\Admin\Settings\PermissionController;
use App\Http\Controllers\Admin\Settings\UnitProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Permission Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:settings.manage'])
    ->prefix('admin/settings')
    ->name('admin.settings.')
    ->group(function () {

        Route::get('/permissions', [PermissionController::class, 'index'])
            ->name('permissions.index');

        Route::put('/permissions', [PermissionController::class, 'update'])
            ->name('permissions.update');
    });


/*
|--------------------------------------------------------------------------
| Profil Induk
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:profile_induk.manage'])
    ->prefix('admin/settings')
    ->name('admin.settings.')
    ->group(function () {

        Route::get('/profile-induk', [IndukProfileController::class, 'edit'])
            ->name('profile-induk.edit');

        Route::put('/profile-induk', [IndukProfileController::class, 'update'])
            ->name('profile-induk.update');

        Route::delete('/profile-induk/logo', [IndukProfileController::class, 'removeLogo'])
            ->name('profile-induk.logo.destroy');
    });


/*
|--------------------------------------------------------------------------
| Profil Unit
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:profile_unit.manage'])
    ->prefix('admin/settings')
    ->name('admin.settings.')
    ->group(function () {

        Route::get('/profile-unit', [UnitProfileController::class, 'index'])
            ->name('profile-unit.index');

        Route::put('/profile-unit/{unit}', [UnitProfileController::class, 'update'])
            ->name('profile-unit.update');

        Route::delete('/profile-unit/{unit}/logo', [UnitProfileController::class, 'removeLogo'])
            ->name('profile-unit.logo.destroy');
    });
