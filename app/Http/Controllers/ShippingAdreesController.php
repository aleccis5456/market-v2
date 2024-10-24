<?php

namespace App\Http\Controllers;

use App\Models\ShippingAdress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingAdreesController extends Controller
{
    public function index(Request $request){
        $adrees = ShippingAdress::where('user_id', $request->user()->id)->first();
        if(!$adrees){
            return response()->json([
                'message' => 'sin direcciones de envio',                
            ]);
        }

        return response()->json($adrees);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [            
            'state' => 'required|string',
            'city' => 'required|string',
            'street' => 'required|string'
        ]);        

        if($validator->fails()){
            return response()->json([
                'message' => 'error en la validacion de direccion (linea 22)',
                'errors' => $validator->errors(),
            ]);
        }

        $adress = ShippingAdress::create([
            'user_id' => $request->user()->id,
            'state' => $request->state,
            'city' => $request->city,
            'street' => $request->street,
        ]);

        return response()->json([
            'message' => 'direccion agregada',
            'adrees' => $adress,
        ]);
    }
}
