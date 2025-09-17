<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebHook extends Controller
{
    public function handleWebHook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('stripe.webhook_secret')
            );
        } catch (SignatureVerificationException $e) {
            return response('Invalid webhook signature.', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                Order::create([
                    'stripe_session_id' => $session->id,
                    'user_id' => $session->metadata['user_id'],
                    'amount' => $session->amount_total,
                    'status' => 'paid',
                ]);
                break;
        }
        return response('Webhook received.', 200);
    }
}