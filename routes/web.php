<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;   

Route::get('/test-redis', function () {
    try {
        Redis::set('test_key', 'testing_redis_connection');
        $value = Redis::get('test_key');

        return response()->json([
            'message' => 'Conexión a Redis exitosa!',
            'data' => $value,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error de conexión a Redis',
            'error' => $e->getMessage(),
        ]);
    }
});


Route::get('/', function () {
    return view('welcome');
});
