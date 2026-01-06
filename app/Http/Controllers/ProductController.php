<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with(['seller']);
        
        if (request()->has('own')) {
            $query->where('seller_id', auth()->id());
        } else {
            $query->where('status', 'available');
        }
        
        $products = $query->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
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
            'category' => $request->category,
            'unit' => $request->unit ?: 'kg',
            'min_order' => $request->min_order ?: 1,
            'price' => $request->price,
            'location' => $request->location,
            'image' => $request->image,
            'status' => $request->status ?: 'pending',
        ]);

        return response()->json($product, 201);
    }

    public function show(string $id)
    {
        $product = Product::where('id', $id)->with('seller')->firstOrFail();
        return response()->json($product);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
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

        $product->update($request->only(['name', 'description', 'category', 'unit', 'min_order', 'price', 'location', 'image', 'status']));

        return response()->json($product);
    }

    public function destroy(string $id)
    {
        $product = Product::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
