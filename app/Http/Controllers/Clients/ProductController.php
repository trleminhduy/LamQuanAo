<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();

        $products = Product::with('firstImage')->where('status', 'in_stock')->paginate(9);
        


        //Thêm ảnh nếu ảnh chưa có thì lấy ảnh default trong thư mục
       
        /** @var Product $product */
        foreach ($products as $product) {
            $product->image_url = $product->firstImage?->image ? asset('storage/uploads/products/' . $product->firstImage->image)
                : asset('storage/uploads/products/default-product.png');
        }

        return view('clients.pages.products', compact('categories', 'products'));
    }

    public function filter(Request $request)
    {
        $query = Product::with('firstImage')->where('status', 'in_stock');

        //Lọc danh mục nếu tồn tại
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        //Lọc giá tiền nhỏ/lớn
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        //Sắp xếp theo giá trị
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'latest':
                    $query->orderBy('created_at', 'desc');
                    break;
               
                default:
                    $query->orderBy('id', 'desc');
                    break;
            }
        }
        
        $products = $query->paginate(9);
        
        //Thêm ảnh cho mỗi sản phẩm
        /** @var Product $product */
        foreach ($products as $product) {
            $product->image_url = $product->firstImage?->image ? asset('storage/uploads/products/' . $product->firstImage->image)
                : asset('storage/uploads/products/default-product.png');
        }
        
        return response()->json([
            'products' => view('clients.components.products_grid', compact('products'))->render(),
            'pagination' => $products->links('clients.components.pagination.pagination_custom')->toHtml()
        ]);
    }

    //Trang chi tiết sản phẩm
    public function detail($slug){
        $product = Product::with(['category','images','variants.size','variants.color','reviews.user',])
            ->where('slug', $slug)
            ->firstOrFail();

        //Lấy sản phẩm liên quan cùng danh mục (trừ cái userđang xem)
        $relatedProducts = Product::with('firstImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'in_stock')
            ->limit(4)
            ->get();
        
        /** @var Product $relatedProduct */
        foreach ($relatedProducts as $relatedProduct) {
            $relatedProduct->image_url = $relatedProduct->firstImage?->image 
                ? asset('storage/uploads/products/' . $relatedProduct->firstImage->image)
                : asset('storage/uploads/products/default-product.png');
        }
        
        return view('clients.pages.product-detail', compact('product', 'relatedProducts'));
    }

    //Trang chi tiết sản phẩm altenative
//     Product::with(['category','images','variants.size','variants.color'])
// ->where('slug', $slug)
// ->firstOrFail();
}
