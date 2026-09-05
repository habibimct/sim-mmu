<?php

use App\Http\Controllers\Admin\TeachingAssignmentController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'permission:teaching_assignments.view',
])
    ->prefix('admin/teaching-assignments')
    ->name('admin.teaching-assignments.')
    ->group(function () {

        Route::get('/', [
            TeachingAssignmentController::class,
            'index',
        ])->name('index');


        Route::post('/', [
            TeachingAssignmentController::class,
            'store',
        ])
            ->middleware('permission:teaching_assignments.manage')
            ->name('store');


        Route::put('/{teachingAssignment}', [
            TeachingAssignmentController::class,
            'update',
        ])
            ->middleware('permission:teaching_assignments.manage')
            ->name('update');


        Route::delete('/{teachingAssignment}', [
            TeachingAssignmentController::class,
            'destroy',
        ])
            ->middleware('permission:teaching_assignments.manage')
            ->name('destroy');

    });
