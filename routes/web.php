<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::resource('leads', LeadController::class);
    Route::resource('deals', DealController::class);

    Route::patch('tasks/{task}/completion', [TaskController::class, 'completion'])->name('tasks.completion');
    Route::resource('tasks', TaskController::class);
});

require __DIR__.'/settings.php';
