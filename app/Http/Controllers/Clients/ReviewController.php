<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{

    public function index (Product $product){
        return view('clients.pages.includes.review-list', compact('product'))->render();
    }
    public function createReview(Request $request){
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập để đánh giá sản phẩm',
            ],401);
        }

        $request ->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // check trùng lặp
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này',
            ], 422);
        }

        $review = new Review();
        $review->user_id = Auth::id();
        $review->product_id = $request->product_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã thêm đánh giá thành công',
        ],200);
    }
}
