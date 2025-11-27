<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        foreach ($categories as $index => $category) {
            foreach ($category->products as $product) {
                $product->image_url = $product->firstImage?->image ? asset('storage/uploads/products/' . $product->firstImage->image)
                    :  asset('storage/uploads/products/default-product.png');
            }
        }

        // Sản phẩm bán chạy nhất
            $bestSellingProducts = Product::selectRaw("products.*,
                (SELECT COALESCE(SUM(oi.quantity), 0)
                 FROM order_items oi
                 JOIN product_variants pv ON pv.id = oi.product_variant_id
                 WHERE pv.product_id = products.id) as total_sold")
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get();

           

        //Thêm ảnh nếu ảnh chưa có thì lấy ảnh default trong thư mục
        foreach ($bestSellingProducts as $product) {
            $product->image_url = $product->firstImage?->image ? asset('storage/uploads/products/' . $product->firstImage->image)
                : asset('storage/uploads/products/default-product.png');
        }

        return view('clients.pages.home', compact('categories', 'bestSellingProducts'));
    }
}
