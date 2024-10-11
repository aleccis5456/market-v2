<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offer = Offer::with('product')->get();
        dd($offer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {        
        $request->validate([            
            'product_id' => 'required|string|exists:products,id', 	
            'type' => 'required|string', 	
            'value' => 'required|numeric', 	
            'start' => 'nullable|date', 	
            'end' => 'nullable|date'
        ]);

        try{
            $offer = Offer::create([
                'product_id' => $request->product_id, 	
                'discount_type' => $request->type, 	
                'discount_value' => $request->value, 	
                'start_date' => $request->start ?? null, 	
                'end_date' => $request->end ?? null, 	
                'active' => true,
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }        

        return response()->json([
            'message' => 'oferta creada',
            'offer' => $offer
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_id' => 'sometimes|string|exists:products,id', 	
            'type' => 'sometimes|string', 	
            'value' => 'sometimes|numeric', 	
            'start' => 'sometimes|date', 	
            'end' => 'sometimes|date'
        ]);

        $offer = Offer::find($id);
        $offer->update($request->all());

        return response()->json([
            'message' => 'actualizado correctamente',
            'offer' => $offer
        ]);
    }

    public function desactivateExpiredOffers(){

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }
}
