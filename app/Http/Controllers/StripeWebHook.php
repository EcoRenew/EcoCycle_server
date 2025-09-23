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
        Log::info('Webhook received.');

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
            Log::error('Stripe webhook signature verification failed.', ['error' => $e->getMessage()]);
            return response('Invalid webhook signature.', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                
                try {
                    $userId = $session->metadata['user_id'] ?? null;
                    if (!$userId) {
                        Log::error('Stripe webhook checkout.session.completed event is missing user_id in metadata.', ['session_id' => $session->id]);
                        return response('Missing user_id metadata.', 400);
                    }

                    $amountInDollars = $session->amount_total / 100;

                    $order = Order::create([
                        'stripe_session_id' => $session->id,
                        'user_id' => $userId,
                        'amount' => $amountInDollars,
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
                    Log::info('Sending order paid email to: ' . $user->email);
                    Mail::to($user->email)->send(new OrderPaid($order));

                } catch (\Exception $e) {
                    Log::error('Error processing Stripe checkout.session.completed webhook.', ['error' => $e->getMessage(), 'session_id' => $session->id]);
                    return response('Error processing webhook.', 500);
                }
                
                break;
        }

        return response('Webhook received.', 200);
    }
}
