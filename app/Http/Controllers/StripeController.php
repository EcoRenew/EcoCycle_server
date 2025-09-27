<?php

namespace App\Http\Controllers;

use App\Services\StripePaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $result = $this->stripeService->createCheckoutSession($validated, auth()->user());

            return response()->json($result, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $order = $this->stripeService->handlePaymentSuccess($validated['session_id']);

            return response()->json([
                'message' => 'Payment confirmed successfully and cart emptied',
                'order' => $order,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error confirming payment: ' . $e->getMessage()], 500);
        }
    }

    public function paymentCancel(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $this->stripeService->handlePaymentCancel($validated['session_id']);

            return response()->json(['message' => 'Order cancelled successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error cancelling order: ' . $e->getMessage()], 500);
        }
    }
}
