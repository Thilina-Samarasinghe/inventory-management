<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Welcome page redirects to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory Items - Resource routes
    Route::resource('items', InventoryController::class)->except(['edit']);
    
    // Custom inventory routes
    Route::get('/items/deduct/create', [InventoryController::class, 'createDeduct'])
        ->name('items.deduct.create');
    Route::post('/items/deduct', [InventoryController::class, 'deduct'])
        ->name('items.deduct');
    Route::get('/items/{item}/history', [InventoryController::class, 'history'])
        ->name('items.history');

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes (from Breeze)
require __DIR__.'/auth.php';