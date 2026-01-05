<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GHNService;

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

    public function confirmReceived(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        //Chỉ cho phép xác nhận nếu  hàng đã được giao
        if ($order->status != 'delivered') {
            toastr()->error('Đơn hàng chưa được giao tới bạn');
            return back();
        }
        $order->update([
            'status' => 'completed',
        ]);
        if ($order->payment) {
            $order->payment->update([
                'status' => 'completed',
            ]);
        }
        toastr()->success('Cảm ơn bạn đã xác nhân đơn hàng');
        return back();
    }

    protected $ghnService;
    public function __construct(GHNService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    public function getTracking($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$order->ghn_order_code) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng chưa được giao cho đơn vị vận chuyển'
            ]);
        }

        return response()->json(
            $this->ghnService->getOrderTracking($order->ghn_order_code)
        );
    }
}
