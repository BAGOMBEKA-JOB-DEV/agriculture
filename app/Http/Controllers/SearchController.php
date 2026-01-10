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
        $user = auth('sanctum')->user();
        $products = Product::query()
            ->with(['category:id,name'])
            ->where(function (Builder $q) use ($user) {
                $q->where('status', 'available');
                if ($user && ($user->role === 'vendor' || $user->role === 'admin' || $user->role === 'super_admin')) {
                    $q->orWhere('seller_id', $user->id);
                }
            })
            ->where(function (Builder $q) use ($loweredQuery) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$loweredQuery}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$loweredQuery}%"])
                  ->orWhereHas('category', function (Builder $cq) use ($loweredQuery) {
                      $cq->whereRaw('LOWER(name) LIKE ?', ["%{$loweredQuery}%"]);
                  });
            })
            ->limit(10)
            ->get(['id', 'name', 'price', 'product_category_id', 'description', 'status']);

        return response()->json([
            'products' => $products
        ]);
    }
}
