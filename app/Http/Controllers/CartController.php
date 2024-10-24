<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Helpers\CartHelper;
use Illuminate\Support\Facades\Redis;

class CartController extends Controller
{   
    protected $cartService;
    
    public function __construct(CartService $cartService){
        $this->cartService = $cartService;
    }

    public function addToCart(Request $request){        
        $cart = $this->cartService->addToCart($request->user()->id, $request->productId, 1);
        
        return response()->json([
            'message' => 'producto agregado al carrito :D',            
            'cart' => $cart,
        ]);
    }

    public function removeFromCart(Request $request, $index){        
        $cart = $this->cartService->removeFromCart($index, $request->user()->id);

        return response()->json([
            'message' => $cart != null ? 'cantidad disminuido' : 'carrito borrado',
            'cart' => $cart
        ]);
    }
    public function showCart(Request $request){
        $cart = $this->cartService->showCart($request->user()->id);
        return response()->json([
            'total_to_pay' => CartHelper::totalCart($request->user()->id),
            'cart' => $cart
        ]);
    }
}
