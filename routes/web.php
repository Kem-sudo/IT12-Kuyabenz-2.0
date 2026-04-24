<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/transactions', [AdminController::class, 'transactionMonitor'])->name('admin.transactions');

        Route::get('/sales', [AdminController::class, 'salesReport'])->name('admin.sales');
        Route::post('/sales/download', [AdminController::class, 'downloadSalesReport'])->name('admin.sales.download');
        Route::get('/sales/realtime', [AdminController::class, 'getRealTimeSales'])->name('admin.sales.realtime');

        Route::get('/users', [UserController::class, 'index'])->name('admin.users');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::post('/users/{user}/change-password', [UserController::class, 'updatePassword'])->name('admin.users.change-password');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        Route::get('/menu', [MenuItemController::class, 'index'])->name('admin.menu');
        Route::post('/menu', [MenuItemController::class, 'store'])->name('admin.menu.store');
        Route::put('/menu/{menuItem}', [MenuItemController::class, 'update'])->name('admin.menu.update');
        Route::delete('/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('admin.menu.destroy');
        Route::get('/menu/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('admin.menu.edit');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs');
    });


    /*
    |--------------------------------------------------------------------------
    | 🔐 ADMIN VALIDATION (FIXED - NOW ACCESSIBLE BY CASHIER)
    |--------------------------------------------------------------------------
    */
    Route::post('/admin/validate', [CashierController::class, 'validateAdmin'])
        ->name('admin.validate');


    /*
    |--------------------------------------------------------------------------
    | CASHIER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('cashier')->middleware(['role:cashier'])->group(function () {

        Route::get('/', [CashierController::class, 'index'])
            ->name('cashier.pos');

        Route::post('/order', [CashierController::class, 'processOrder'])
            ->name('cashier.process-order');

        Route::get('/receipt/{order}', [CashierController::class, 'showReceipt'])
            ->name('cashier.receipt');

        /*
        |--------------------------------------------------------------------------
        | REMOVE ITEM (WITH ADMIN APPROVAL)
        |--------------------------------------------------------------------------
        */
        Route::post('/remove-item', [CashierController::class, 'removeItemFromOrder'])
            ->name('cashier.remove-item');

        /*
        |--------------------------------------------------------------------------
        | DELETE ORDER (OPTIONAL)
        |--------------------------------------------------------------------------
        */
        Route::post('/delete-order', [CashierController::class, 'deleteOrder'])
            ->name('cashier.delete-order');
    });


    /*
    |--------------------------------------------------------------------------
    | KITCHEN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('kitchen')->middleware(['role:kitchen'])->group(function () {

        Route::get('/', [KitchenController::class, 'index'])
            ->name('kitchen.display');

        Route::post('/orders/{order}/start', [KitchenController::class, 'startPreparing'])
            ->name('kitchen.start-preparing');

        Route::post('/orders/{order}/complete', [KitchenController::class, 'completeOrder'])
            ->name('kitchen.complete-order');
    });

});


/*
|--------------------------------------------------------------------------
| DEFAULT REDIRECT
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login');