<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PhoneNumberController extends Controller
{
    /**
     * Return authenticated user's phone numbers (single phone stored on user model)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $phones = [];
        if ($user->phone) {
            $phones[] = [
                'id' => $user->user_id,
                'number' => $user->phone,
            ];
        }

        return response()->json(['success' => true, 'data' => $phones]);
    }

    /**
     * Store (or update) a phone number on the authenticated user
     */
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        // make sure $user is an Eloquent model instance before calling save()
        if ($user instanceof User) {
            $user->phone = $request->input('number');
            $user->save();
        }

        return response()->json(['success' => true, 'data' => [
            'id' => $user->user_id,
            'number' => $user->phone
        ]], 201);
    }
}
