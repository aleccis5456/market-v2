<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request){        
        $seller = Seller::where('user_id',$request->user()->id)->first();        
        if(!$seller){
            return response()->json([
                'message' => 'El usuario no tiene cuenta de vendedor'
            ]);
        }
        $sellerId = $seller->id;
        $products = Product::with('orderProducts')
                            ->where('seller_id', $sellerId)
                            ->get();        
                                
        return response()->json($products) ;
    }    
}
