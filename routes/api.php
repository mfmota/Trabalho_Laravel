<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\CreateUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\OrderController;   
use App\Http\Controllers\OrderItemController;

Route::post('/login', [AuthController::class,'authenticate'])->name('login');

Route::middleware('auth:sanctum')->group(function(){

    Route::post('/users', [CreateUserController::class, 'store']);

    Route::post('/logout', [AuthController::class,'logout'])->name('logout');

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('products', ProductController::class);

    Route::group(['prefix' => 'orders'], function () {
        Route::apiResource('/', OrderController::class)->except(['update']);

        Route::put('/{order}/send', [OrderController::class, 'send']);
        Route::put('/{order}/finish', [OrderController::class, 'finish']);

        Route::post('/{order}/items', [OrderItemController::class, 'store']);
        Route::delete('/{order}/items/{item}', [OrderItemController::class, 'destroy']);
    
    });
});