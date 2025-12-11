<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::where('role_id', 3)->latest()->limit(3)->get();
        $categories = Category::with('products')->get();
        $products = Product::where('status', 'in_stock')->get();
        $orders = Order::with('shippingAddress')->latest()->get();

        // Lấy top 3 sản phẩm bán chạy nhất
        $topSellingProducts = Product::select(
                'products.*',
                DB::raw('SUM(COALESCE(order_items.quantity, 0)) as total_sold')
            )
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            
            ->groupBy('products.id')
            ->orderByRaw('SUM(COALESCE(order_items.quantity, 0)) DESC')
            ->take(3)
            ->get();
        // Doanh thu theo tháng (6 tháng gần nhất)
        $monthlyRevenues = Order::select(
            DB::raw('SUM(total_price) as revenue'),
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->where('status', '!=', 'cancelled') // Không tính đơn cáncelled
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();
        

        return view('admin.pages.dashboard', compact('users', 'categories', 'products', 'orders', 'topSellingProducts', 'monthlyRevenues'));
    }
}
