<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Return authenticated user's addresses
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $addresses = Address::where('user_id', $userId)->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    /**
     * Store a new address for authenticated user
     */
    public function store(Request $request)
    {
        $request->validate([
            'street' => 'required|string|max:500',
            'city' => 'nullable|string|max:255'
        ]);

        $address = Address::create([
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'data' => $address
        ], 201);
    }
}
