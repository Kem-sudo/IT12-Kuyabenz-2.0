<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\OrderController;

Route::middleware('auth:sanctum')->group(function () {
    // Menu routes
    Route::get('/menu-items', [MenuController::class, 'index']);
    Route::get('/menu-items/{menuItem}', [MenuController::class, 'show']);
    
    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});