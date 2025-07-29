<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas (con rate limiting específico para login)
Route::middleware(['throttle:login'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas de productos públicas (solo lectura)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Rutas protegidas
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // User routes
    Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');
    Route::post('/users', [UserController::class, 'store'])->middleware('role:admin');
    Route::get('/users/{id}', [UserController::class, 'show'])->middleware('role:admin,cliente');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('role:admin,cliente');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('role:admin');

    // Product routes (admin y empleado)
    Route::middleware('role:admin,empleado')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    // 🔥 Favorites routes (todos los usuarios autenticados)
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/', [FavoriteController::class, 'store']);
        Route::delete('/{productId}', [FavoriteController::class, 'destroy']);
        Route::get('/check/{productId}', [FavoriteController::class, 'check']);
        Route::get('/count', [FavoriteController::class, 'count']);
    });

    // 🔥 Cart routes (todos los usuarios autenticados)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'store']);
        Route::put('/{id}', [CartController::class, 'update']);
        Route::delete('/{id}', [CartController::class, 'destroy']);
        Route::delete('/', [CartController::class, 'clear']);
        Route::get('/count', [CartController::class, 'count']);
    });

    // 🔥 Orders routes - CORREGIDO
    Route::prefix('orders')->group(function () {
        // Rutas para usuarios regulares
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/cancel', [OrderController::class, 'cancel']);
    });

    // 🔥 Admin Orders routes - SEPARADO Y CORREGIDO
    Route::prefix('admin')->middleware('role:admin,empleado')->group(function () {
        Route::get('/orders', [OrderController::class, 'adminIndex']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    });
});
