<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserCartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('authentication')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    // You can add more auth routes here
    // Route::post('/register', [AuthController::class, 'register']);
    // Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('products')->group(function () {
    Route::get('/fetchProducts', [ProductController::class, 'fetchProducts']);
});

Route::middleware('auth:sanctum')->prefix('checkout')->group(function () {
    Route::middleware('auth:sanctum')->get('/fetchUserCart', [UserCartController::class, 'fetchUserCart']);
    Route::middleware('auth:sanctum')->post('/addToCart', [UserCartController::class, 'addToCart']);
    Route::middleware('auth:sanctum')->post('/changeUserProductQty', [UserCartController::class, 'changeUserProductQty']);
});

Route::middleware('auth:sanctum')->prefix('order')->group(function () {
    Route::middleware('auth:sanctum')->post('/createOrder', [OrderController::class, 'createOrder']);
    Route::middleware('auth:sanctum')->get('/fetchOrders', [OrderController::class, 'fetchOrders']);
    Route::middleware('auth:sanctum')->post('/cancelOrder', [OrderController::class, 'cancelOrder']);
});
