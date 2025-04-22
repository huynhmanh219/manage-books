<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $role = Role::where('name','user')->first();
        $user->roles()->attach($role);
        $token = $user->createToken("Token")->accessToken;
        return response()->json(['user' => $user,"token"=>$token], 201);
    }

    public function login(Request $request)
    {
    $user = User::where('email', $request->email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Tài khoản hoặc mật khẩu không đúng!'
        ], 401);
    }

    $token = $user->createToken('MyAppToken')->accessToken;

    return response()->json([
        'token' => $token
    ]);
}
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->token()->revoke();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['message' => 'No valid token provided'], 401);
    }
}
