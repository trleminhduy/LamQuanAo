<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.productVariant.product', 'user', 'shippingAddress', 'payment')->get();



        return view('admin.pages.orders', compact('orders'));
    }
    public function confirmOrder(Request $request)
    {

        $order = Order::find($request->id);

        if ($order) {
            $order->status = 'processing';
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'Xác nhận đơn hàng thành công',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Đơn hàng không tồn tại',
        ]);
    }
}
