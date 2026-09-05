<?php

use App\Http\Controllers\Admin\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'permission:subjects.view',
])
    ->prefix('admin/subjects')
    ->name('admin.subjects.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Daftar Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            SubjectController::class,
            'index',
        ])->name('index');


        /*
        |--------------------------------------------------------------------------
        | Tambah Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        Route::post('/', [
            SubjectController::class,
            'store',
        ])
            ->middleware('permission:subjects.manage')
            ->name('store');


        /*
        |--------------------------------------------------------------------------
        | Edit Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        Route::put('/{subject}', [
            SubjectController::class,
            'update',
        ])
            ->middleware('permission:subjects.manage')
            ->name('update');


        /*
        |--------------------------------------------------------------------------
        | Hapus Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        Route::delete('/{subject}', [
            SubjectController::class,
            'destroy',
        ])
            ->middleware('permission:subjects.manage')
            ->name('destroy');

    });
