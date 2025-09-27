<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripePaymentService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.api_key.secret'));
    }

    /**
     * Create Stripe checkout session and order (without decrementing stock)
     */
    public function createCheckoutSession(array $validated, User $user)
    {
        DB::beginTransaction();

        try {
            $lineItems = [];
            $totalAmount = 0;
            $productsData = [];

            foreach ($validated['products'] as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if (!$product) {
                    throw new Exception("Product ID {$item['id']} not found.");
                }

                // Check stck availability but don't decrement yet
                if ($product->stock < $item['quantity']) {
                    throw new Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}");
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'EGP',
                        'product_data' => ['name' => $product->name],
                        'unit_amount' => $product->price * 100,
                    ],
                    'quantity' => $item['quantity'],
                ];

                $totalAmount += $product->price * $item['quantity'];
                $productsData[] = ['product' => $product, 'quantity' => $item['quantity']];
            }

            $session = $this->stripe->checkout->sessions->create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $user->email,
                'success_url' => config('stripe.frontend_url') . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('stripe.frontend_url') . '/payment-cancel?session_id={CHECKOUT_SESSION_ID}',
                'metadata' => ['user_id' => $user->user_id],
                'payment_intent_data' => ['capture_method' => 'automatic'],
            ]);

            $order = Order::create([
                'stripe_session_id' => $session->id,
                'user_id' => $user->user_id,
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            foreach ($productsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->price,
                ]);
            }

            DB::commit();

            return [
                'id' => $session->id,
                'url' => $session->url,
                'order_id' => $order->id,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment creation error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Handle successful payment (decrement stock here)
     */
    public function handlePaymentSuccess(string $sessionId)
    {
        $session = $this->stripe->checkout->sessions->retrieve($sessionId);
        $userId = $session->metadata['user_id'] ?? null;

        if (!$userId) {
            throw new Exception('User authentication information missing');
        }

        if ($session->payment_status === 'paid') {
            $order = Order::where('stripe_session_id', $session->id)
                         ->with('orderItems.product')
                         ->first();

            if (!$order) {
                throw new Exception('Order not found for this session');
            }

            if ($order->status === 'pending') {
                DB::transaction(function () use ($order, $userId) {
                    foreach ($order->orderItems as $orderItem) {
                        $product = $orderItem->product;
                        
                        if ($product->stock < $orderItem->quantity) {
                            throw new Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock}, Required: {$orderItem->quantity}");
                        }
                        
                        $product->stock -= $orderItem->quantity;
                        $product->save();
                    }

                    $order->update([
                        'status' => 'paid',
                        'user_id' => $userId,
                    ]);

                    $this->emptyUserCart($userId);

                    Log::info('Payment confirmed and stock updated successfully', [
                        'user_id' => $userId,
                        'order_id' => $order->id,
                        'amount' => $order->amount,
                    ]);
                });
            }

            return $order->fresh();
        }

        throw new Exception('Payment not completed. Status: ' . $session->payment_status);
    }

    public function handlePaymentCancel(string $sessionId)
    {
        $order = Order::where('stripe_session_id', $sessionId)
                     ->where('status', 'pending')
                     ->first();

        if ($order) {
            $order->update(['status' => 'cancelled']);
            return true;
        }

        throw new Exception('Order not found or already processed');
    }

    /**
     * Empty cart after payment success
     */
    private function emptyUserCart($userId)
    {
        $cart = Cart::where('user_id', $userId)->first();
        if ($cart) {
            $cart->cartProducts()->delete();
            Log::info('Cart emptied successfully', ['user_id' => $userId]);
        }
    }
}