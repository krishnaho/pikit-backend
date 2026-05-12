<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalUserCount = User::count();
        $today = Carbon::today();
        $totalRestaurants = Restaurant::all();
        $orders = Order::with('user', 'restaurant', 'orderstatus')
            ->whereDate('created_at', $today)
            ->get();
        $totalSales = Order::where('order_status_id', 7)->sum('total');


        return view('admin.dashboard.dashboard', [
            'totalUserCount' => $totalUserCount,
            'orders' => $orders,
            'totalSales' => $totalSales,
            'totalRestaurants' => $totalRestaurants,
        ]);
    }
}
