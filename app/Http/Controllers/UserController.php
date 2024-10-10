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
            'messsge' => 'ahora el usuario es un vendedor',
            'details' => 'Ahora tienes acceso como vendedor. Configura tu perfil para comenzar a vender',
            'more details' => 'Tu perfil de vendedor ha sido configurado automáticamente con tus datos de usuario',
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
