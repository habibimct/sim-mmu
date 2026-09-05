<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [
    AuthenticatedSessionController::class,
    'redirectToDashboard',
])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'permission:users.view'])
    ->get('/test-users', function () {
        return 'Anda memiliki permission users.view';
    });

Route::middleware(['auth', 'permission:finance.approve'])
    ->get('/test-finance', function () {
        return 'Anda memiliki permission finance.approve';
    });

Route::middleware(['auth', 'permission:dashboard.view'])
    ->get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })
    ->name('admin.dashboard');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/admin/subjects.php';
require __DIR__ . '/admin/student-attendance.php';
require __DIR__ . '/admin/teaching-assignments.php';
require __DIR__ . '/admin/teacher-attendance.php';
require __DIR__ . '/notifications.php';
require __DIR__ . '/ketua-induk.php';
require __DIR__ . '/kepala-unit.php';
require __DIR__ . '/guru/web.php';
