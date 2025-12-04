<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardAvailabilityController;
use App\Http\Controllers\DashboardEmployeesController;
use App\Http\Controllers\DashboardOverviewController;
use App\Http\Controllers\DashboardPatientsController;
use App\Http\Controllers\DashboardAppointmentsController;
use App\Http\Controllers\DashboardInvoicesController;
use App\Http\Controllers\DashboardMessagesController;
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

    Route::get('/dashboard/availability', [DashboardAvailabilityController::class, 'index'])
        ->name('dashboard.availability');

    Route::get('/dashboard/patients', [DashboardPatientsController::class, 'index'])
        ->name('dashboard.patients');

    Route::get('/dashboard/appointments', [DashboardAppointmentsController::class, 'index'])
        ->name('dashboard.appointments');

    Route::get('/dashboard/invoices', [DashboardInvoicesController::class, 'index'])
        ->name('dashboard.invoices');

    Route::get('/dashboard/messages', [DashboardMessagesController::class, 'index'])
        ->name('dashboard.messages');
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
