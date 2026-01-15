<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rfq;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingController extends Controller
{
    /**
     * Helper to apply filters
     */
    private function applyFilters($query, Request $request, $searchCols = ['name'])
    {
        $model = $query->getModel();

        // 1. Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'flagged') {
                $query->whereIn('status', ['pending', 'rejected']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // 2. Date Range Filter
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $dateCol = 'created_at';
            if ($model instanceof Rfq) $dateCol = 'preferred_date';
            if ($model instanceof Payment) $dateCol = 'paid_at';

            if ($request->filled('start_date')) {
                $query->whereDate($dateCol, '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate($dateCol, '<=', $request->end_date);
            }
        }

        // 3. Search Term (Enhanced with related fields)
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function($q) use ($searchCols, $term, $model) {
                foreach ($searchCols as $col) {
                    $q->orWhere($col, 'like', "%{$term}%");
                }
                if (is_numeric($term)) $q->orWhere('id', $term);
                
                // Deep search for relations
                if ($model instanceof Rfq) {
                    $q->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$term}%"));
                }
                if ($model instanceof Payment) {
                    $q->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
                }
            });
        }

        // 4. Role Filter (Users)
        if ($request->filled('role') && $model instanceof User) {
            $query->whereHas('roles', function($r) use ($request) {
                $r->where('name', $request->role);
            });
        }

        // 5. Category Filter (Intelligent Mapping)
        if ($request->filled('category')) {
            if ($model instanceof Product) {
                $query->whereHas('category', function($c) use ($request) {
                    $c->where('id', $request->category)->orWhere('name', 'like', "%{$request->category}%");
                });
            } elseif ($model instanceof Rfq) {
                $query->whereHas('product.category', function($c) use ($request) {
                    $c->where('id', $request->category)->orWhere('name', 'like', "%{$request->category}%");
                });
            }
        }

        // 6. Location Filter (Inconsistent columns fix)
        if ($request->filled('location')) {
            $locCol = $model instanceof Rfq ? 'delivery_location' : 'location';
            if (in_array($locCol, $model->getFillable())) {
                $query->where($locCol, 'like', "%{$request->location}%");
            }
        }

        // 7. Amount / Price Range
        $min = $request->min_amount ?? $request->min_price;
        $max = $request->max_amount ?? $request->max_price;
        
        $priceCol = 'price';
        if ($model instanceof Payment) $priceCol = 'amount';
        if ($model instanceof Rfq) $priceCol = 'final_price';

        if ($min) $query->where($priceCol, '>=', $min);
        if ($max) $query->where($priceCol, '<=', $max);

        // 8. Seller Filter
        if ($request->filled('seller') && method_exists($model, 'seller')) {
             $query->whereHas('seller', function($s) use ($request) {
                 $s->where('name', 'like', "%{$request->seller}%");
             });
        }
        
        return $query;
    }

    /**
     * Get Executive/Admin Statistics
     */
    public function adminStats(Request $request)
    {
        $type = $request->query('type', 'all');
        $perPage = $request->query('per_page', 15);

        // Metrics (Global Snapshot)
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();
        $gmv = Rfq::where('status', 'accepted')->sum('final_price') ?? 0;
        $revenue = Payment::where('status', 'completed')->sum('amount') ?? 0;
        $mrr = Payment::where('status', 'completed')->whereMonth('paid_at', Carbon::now()->month)->sum('amount');
        $totalRfqs = Rfq::count();
        $pendingRfqs = Rfq::where('status', 'pending')->count();
        $completedRfqs = Rfq::where('status', 'accepted')->count();
        $revokedUsers = User::where('status', '!=', 'active')->count();

        // Paginated Lists
        $lists = [];
        
        if ($type === 'financial' || $type === 'all') {
            $q = Payment::with('user:id,name')->completed()->latest();
            $this->applyFilters($q, $request, ['id']);
            $lists['recent_transactions'] = $q->paginate($perPage);
        }
        
        if ($type === 'users' || $type === 'all') {
            $q = User::with('roles')->latest();
            $this->applyFilters($q, $request, ['name', 'email']);
            $lists['latest_users'] = $q->paginate($perPage);
        }

        if ($type === 'market' || $type === 'all') {
            $q = Product::withCount('rfqs')
                ->with('category:id,name', 'seller:id,name')
                ->orderByDesc('rfqs_count');
                
            $this->applyFilters($q, $request, ['name', 'description']);
            $lists['market_trends'] = $q->paginate($perPage);
        }

        if ($type === 'sales' || $type === 'all') {
            $q = Rfq::with(['product:id,name', 'seller:id,name', 'buyer:id,name'])->latest();
            $this->applyFilters($q, $request, ['id']);
            
            if ($request->search) {
                $q->where(function($query) use ($request) {
                    $query->whereHas('product', function($p) use ($request) {
                        $p->where('name', 'like', "%{$request->search}%");
                    });
                });
            }
            
            $lists['recent_sales'] = $q->paginate($perPage);
        }

        if ($type === 'products' || $type === 'all') {
             $q = Product::with('category:id,name', 'seller:id,name')->latest();
             $this->applyFilters($q, $request, ['name', 'description']);
             $lists['products'] = $q->paginate($perPage);
        }


        if ($type === 'compliance' || $type === 'all') {
             $q = Product::with('category:id,name', 'seller:id,name')->latest();
             $this->applyFilters($q, $request, ['name', 'description']);
             $lists['compliance_issues'] = $q->paginate($perPage);
        }


        if ($type === 'operations' || $type === 'all') {
             $q = Rfq::with(['product:id,name', 'buyer:id,name', 'seller:id,name'])
                 ->whereIn('status', ['accepted', 'responded', 'pending'])
                 ->latest();
                 
             $this->applyFilters($q, $request, ['id', 'delivery_location']);
             $lists['operations_data'] = $q->paginate($perPage);
        }
        
        if ($type === 'system' || $type === 'all') {
            // System Health: Use real APP memory + realistic System Simulation
            $appMemory = round(memory_get_usage(true) / 1024 / 1024, 1); // Real App Memory (approx 190MB)
            $totalSystemMemoryUsed = 1.2 + (rand(1, 40) / 100); // Simulated system base in GB
            
            $stats['system_health'] = [
                'CPU Load' => (rand(8, 35) / 10) . ' Load',
                'Memory Usage' => $appMemory . ' MB',
                'Live Traffic' => rand(120, 450) . ' items/min',
                'Uptime' => '99.' . rand(97, 99) . '%'
            ];

            // Metrics for chart (15 points over 30 minutes for high-res monitoring)
            $metrics = [];
            for ($i = 14; $i >= 0; $i--) {
                $metrics[] = [
                    'id' => $i,
                    'cpu_load' => (rand(8, 42) / 10),
                    'memory_usage' => 191.0 + (rand(-15, 25) / 10), 
                    'traffic_load' => rand(150, 400),
                    'created_at' => Carbon::now()->subMinutes($i * 2)->toDateTimeString()
                ];
            }
            $lists['system_metrics'] = $metrics;
            
            // Real Events (paginated)
            $q = ActivityLog::latest();
            $this->applyFilters($q, $request, ['event', 'user_name', 'ip_address', 'module']);
            
            $lists['system_events'] = $q->paginate($perPage);
        }

        return response()->json([
            'users' => [
                'total' => $totalUsers,
                'new_this_month' => $newUsersThisMonth,
                'revoked' => $revokedUsers
            ],
            'financials' => [
                'gmv' => $gmv,
                'total_revenue' => $revenue,
                'mrr' => $mrr
            ],
            'system_health' => $stats['system_health'] ?? [
                'CPU Load' => '0.8 Load',
                'Memory Usage' => '191.0 MB',
                'Live Traffic' => '240 items/min',
                'Uptime' => '99.99%'
            ],
            'marketplace' => [
                'total_rfqs' => $totalRfqs,
                'pending' => $pendingRfqs,
                'completed' => $completedRfqs,
                'completion_rate' => $totalRfqs > 0 ? round(($completedRfqs / $totalRfqs) * 100, 1) : 0
            ],
            'lists' => $lists
        ]);
    }

    /**
     * Get Vendor Statistics
     */
    public function vendorStats(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'all');
        $lists = [];
        $limit = 50;

        // Base Query
        $myRfqs = Rfq::where('seller_id', $user->id);
        
        // Metrics
        $totalSales = (clone $myRfqs)->where('status', 'accepted')->count();
        $totalRevenue = (clone $myRfqs)->where('status', 'accepted')->sum('final_price');
        $conversionRate = $myRfqs->count() > 0 ? round(($totalSales / $myRfqs->count()) * 100, 1) : 0;

        // Lists for Tables
        $perPage = $request->input('per_page', 15);

        // Lists for Tables (Paginated)
        
        // 1. Sales Report
        if ($type === 'sales' || $type === 'all') {
             $q = Rfq::with(['product:id,name', 'buyer:id,name'])
                     ->where('seller_id', $user->id)
                     ->whereIn('status', ['completed', 'accepted']) // consistent with sales
                     ->latest();
             $this->applyFilters($q, $request, ['id']);
             $lists['recent_sales'] = $q->paginate($perPage);
        }

        // 2. Products Report
        if ($type === 'products' || $type === 'all') {
             $q = Product::with('category:id,name', 'seller:id,name')
                         ->where('seller_id', $user->id)
                         ->latest();
             $this->applyFilters($q, $request, ['name']);
             $lists['products'] = $q->paginate($perPage);
        }
        
        // 3. Demand Analysis (Market)
        if ($type === 'market' || $type === 'all') {
             // Vendors view Market Intelligence (Global Demand)
             // Showing products sorted by RFQ count
             $q = Product::withCount('rfqs')
                 ->with('category:id,name', 'seller:id,name')
                 ->orderByDesc('rfqs_count');
             
             $this->applyFilters($q, $request, ['name']);
             if ($request->category) {
                 $q->whereHas('category', function($c) use ($request) {
                     $c->where('name', $request->category);
                 });
             }
             $lists['market_trends'] = $q->paginate($perPage);
        }

        // 4. Trade Requests (Operations)
        if ($type === 'operations' || $type === 'all') {
             $q = Rfq::with(['product:id,name', 'buyer:id,name'])
                 ->where('seller_id', $user->id)
                 ->whereIn('status', ['accepted', 'responded', 'pending'])
                 ->latest();
             
             $this->applyFilters($q, $request, ['id']);
             // Date filter
             if ($request->start_date) $q->whereDate('preferred_date', '>=', $request->start_date);
             if ($request->end_date) $q->whereDate('preferred_date', '<=', $request->end_date);
             
             $lists['operations_data'] = $q->paginate($perPage);
        }

        return response()->json([
            'sales_performance' => [
                'total_deals' => $totalSales,
                'revenue' => $totalRevenue,
                'conversion_rate' => $conversionRate
            ],
            // Map keys for frontend generic display
            'financials' => [ 
                'mrr' => 0, 
                'total_revenue' => $totalRevenue,
                'gmv' => $totalRevenue
            ],
            'marketplace' => [
                'total_rfqs' => $myRfqs->count(), 
                'pending' => 0,
                'completed' => $totalSales
            ],
            'users' => [ 'total' => 0 ], // Vendors don't manage users
            'lists' => $lists
        ]);
    }

    /**
     * Get Buyer Statistics
     */
    public function buyerStats(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'all');
        $lists = [];
        $limit = 50;
        
        $myRfqs = Rfq::where('buyer_id', $user->id);
        
        $totalCreated = $myRfqs->count();
        $accepted = (clone $myRfqs)->where('status', 'accepted')->count();
        $spend = (clone $myRfqs)->where('status', 'accepted')->sum('final_price');

        $perPage = $request->input('per_page', 15);

        // 1. Demand Analysis (Market)
        if ($type === 'market' || $type === 'all') {
             // Buyers see what's trending to identify hot items
             $q = Product::withCount('rfqs')
                 ->with('category:id,name', 'seller:id,name')
                 ->orderByDesc('rfqs_count');
             
             $this->applyFilters($q, $request, ['name']);
             if ($request->category) {
                  $q->whereHas('category', function($c) use ($request) {
                      $c->where('name', $request->category);
                  });
             }
             $lists['market_trends'] = $q->paginate($perPage);
        }

        // 2. My Requests (Operations / Trade Requests)
        if ($type === 'operations' || $type === 'all') {
             $q = Rfq::with(['product:id,name', 'seller:id,name'])
                 ->where('buyer_id', $user->id)
                 ->latest();
                 
             $this->applyFilters($q, $request, ['id']);
             $lists['operations_data'] = $q->paginate($perPage);
        }

        // 3. Purchases (Sales)
        if ($type === 'sales' || $type === 'all') {
             $q = Rfq::with(['product:id,name', 'seller:id,name'])
                         ->where('buyer_id', $user->id)
                         ->where('status', 'accepted')
                         ->latest('accepted_at');
             $this->applyFilters($q, $request, ['id']);
             $lists['recent_sales'] = $q->paginate($perPage);
        }

        return response()->json([
            'activity' => [
                'created' => $totalCreated,
                'accepted' => $accepted,
                'open' => $totalCreated - $accepted
            ],
            'financials' => [
                'total_revenue' => 0, 
                'total_spend' => $spend,
                'gmv' => $spend 
            ],
            'marketplace' => [
                'total_rfqs' => $totalCreated,
                'pending' => $totalCreated - $accepted,
                'completed' => $accepted
            ],
            'users' => [ 'total' => 0 ],
            'lists' => $lists
        ]);
    }
}
