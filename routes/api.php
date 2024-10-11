<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShippingAdreesController;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('/users', UserController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/enabledSeller', [UserController::class, 'enabledSeller']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products' ,[ProductController::class, 'index']);
    
    Route::post('/addToCart', [CartController::class, 'addToCart']);
    Route::put('/removeFromCart/{cartIndex}', [CartController::class, 'removeFromCart']);
    Route::get('/showCart', [CartController::class , 'showCart']);
    
    Route::apiResource('/orders', OrderController::class);
    Route::get('/prepareOrder', [OrderController::class, 'prepareOrder']);
    //Route::post('/orders', [OrderController::class, 'store']);

    Route::apiResource('/addresses', ShippingAdreesController::class);

    Route::apiResource('/seller', SellerController::class);

    Route::apiResource('/offer', OfferController::class);

    Route::apiResource('/review', ReviewController::class);
});


Route::get('/debug', function(){

    $cartKeys = Redis::keys('laravel_database_cart:*');
    return response()->json($cartKeys);
});
