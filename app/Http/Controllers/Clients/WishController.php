<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishController extends Controller
{
    public function index()
    {
        $wishlists= Wishlist::with('product')->where('user_id', Auth::id())->get();
        return view('clients.pages.wishlist', compact('wishlists'));
    }

    public function add(Request $request){
        if(!Auth::check()){
            return response()->json([
                'success' => false,
                'message'=>'Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích',
                'redirect' => route('login')
            ]);
        }
        $exists = Wishlist::where('user_id', Auth::id())
        ->where('product_id', $request->product_id)
        ->exists();

        if($exists){
            return response()->json([
                'success' => false,
                'message'=>'Sản phẩm đã có trong danh sách yêu thích'
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'success' => true,
            'message'=>'Thêm sản phẩm vào danh sách yêu thích thành công'
        ]);
    }

    public function remove(Request $request){
        Wishlist::where('user_id', Auth::id())
        ->where('product_id', $request->product_id)
        ->delete();

        return response()->json([
            'success' => true,
            'message'=>'Xóa sản phẩm khỏi danh sách yêu thích thành công'
        ]);
    }
}
