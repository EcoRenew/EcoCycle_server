<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $attrs = $request->validated();
        $user = User::create($attrs);
        $token = $user->createToken($request->email);
        return response()->json([
            "message" => "User registered successfully.",
            "user" => new UserResource($user),
            "token" => $token->plainTextToken
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required | email | exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'message' => "the provided credentials are incorrect"
            ];
        }
        $token = $user->createToken($user->name);
        return response()->json([
            "message" => "User logged in successfully.",
            "user" => new UserResource($user),
            "token" => $token->plainTextToken
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'You have been successfully logged out.'
        ], 200);
    }
}
