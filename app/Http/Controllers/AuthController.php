<?php

namespace App\Http\Controllers;

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

        return response()->json(['message' => "User created successfully"], 201);
    }
    public function login(Request $request)
    {
        $users = User::where('email', $request->input('email'))->first();

        if (!$users || !Hash::check($request->password, $users->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $users->createToken('Token');
        return response()->json([
            'user' => $users,
            "token" => $token->plainTextToken
        ]);
    }
}
