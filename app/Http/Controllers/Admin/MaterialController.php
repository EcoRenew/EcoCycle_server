<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials.
     */
    public function index(Request $request)
    {
        $query = Material::with('category');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('material_name', 'like', "%{$searchTerm}%");
        }

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
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_name'  => 'required|string|max:255',
            'price_per_unit' => 'required|numeric|min:0',
            // accept both old 'unit' and new 'default_unit' for backward compatibility
            'default_unit'   => 'nullable|string|max:50',
            'unit'           => 'nullable|string|max:50',
            'units'          => 'nullable|array',
            'units.*'        => 'string',
            'image_url'      => 'nullable|url',
            'category_id'    => 'required|exists:categories,category_id',
            'points'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // normalize old 'unit' -> 'default_unit'
        if (! empty($data['unit']) && empty($data['default_unit'])) {
            $data['default_unit'] = $data['unit'];
        }
        unset($data['unit']);

        $material = Material::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Material created',
            'data' => $material->load('category'),
        ], 201);
    }

    /**
     * Display the specified material.
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
     */
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'material_name'  => 'sometimes|required|string|max:255',
            'price_per_unit' => 'sometimes|required|numeric|min:0',
            'default_unit'   => 'nullable|string|max:50',
            'unit'           => 'nullable|string|max:50',
            'units'          => 'nullable|array',
            'units.*'        => 'string',
            'image_url'      => 'nullable|url',
            'category_id'    => 'sometimes|required|exists:categories,category_id',
            'points'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (! empty($data['unit']) && empty($data['default_unit'])) {
            $data['default_unit'] = $data['unit'];
        }
        unset($data['unit']);

        $material->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Material updated',
            'data' => $material->load('category'),
        ]);
    }

    /**
     * Remove the specified material from storage.
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
