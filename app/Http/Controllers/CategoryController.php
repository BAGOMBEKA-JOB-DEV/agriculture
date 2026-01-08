<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_categories',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = ProductCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:product_categories,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update($request->only(['name', 'description', 'is_active']));

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);
        
        // Prevent deletion if products exist
        if ($category->products()->count() > 0) {
            return response()->json(['message' => 'Cannot delete category with associated products.'], 422);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
