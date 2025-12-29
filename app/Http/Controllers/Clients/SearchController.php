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
        
        // Xử lý keyword từ voice search: loại bỏ dấu câu, khoảng trắng thừa
        $keyword = trim($keyword); // Xóa khoảng trắng 2 đầu
        $keyword = rtrim($keyword, '.,!?;:'); // Xóa dấu câu cuối
        $keyword = preg_replace('/\s+/', ' ', $keyword); // Xóa khoảng trắng thừa giữa các từ
        
    
        $products = Product::where('name', 'LIKE', "%$keyword%")
            ->paginate(12)
            ->appends(['keyword' => $keyword]);
            
             
        // $products = Product::where('id', $keyword)
        //     ->orWhere('name', 'LIKE', "%$keyword%")
        //     ->paginate(12)
        //     ->appends(['keyword' => $keyword]);
        return view('clients.pages.products-search', compact('keyword', 'products'));
    }
}
