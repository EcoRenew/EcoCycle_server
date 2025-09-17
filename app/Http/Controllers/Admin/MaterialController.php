<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Material::with('category');

        // Optional search by material_name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('material_name', 'like', "%{$searchTerm}%");
        }

        // Sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = (int) $request->input('per_page', 10);
        $materials = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    /**
     * Store a newly created material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_name' => 'required|string|max:255',
            'price_per_unit' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,item',
            'category_id' => 'required|exists:categories,category_id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $material = Material::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Material created',
            'data' => $material->load('category'),
        ], 201);
    }

    /**
     * Display the specified material.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $material = Material::with('category')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $material,
        ]);
    }

    /**
     * Update the specified material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'material_name' => 'sometimes|required|string|max:255',
            'price_per_unit' => 'sometimes|required|numeric|min:0',
            'unit' => 'sometimes|required|in:kg,item',
            'category_id' => 'sometimes|required|exists:categories,category_id',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $material->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Material updated',
            'data' => $material->load('category'),
        ]);
    }

    /**
     * Remove the specified material from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Material deleted',
        ], 200);
    }
}