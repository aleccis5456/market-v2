<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['product', 'user'])
                        ->where('user_id', $request->user()->id);
        return response()->json($reviews, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error en la validacion',
                'errors' => $validator->errors(),
            ]);
        }
        $orders = Order::where('user_id', $request->user()->id)->get();        
        if (count($orders) >= 1){                   
            foreach($orders as $order){
                $product = OrderProduct::where('order_id', $order->id)
                                            ->where('product_id', $request->product_id)->first();                
                if(!is_null($product)){
                    break;
                }else{
                    return response()->json(['message' => 'no cuenta un pedido con ese producto'], 400);
                }
            }
        }else{
            return response()->json(['message' => 'para calificar el user tiene que tener un pedido con el producto'],400);
        }                

        try {
            $review = Review::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'message' => 'review creada',
                'review' => $review
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error al crear la review',
                'errors' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(String $id){
        $review = Review::find($id);
        if(!$review){
            return response()->json(['message' => 'review not found'], 404);
        }
        return response()->json($review, 200);
    }
    
    public function update(Request $request, String $id){
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes'
        ]);        
        if($validator->fails()){
            return response()->json([
                'message' => 'error al validar',
                'errors' => $validator        
            ], 400);
        }
        
        $review = Review::find($id);
        $review->update($request->all());

        return response()->json([
            'message' => 'review actualizado',
            'error' => $review,
        ]);
    }

    public function destroy(String $id){
        $review = Review::destroy($id);
        if(!$review){
            return response()->json(['message' => 'review not found'], 404);
        }

        return response()->json(['message' => 'review eliminado']);
    }
}
