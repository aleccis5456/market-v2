<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orderBy = $request->query('orderBy');
        $column = $request->query('column') ?? 'created_at';
        $priceMin = $request->query('priceMin');
        $priceMax = $request->query('priceMax');
        $search = $request->query('search');
        $query = Product::query();

        if($orderBy == 'asc'){
            $query->orderBy($column,$orderBy);
            $cantidad = $query->count();
        }else{
            $query->orderByDesc($column);
            $cantidad = $query->count();
        }

        if(!is_null($priceMin)){
            $query->where('price', '>=',$priceMin);
            $cantidad = $query->count();
        }

        if(!is_null($priceMax)){
            $query->where('price', '<=',$priceMax);
            $cantidad = $query->count();
        }

        if(!is_null($search)){
            $query->whereLike('name', "%$search%")
                  ->orWhereLike('price', "%$search%");
            $cantidad = $query->count();
        }
        
        $products = $query->with('seller')
                          ->paginate(8)                          
                          ->appends([
                            'orderBy' => $orderBy, 
                            'column' => $column,
                            'priceMin' => $priceMin, 
                            'priceMax' => $priceMax,
                            'search' => $search,
                           ]);

        return response()->json([
            'cantidad' => $cantidad ?? null,
            'products' => $products,            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable|image',
            'description' => 'nullable',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);           

        if($validator->fails()){
            return response()->json([
                'massage' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        if($request->hasFile('image')){
            $imagePath = $request->file('image');
            $imageName = time(). '.'.$imagePath->getClientOriginalExtension();
            $destinationPath = public_path('images/products');
            $imagePath->move($destinationPath,$imageName);
        }

        $code = $this->getCode(8);        
        $slug = Str::slug($request->name);        
        $seller = Seller::where('user_id', $request->user()->id)->first();
        if(!$seller){
            return response()->json([
                'message' => 'primero habilita tu perfil de vendedor'
            ]);
        }

        try{
            $product = Product::create([
                'seller_id' => $seller->id,
                'code' => $code,
                'slug' => $slug,
                'name' => $request->name, 	
                'image' => $imageName ?? '', 	
                'description' => $request->description, 	
                'price' => $request->price, 	
                'stock' => $request->stock
            ]);

            return response()->json([
                'message' => 'producto creado',
                'producto' => $product,
            ], 201);
            
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error al crear el prducto',
                'errors' => $e->getMessage(),
            ], 400);
        }
        
    }
    
    public function getCode($cantidad){
        $code = '123456789qwertyuioplkjhgfdsazxcvbnm';
        $setCode = '';        
        for($i = 0; $i < $cantidad ; $i++ ){
            $setCode .= $code[rand(0, strlen($code) - 1)];
        }
        return $setCode;
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('seller')->find($id);
        if(!$product){
            return response()->json([
                'message' => 'product not found'
            ]);
        }

        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'stock' => 'sometimes|numeric',
            'imgme' => 'sometimes|image'
        ]);

        if(!$validator->fails()){
            return response()->json([
                'message' => 'error en la validacion del request',
                'errors' => $validator->errors(),
            ]);
        }

        $product = Product::find($id);
        if(!$product){
            return response()->json(['message' => 'el producto no existe']);
        }
        
        if($request->hasFile('image')){
            $imagePath = $request->file('image');
            $imageNameUpdated = time(). '.'.$imagePath->getClientOriginalExtension();
            $destination = public_path('images/products');
            $imagePath->move($destination, $imageNameUpdated);

            if(File::exists($product->image)){
                File::delete($product->image);
            }
        }                

        $product->name = $request->name ?? $product->name;
        $product->description = $request->description ?? $product->description;
        $product->stock = $request->stock ?? $product->stock;
        $product->price = $request->price ?? $product->price;
        $product->image = $imageNameUpdated ?? $product->image;

        $product->save();

        return response()->json([
            'message' => 'producto actualizado',
            'producto' => $product
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::destroy($id);
        if(!$product){
            return response()->json([
                'message' => 'producto borrado'
            ]);
        }
    }
}
