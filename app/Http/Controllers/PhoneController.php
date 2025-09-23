<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class PhoneController extends Controller
{
    /**
     * Return all phones for authenticated user.
     */
    public function index(Request $request)
    {
         /** @var User|null $user */
        $user = Auth::user();
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $phones = $user->phones()->orderByDesc('is_primary')->orderByDesc('created_at')->get();

        return response()->json(['data' => $phones]);
    }

    /**
     * Create a new phone for authenticated user.
     */
    public function store(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();

        // If user already has same phone, return it
        $existing = $user->phones()->where('phone', $payload['phone'])->first();
        if ($existing) {
            // toggle primary if requested
            if (! empty($payload['is_primary']) && ! $existing->is_primary) {
                $user->phones()->update(['is_primary' => false]);
                $existing->is_primary = true;
                $existing->save();
            }

            return response()->json(['data' => $existing], 200);
        }

        // If marking new as primary, clear others
        if (! empty($payload['is_primary'])) {
            $user->phones()->update(['is_primary' => false]);
        }

        // If user has no phones, make the first one primary
        $isPrimary = $payload['is_primary'] ?? ($user->phones()->count() === 0);

        $phone = $user->phones()->create([
            'phone' => $payload['phone'],
            'is_primary' => (bool) $isPrimary,
        ]);

        return response()->json(['data' => $phone], 201);
    
    }
}