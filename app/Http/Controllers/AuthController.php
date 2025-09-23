<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $attrs = $request->validated();
        $user = User::create($attrs);
        event(new Registered($user));
        return response()->json([
            "message" => "User registered successfully. Please check your email to verify your account.",
            "user" => new UserResource($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required | email | exists:users',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => "The provided credentials are incorrect."
            ], 401);
        }
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Your email is not verified.',
                'email_not_verified' => true
            ], 403);
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

    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->user_id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }
}
