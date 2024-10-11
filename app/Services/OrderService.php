<?php

namespace App\Services;

use App\Models\User;
use App\Models\OrderProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAdress;
use App\Helpers\CartHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderService
{
    public function store($userId, $addreesId)
    {
        $cartKey = "cart:{$userId}";
        $cart = json_decode(Redis::get($cartKey));
        $total = CartHelper::totalCart($userId);
        $datac = [];

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'usuario no exite'], 404);
        }
        $address = ShippingAdress::find($addreesId);
        if (!$address) {
            return response()->json([
                'message' => 'el usuario no tiene una direccion de envio o se cargo mal el id de la direccion',
                'redirect' => 'http://127.0.0.1:8000/api/adreeses'
            ], 404);
        }

        try{
            DB::beginTransaction();
            $order = Order::create([
                'code' => $this->setCode(),
                'user_id' => $userId,
                'shipping_address_id' => $addreesId,
                'total' => $total,
                'status' => 'Pending',
            ]);
            foreach ($cart as $item) {
                $product = Product::find($item->product_id);
                if (!$product) {
                    return response()->json([
                        'message' => 'producto no encontrado'
                    ]);
                }                
                if ($item->quantity > $product->stock) {
                    return response()->json([
                        'message' => 'el producto no tiene sufiente stock'
                    ]);
                }

                $orderProduct = OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                ]);

                $datac[] = [
                    'orders' => $order,
                    'orderProducts' => $orderProduct,
                ];

                $product->stock -= $item->quantity;
                $product->save();
            }
            DB::commit();
            $data = json_decode(json_encode($datac));
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'error en el al crear orden',
                'errors' => $e->getMessage(),
            ]);
        }
    }

    public function setCode()
    {
        $strings = '0123456789qazwsxedcrfvtgbyhnujmikolp';
        $code = '';
        for ($i = 0; $i <= 8; $i++) {
            $code .= $strings[rand(0, strlen($strings)) - 1];
        }
        return $code;
    }
}