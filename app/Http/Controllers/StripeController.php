<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
{
    public $stripe;
    public function __construct()
    {
        $this->stripe = new StripeClient(
            config('stripe.api_key.secret')
        );
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        $lineItems = [];
        foreach ($validated['products'] as $item) {
            $product = Product::find($item['id']);
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'EGP',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            $session = $this->stripe->checkout->sessions->create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $user->email,
                'success_url' => config('stripe.frontend_url') . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('stripe.frontend_url') . '/?canceled=true',
                'metadata' => [
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                ],
            ]);
            return response()->json([
                'id' => $session->id,
                'url' => $session->url,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unexpected error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
