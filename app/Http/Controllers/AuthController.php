<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users',
            'password' => 'string|min:6',
        ]);        

        if($validator->fails()){
            return response()->json([
                'message' => 'error', 
                'errors' => $validator->errors()
            ], 400); 
        }        

        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return response()->json([
                'message' => 'user created',
                'user' => $user,
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'error',
                'errors' => $e->getMessage(),
            ],400);
        }        
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [            
            'email' => 'required|email|string',
            'password' => 'string|min:6',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'error', 
                'errors' => $validator->errors()
            ], 400); 
        }
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json([
                'message' => 'el email no esta registrado'
            ], 404);
        }
        
        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'contaseña incorrecta' 
            ], 400);
        }

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'message' => 'user loged',
                'user' => $user,
                'token' => $token,
                'toke_type' => 'Bearer',
            ],200);
        }else{
            return response()->json('error');
        }
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' =>  'loged out successfully'], 200);
    }
}
