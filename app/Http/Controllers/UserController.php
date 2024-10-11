<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }

    public function update(Request $request, string $id){
        $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|string|email|unique:users',
            'password' => 'sometimes|string|confirmed|min:6',
        ]);

        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'user not found'], 404);            
        }        
        $user->update($request->all());
        return response()->json([
            'message' => 'user updated',
            'user' => $user,
        ]);
    }    
    
    public function enabledSeller(Request $request){       
        $user = User::find($request->user()->id);
        $user->is_seller = 1;
        $user->save();

        $seller = Seller::create([
            'user_id' 	=> $request->user()->id,
            'store_name' => $request->user()->name, 
            'store_description' => null, 	
            'logo' => null,
        ]);

        return response()->json([           
            'more details' => 'el usuario ahora es vendedor(seller)',
            'user' => $user,
        ]);
    }

    public function desabledSeller(Request $request){        
        $user = User::find($request->user()->id);

        $user->is_seller = 0;
        $user->save();

        return response()->json([
            'messsge' => 'el usuario ya no es vendedor',
            'user' => $user,
        ]);
    }
}
