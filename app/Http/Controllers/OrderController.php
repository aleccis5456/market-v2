<?php

namespace App\Http\Controllers;

use App\Models\ShippingAdress;
use App\Models\Order;
use App\Services\OrderService;
use App\Helpers\CartHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService){
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     */    
    public function index(Request $request)
    {
        $user_id = $request->user()->id;         
        $orders = Order::with('shippingAdress')
                                ->where('user_id', $user_id)
                                ->get();
        return response()->json([
            'user_orders' => [
                'orders' => $orders,            
            ],
        ],200);
    }
    /**
     *prepareOrder muestra los detalles para un supuesto formulario
     */
    public function prepareOrder(Request $request){
        $userId = $request->user()->id;
        $cartKey = "cart:{$userId}";
        $cart = json_decode(Redis::get($cartKey)); 
        $total = CartHelper::totalCart($userId);

        $addresses = ShippingAdress::where('user_id', $userId)->get();        
        if(!$cart){
            return response()->json(['message' => 'No se pudo recuperar datos del carrito'],404);
        }
        if(count($addresses) <= 0){
            return response()->json(['message' => 'el usuario no tiene direccion/es registrada'],404);
        }

        return response()->json([            
            'total_to_pay' => $total,
            'products' => count($cart),
            'cart' => $cart,
            'addresses' => $addresses,
        ],200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {                       
        $response = $this->orderService->store($request->user()->id, $request->address_id);
        return $response;        
    }   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('shippingAdress')->find($id);
        if(!$order){            
            return response()->json(['message' => 'no se pudo recuperar datos de la orden'], 404);
        }

        return response()->json([
            'order' => $order
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);
        
        $order = Order::find($id);
        $order->update($request->all());                
        
        return response()->json([
            'message' => 'orden actualizado',
            'order' => $order,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::destroy($id);
        if(!$order){
            return response()->json([
                'message' => 'NO se pudo borrar',
                'error' =>  'not found'
            ], 404);
        }
        return response()->json(['message' => 'la orden se borro'], 200);
    }
}
