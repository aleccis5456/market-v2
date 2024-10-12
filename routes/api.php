<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
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
    //Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/enabled2fa', [AuthController::class, 'enabled2fa']);
    Route::post('/desable2fa', [AuthController::class, 'desable2fa']);

    //User
    Route::apiResource('/users', UserController::class);    
    Route::post('/enabledSeller', [UserController::class, 'enabledSeller']);

    //products
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products' ,[ProductController::class, 'index']);
    
    //cart
    Route::post('/addToCart', [CartController::class, 'addToCart']);
    Route::put('/removeFromCart/{cartIndex}', [CartController::class, 'removeFromCart']);
    Route::get('/showCart', [CartController::class , 'showCart']);
    
    //orders
    Route::apiResource('/orders', OrderController::class);
    Route::get('/prepareOrder', [OrderController::class, 'prepareOrder']);    

    //addresses
    Route::apiResource('/addresses', ShippingAdreesController::class);

    //seller
    Route::apiResource('/seller', SellerController::class);

    //offer
    Route::apiResource('/offer', OfferController::class);

    //reviews
    Route::apiResource('/review', ReviewController::class);    
});


Route::get('/debug', function(){

    $cartKeys = Redis::keys('laravel_database_cart:*');
    return response()->json($cartKeys);
});
