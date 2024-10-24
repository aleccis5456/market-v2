<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Offer;
use Illuminate\Support\Facades\Redis;

class CartService
{
    public function addToCart($userId, $productId, $quantity = 1)
    {
        $cartKey = "cart:{$userId}";
        $cart = json_decode(Redis::get($cartKey)) ?? [];        
        $product = Product::find($productId);
        $offer = Offer::where('product_id', $productId)->first();        
        $currentPrice = $product->price;

        if ($offer != null && $offer->active == true) {
            if ($offer->discount_type == 'percentage') {
                $discountAmount = $product->price * ($offer->discount_value / 100);
                $currentPrice -= $discountAmount; //Descuento porcentual
            } elseif ($offer->discount_type == 'fixed') {
                $currentPrice -= $offer->discount_value; //Descuento fijo
            }
        }            
        $productInCart = false;
        foreach ($cart as &$item) {                     
            if($item->quantity < $product->stock){
                if ($item->product_id == $product->id) {
                    $productInCart = true;
                    $item->quantity += $quantity;
                    break;
                }
            }else{
                return response()->json([
                    'message' => 'el producto no tiene sufiente stock'
                ]);
            }            
        }        

        if ($productInCart == false) {
            $cart[] = [
                'user_id' => $userId,
                'product_id' => $productId,
                'unit_price' => $currentPrice,                
                'name' => $product->name,
                'quantity' => 1,
                'product_detail' => [
                    'product' => $product,
                    'offer' => $offer ?? 'producto sin oferta',
                ],
            ];
        }
        Redis::set($cartKey, json_encode($cart));     
        
        return $cart;
    }

    public function removeFromCart($index, $userId)
    {
        $cartKey = "cart:{$userId}";
        $cart = json_decode(Redis::get($cartKey), true);        
        if(!$cart){
            return response()->json(['message' => 'No se pudo recuperar el carrito'], 500);
        }

        if (isset($cart[$index])) {
            if ($cart[$index]['quantity'] > 1) {
                $cart[$index]['quantity']--;
            } else {
                unset($cart[$index]);
            }
        }
        Redis::set($cartKey, json_encode($cart));

        return $cart;
    }

    public function showCart($userId)
    {       
        $cartKey = "cart:{$userId}";         
        $cart = Redis::get($cartKey);    
        $cart = json_decode($cart, true);        
        return $cart;
    }
    
}
