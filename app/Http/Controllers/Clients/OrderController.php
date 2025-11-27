<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showOrder($id)
    {
        $order = Order::with('items.productVariant', 'user', 'shippingAddress', 'payment')->findOrFail($id);

        $userId = auth()->id();

        // dd($order);

        return view('clients.pages.order-detail', compact('order'));
    }
    //Huỷ đơn hàng
    public function cancel($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        //Hoàn về kho
        foreach ($order->items as $item) {
            $item->productVariant->increment('stock', $item->quantity);
        }
        //Update trạng thái đơn hàng

        $order->update([
            'status' => 'cancelled'
        ]);
        return redirect()->back()->with('success', 'Đơn hàng đã được huỷ thành công');
    }
}
