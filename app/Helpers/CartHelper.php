<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;
class CartHelper{
    public static function totalCart($userId){
        $cartKey = "cart:{$userId}";
        $cart = json_decode(Redis::get($cartKey));                
        $total = 0;
        foreach($cart as $item){
            $total += $item->unit_price * $item->quantity;
        }
        return $total;
    }
}