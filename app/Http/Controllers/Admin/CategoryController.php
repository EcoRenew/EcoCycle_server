<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parentCategory')
            ->orderBy('category_name')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255', Rule::unique('categories', 'category_name')],
            'parent_category_id' => ['nullable', 'exists:categories,category_id'],
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created',
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = Category::with(['parentCategory', 'childCategories'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'category_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'category_name')->ignore($category->category_id, 'category_id'),
            ],
            'parent_category_id' => ['nullable', 'different:category_id', 'exists:categories,category_id'],
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->childCategories()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with child categories',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted'
        ]);
    }
}


