<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

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
            'otp' => 'nullable|string'
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
            //verificamos si tiene auth 2fa            
            if($user->is_2fa_enabled == true){                
                $google2fa = app(Google2FA::class);                
                if(!$google2fa->verifyKey($user->code_2fa, $request->otp )){
                    return response()->json(['message' => 'codigo invalido']);
                }
            }   
            if($user->is_seller == true){
                $sellerToken = $user->createToken('seller_token', ['create', 'update','delete'])->plainTextToken;                
            }else{
                $UserToken = $user->createToken('user_token')->plainTextToken;
            }

            //$token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'message' => 'user loged',
                'user' => $user,
                'token' => $UserToken ?? $sellerToken,
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
    public function enabled2fa(Request $request){
        $user = Auth::user();
        $google2fa = new Google2FA;

        $code = $google2fa->generateSecretKey();

        $user->is_2fa_enabled = true;
        $user->code_2fa = $code;
        $user->save();

        $QRimage = $google2fa->getQRCodeUrl(
            'Market-v2 | autenticacion de dos factores',
            $user->email,
            $code,
        );

        return response()->json([
            'QRimage' => $QRimage,
            'code2FA' => $code,
        ], 201);        
    }    

    public function desable2fa(Request $request){
        $user = Auth::user();

        $user->is_2fa_enabled = false;
        $user->code_2fa = null;
        $user->save();

        return response()->json([
            'message' => 'autenticacion de dos factores eliminado'
        ], 200);
    }
}
