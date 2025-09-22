<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $cart = Cart::with('cartProducts.product')
                ->firstOrCreate(['user_id' => $request->user()->user_id]);

            return response()->json([
                'success' => true,
                'data' => $cart
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($request->product_id);

            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available',
                ], 400);
            }

            $cart = Cart::firstOrCreate(['user_id' => $request->user()->user_id]);

            $cartProduct = CartProduct::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartProduct) {
                $newQuantity = $cartProduct->quantity + $request->quantity;

                if ($newQuantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock available for this quantity',
                    ], 400);
                }

                $cartProduct->quantity = $newQuantity;
                $cartProduct->save();
            } else {
                $cartProduct = CartProduct::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'data' => $cartProduct
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $cart = Cart::where('user_id', $request->user()->user_id)->firstOrFail();

            $cartProduct = CartProduct::where('cart_id', $cart->id)
                ->where('id', $id)
                ->firstOrFail();

            $product = Product::findOrFail($cartProduct->product_id);

            $newQuantity = $request->quantity;

            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available',
                ], 400);
            }

            $cartProduct->update(['quantity' => $newQuantity]);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'data' => $cartProduct
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function destroy(Request $request, $id)
    {
         try {
            $cart = Cart::where('user_id', $request->user()->user_id)->firstOrFail();

            $cartProduct = CartProduct::where('cart_id', $cart->id)->findOrFail($id);
            $cartProduct->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function buyWithPoints(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            }

            $cart = Cart::with('cartProducts.product')->where('user_id', $user->user_id)->first();
            if (!$cart || $cart->cartProducts->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Cart is empty.'], 400);
            }

            $totalPoints = 0;
            $items = [];
            foreach ($cart->cartProducts as $cartProduct) {
                $product = $cartProduct->product;
                if ($product->stock < $cartProduct->quantity) {
                    return response()->json(['success' => false, 'message' => "Not enough stock for {$product->name}."], 400);
                }
                $pointsPrice = $product->points_price ?? 0;
                $totalPoints += $pointsPrice * $cartProduct->quantity;
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $cartProduct->quantity,
                    'price' => 0, 
                ];
            }

            if ($user->recycling_points < $totalPoints) {
                return response()->json(['success' => false, 'message' => 'Not enough points to complete this purchase.'], 402);
            }

            $user->recycling_points -= $totalPoints;
            $user->save();

            foreach ($cart->cartProducts as $cartProduct) {
                $product = $cartProduct->product;
                $product->decrement('stock', $cartProduct->quantity);
                $cartProduct->delete(); 
            }

            $order = Order::create([
                'user_id' => $user->user_id,
                'status' => 'paid',
                'amount' => 0,
                'stripe_session_id' => null
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => 0,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Purchase successful!',
                'order' => $order->load('orderItems'),
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during the purchase.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
