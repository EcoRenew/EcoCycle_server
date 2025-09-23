<?php

namespace App\Http\Controllers;

use App\Mail\OrderPaid;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

class StripeWebHook extends Controller
{
    /**
     * Handle Stripe webhook events.
     */
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

        // Handle the specific event type.
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // Create a new order in your database.
                $order = Order::create([
                    'stripe_session_id' => $session->id,
                    'user_id' => $session->metadata->user_id,
                    'amount' => $session->amount_total,
                    'status' => 'paid',
                ]);

                Stripe::setApiKey(config('stripe.api_key.secret'));
                $stripe = new StripeClient(config('stripe.api_key.secret'));
                $line_items = $stripe->checkout->sessions->allLineItems($session->id, ['limit' => 100]);

                foreach ($line_items as $item) {
                    $stripe_product_id = $item->price->product;
                    
                    $product = Product::where('stripe_product_id', $stripe_product_id)->first();

                    if ($product && $product->stock >= $item->quantity) {
                        $product->stock -= $item->quantity;
                        $product->save();
                    }
                }

                $user = $order->user;
                Mail::to($user->email)->send(new OrderPaid($order));
                
                break;
        }

        // Return a 200 response to acknowledge receipt of the event.
        return response('Webhook received.', 200);
    }
}
