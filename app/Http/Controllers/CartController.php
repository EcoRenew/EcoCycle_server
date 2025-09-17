<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::with('cartProducts.product')
            ->firstOrCreate(['user_id' => $request->user()->user_id]);

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    public function store(Request $request)
    {
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
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cart = Cart::where('user_id', $request->user()->user_id)->firstOrFail();

            $cartProduct = CartProduct::where('cart_id', $cart->id)
                ->where('id', $id)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }

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
    }
    public function destroy(Request $request, $id)
    {
        $cart = Cart::where('user_id', $request->user()->user_id)->firstOrFail();

        $cartProduct = CartProduct::where('cart_id', $cart->id)->findOrFail($id);
        $cartProduct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }
}
