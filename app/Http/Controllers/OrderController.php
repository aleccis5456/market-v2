<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Helpers\CartHelper;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
        $orders = Order::where('user_id', $user_id)->get();

        return response()->json([
            $orders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $response = $this->orderService->store($request->user()->id);
        //return $response;
        
    }   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
