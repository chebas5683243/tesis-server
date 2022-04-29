<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('jwt', ['except' => ['login', 'register']]);
    }

    public function register(Request $request){
        $user = new User([
            'email' => $request->email,
            'password' => $request->password
        ]);
        // $user->password = bcrypt($user->password);
        $user->save();
        $token = JWTAuth::fromUser($user);
        return $this->respondWithToken($token,$user);
    }

    public function login(){
        $credentials = request(['email', 'password']);

        $user = User::with('company')->where('email',$credentials['email'])->where('password',$credentials['password'])->first();

        $can_login = $user && $user->estado && $user->company->estado;

        if($can_login) $token = JWTAuth::fromUser($user);
        else return response()->json(['error' => 'Unauthorized']);
        
        return $this->respondWithToken($token,$user);
    }

    public function me(){
        return response()->json(auth()->user());
    }

    public function logout(){
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(){
        return $this->respondWithToken(auth()->refresh());
    }
    
    public function validateUser(){
        $credentials = request(['email', 'password']);

        $user = User::where('email',$credentials['email'])->where('password',$credentials['password'])->first();

        if($user) return response()->json(['validation' => true]);
        else return response()->json(['validation' => false]);
    }

    protected function respondWithToken($token,$user){
        return response()->json([
            'access_token' => $token,
            'usuario' => $user
        ]);
    }
}
