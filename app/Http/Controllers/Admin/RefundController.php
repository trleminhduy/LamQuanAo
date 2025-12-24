<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    
    public function index()
    {
        $refunds = Refund::with('user', 'orderItem.productVariant.product', 'orderItem.order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pages.refunds', compact('refunds'));
    }

    
    public function approve(Request $request)
    {
        $refund = Refund::with('orderItem.productVariant')->findOrFail($request->id);

        if ($refund->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu này đã được xử lý!'
            ]);
        }

        // Cộng lại stock
        $variant = $refund->orderItem->productVariant;
        $variant->stock += $refund->quantity;
        $variant->save();

        
        $refund->status = 'approved';
        $refund->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã duyệt yêu cầu hoàn trả và cộng lại ' . $refund->quantity . ' sản phẩm vào kho.'
        ]);
    }

   
    public function reject(Request $request)
    {
        $refund = Refund::findOrFail($request->id);

        if ($refund->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Yêu cầu này đã được xử lý!'
            ]);
        }

        $refund->status = 'rejected';
        $refund->save();

        return response()->json([
            'status' => true,
            'message' => 'Đã từ chối yêu cầu hoàn trả.'
        ]);
    }
}