<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShippingAdreesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use App\Services\OrderService;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('/users', UserController::class);
    Route::post('/enabledSeller', [UserController::class, 'enabledSeller']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products' ,[ProductController::class, 'index']);
    
    Route::post('/addToCart', [CartController::class, 'addToCart']);
    Route::put('/removeFromCart/{cartIndex}', [CartController::class, 'removeFromCart']);
    Route::get('/showCart', [CartController::class , 'showCart']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::post('/adreeses', [ShippingAdreesController::class, 'store']);
});


Route::get('/debug', function(){

    $cartKeys = Redis::keys('laravel_database_cart:*');
    return response()->json($cartKeys);
});

Route::post('/debug2', [OrderService::class, 'store']);