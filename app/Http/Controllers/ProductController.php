<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'category']);
        
        // Asset Classification Filtering
        if ($request->has('category') && $request->category != '') {
            $query->where('product_category_id', $request->category);
        }

        // Search Protocol
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Advanced Multi-Dimensional Filters (For Marketplace)
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->has('location') && $request->location != '') {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Ownership Logic vs Public Discovery (Sanctum aware)
        $user = auth('sanctum')->user();
        
        if ($request->has('own') && $request->own == 1 && $user) {
            // Private Inventory: Show only vendor's own products
            $query->where('seller_id', $user->id);
        } else {
            // Global Marketplace: Show all available products
            $query->where(function ($q) use ($user) {
                $q->where('status', 'available');
                
                // Vendors also see their own products (even if pending/rejected) in the global list
                if ($user && $user->role === 'vendor') {
                    $q->orWhere('seller_id', $user->id);
                }
            });
        }
        
        $products = $query->orderBy('created_at', 'desc')->get();


        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'unit' => 'nullable|string',
            'min_order' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric',
            'location' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'nullable|string|in:available,unavailable,pending,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'seller_id' => auth()->id(),
            'product_category_id' => $request->product_category_id,
            'unit' => $request->unit ?: 'kg',
            'min_order' => $request->min_order ?: 1,
            'price' => $request->price,
            'location' => $request->location,
            'images' => $request->images,
            'status' => $request->status ?: 'pending',
        ]);

        return response()->json($product, 201);
    }

    public function show(string $id)
    {
        $product = Product::where('id', $id)->with(['seller', 'category'])->firstOrFail();
        return response()->json($product);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'unit' => 'nullable|string',
            'min_order' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric',
            'location' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'nullable|string|in:available,unavailable,pending,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update($request->only(['name', 'description', 'product_category_id', 'unit', 'min_order', 'price', 'location', 'images', 'status']));

        return response()->json($product);
    }

    public function destroy(string $id)
    {
        $product = Product::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
