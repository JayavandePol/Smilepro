<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardEmployeesController;
use App\Http\Controllers\DashboardOverviewController;
use App\Http\Controllers\DashboardUsersController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Overview
    Route::get('/dashboard', [DashboardOverviewController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/users', [DashboardUsersController::class, 'index'])
        ->name('dashboard.users');

    Route::get('/dashboard/employees', [DashboardEmployeesController::class, 'index'])
        ->name('dashboard.employees');
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
