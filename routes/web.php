<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuItemController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Simple auth routes without role middleware (temporary)
Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/transactions', [AdminController::class, 'transactionMonitor'])->name('admin.transactions');
        Route::get('/sales', [AdminController::class, 'salesReport'])->name('admin.sales');
        Route::post('/sales/download', [AdminController::class, 'downloadSalesReport'])->name('admin.sales.download'); // Add this line
        Route::get('/users', [UserController::class, 'index'])->name('admin.users');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        
        Route::get('/menu', [MenuItemController::class, 'index'])->name('admin.menu');
        Route::post('/menu', [MenuItemController::class, 'store'])->name('admin.menu.store');
        Route::put('/menu/{menuItem}', [MenuItemController::class, 'update'])->name('admin.menu.update');
        Route::delete('/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('admin.menu.destroy');
    });

    // Cashier Routes
    Route::prefix('cashier')->group(function () {
        Route::get('/', [CashierController::class, 'index'])->name('cashier.pos');
        Route::post('/order', [CashierController::class, 'processOrder'])->name('cashier.process-order');
        Route::get('/receipt/{order}', [CashierController::class, 'showReceipt'])->name('cashier.receipt');
    });

    // Kitchen Routes
    Route::prefix('kitchen')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('kitchen.display');
        Route::post('/orders/{order}/start', [KitchenController::class, 'startPreparing'])->name('kitchen.start-preparing');
        Route::post('/orders/{order}/complete', [KitchenController::class, 'completeOrder'])->name('kitchen.complete-order');
    });
});

Route::redirect('/', '/login');