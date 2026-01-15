<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    /**
     * Get list of Vendors.
     */
    public function vendors(Request $request)
    {
        if (!$request->user()->hasPermission('view_vendors')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = User::whereHas('roles', function ($q) {
            $q->where('slug', 'vendor');
        })->with('sellerProfile');

        $this->applyFilters($query, $request);

        return response()->json($query->paginate(12));
    }

    public function traders(Request $request)
    {
        if (!$request->user()->hasPermission('view_traders')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = User::whereHas('roles', function ($q) {
            $q->where('slug', 'trader');
        })->with('sellerProfile');

        $this->applyFilters($query, $request);

        return response()->json($query->paginate(12));
    }

    public function buyers(Request $request)
    {
        if (!$request->user()->hasPermission('view_buyers')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = User::whereHas('roles', function ($q) {
            $q->where('slug', 'buyer');
        })->with('buyerProfile');

        $this->applyFilters($query, $request);

        return response()->json($query->paginate(12));
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->search) {
            $searchTerm = strtolower($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        if ($request->location) {
            $locationTerm = strtolower($request->location);
            $query->whereRaw('LOWER(location) LIKE ?', ["%{$locationTerm}%"]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->verified === 'yes') {
            $query->whereHas('sellerProfile', function($q) {
                $q->where('is_verified', true);
            });
        } elseif ($request->verified === 'no') {
            $query->where(function($q) {
                $q->whereHas('sellerProfile', function($subQ) {
                    $subQ->where('is_verified', false);
                })->orWhereDoesntHave('sellerProfile');
            });
        }
    }
}
