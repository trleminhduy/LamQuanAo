<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        if (!$keyword) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm');
        }
        
        // Tìm kiếm tên sản phẩm bắt đầu bằng keyword hoặc có keyword là từ riêng 
        $products = Product::where(function($query) use ($keyword) {
            $query->where('name', 'LIKE', "$keyword%")  // Bắt đầu bằng keyword
                  ->orWhere('name', 'LIKE', "% $keyword%"); // keyword là từ riêng 
        })
        ->paginate(12)
        ->appends(['keyword' => $keyword]);
            
        return view('clients.pages.products-search', compact('keyword', 'products'));
    }
}
