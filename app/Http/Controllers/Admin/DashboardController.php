<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Request as RecyclingRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalRequests = RecyclingRequest::count();
        $pendingRequests = RecyclingRequest::where('status', 'Pending')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'total_products' => $totalProducts,
                'total_requests' => $totalRequests,
                'pending_requests' => $pendingRequests,
            ],
        ]);
    }

    public function activities()
    {
        $recentRequests = RecyclingRequest::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'recent_requests' => $recentRequests,
            ],
        ]);
    }
}


