<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'category'])
            ->where('status', 'active');

        if ($request->has('category')) {
            $query->where('product_category_id', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->latest()->paginate(12);

        return response()->json($products);
    }

    public function show(Product $product)
    {
        return response()->json($product->load(['seller', 'category']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'quantity_available' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'minimum_order_quantity' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'price_on_request' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $product = $request->user()->products()->create([
            'product_category_id' => $request->product_category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
            'quantity_available' => $request->quantity_available,
            'unit' => $request->unit,
            'minimum_order_quantity' => $request->minimum_order_quantity,
            'location' => $request->location,
            'price' => $request->price,
            'price_on_request' => $request->price_on_request ?? false,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json($product->load(['category']), 201);
    }

    public function update(Request $request, Product $product)
    {
        // Ensure user owns the product
        if ($product->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'product_category_id' => 'sometimes|exists:product_categories,id',
            'name' => 'sometimes|string|max:255',
            'quantity_available' => 'sometimes|numeric|min:0',
            'unit' => 'sometimes|string|max:50',
            'minimum_order_quantity' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'price_on_request' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $product->update($request->only([
            'product_category_id',
            'name',
            'quantity_available',
            'unit',
            'minimum_order_quantity',
            'location',
            'price',
            'price_on_request',
            'description',
        ]));

        return response()->json($product->load(['category']));
    }

    public function destroy(Product $product, Request $request)
    {
        // Ensure user owns the product
        if ($product->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function categories()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return response()->json($categories);
    }
}
