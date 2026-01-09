<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([
                'products' => []
            ]);
        }

        $loweredQuery = strtolower($query);

        // Optimized Global Product Search (Case-Insensitive)
        $products = Product::query()
            ->with(['category:id,name'])
            ->where(function (Builder $q) use ($loweredQuery) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$loweredQuery}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$loweredQuery}%"])
                  ->orWhereHas('category', function (Builder $cq) use ($loweredQuery) {
                      $cq->whereRaw('LOWER(name) LIKE ?', ["%{$loweredQuery}%"]);
                  });
            })
            ->limit(10)
            ->get(['id', 'name', 'price', 'product_category_id', 'description']); // Corrected category_id to product_category_id

        return response()->json([
            'products' => $products
        ]);
    }
}
