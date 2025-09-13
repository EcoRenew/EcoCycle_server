<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('materials')->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'img' => 'required|string',
            'hover_img' => 'required|string',
            'stock' => 'required|integer|min:0',
            'materials' => 'required|array',
            'materials.*.material_id' => 'required|exists:materials,material_id',
            'materials.*.quantity' => 'required|numeric|min:1'
        ]);
        $product = Product::create($request->only('name', 'description', 'img', 'hover_img', "stock"));
        foreach ($request->materials as $mat) {
            $product->materials()->attach($mat['material_id'], ['quantity' => $mat['quantity']]);
        }
        return response()->json($product->load('materials'), 201);
    }
    public function produce(Request $request, Product $product)
    {
        $request->validate([
            'amount' => 'required|integer|min:1'
        ]);
        $amount = $request->amount;
        foreach ($product->materials as $mat) {
            $needed = $mat->pivot->quantity * $amount;
            if ($mat->stock < $needed) {
                return response()->json([
                    'error' => "Not enough {$mat->material_name}. Need {$needed}, available {$mat->stock}"
                ], 400);
            }
        }
        foreach ($product->materials as $material) {
            $needed = $material->pivot->quantity * $amount;
            $material->decrement('stock', $needed);
        }
        $product->increment('stock', $amount);
        return response()->json($product->load('materials'));
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
