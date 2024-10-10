<?php 

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAdress;
use App\Models\Offer;
use App\Helpers\CartHelper;
use Illuminate\Support\Facades\Redis;

class OrderService{
    public function store($userId){
        $cartKey = "cart:{$userId}";
        $cart = json_decode(Redis::get($cartKey));
        $total = CartHelper::totalCart($userId);        
        //dd($cart);
        foreach($cart as $item){
            $product = Product::find($item->product_id);
            if(!$product){
                return response()->json([
                    'message' => 'producto no encontrado (linea 17)'
                ]);
            }
            if($item->quantity > $product->stock){
                return response()->json([
                    'message' => 'el producto no tiene sufiente stock'
                ]);
            }
            
            $shippingAdress = ShippingAdress::where('user_id', $userId)->first();                                    
            if($shippingAdress != null){
                $adrees[] = [
                    'dapartamento' => $shippingAdress->state,
                    'ciudad' => $shippingAdress->city,
                    'calle' => $shippingAdress->street,
                ];                
                $order = Order::create([
                    'user_id' => $userId,
                    'shipping_address_id' => $adrees, 	
                    'total' => $total, 	
                    'status' => 'Pending',
                ]);

            }else{
                return response()->json([
                    'message' => 'el usuario no tiene una direccion de envio',
                    'redirect' => 'http://127.0.0.1:8000/api/adreeses'
                ]);
            }
                        
        }
    }

    public function code(){
        $strings = '123456789qazwsxedcrfvtgbyhnujmikolp';
        for($i = 0; $i <= 8 ; $i++ ){

        }
    }
}