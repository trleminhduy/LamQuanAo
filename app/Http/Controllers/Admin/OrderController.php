<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function showOrderDetail($id)
    {
        $order = Order::with('items.productVariant.product', 'user', 'shippingAddress', 'payment')->findOrFail($id);

        return view('admin.pages.order-detail', compact('order'));
    }

    public function sendMailInvoice(Request $request)
    {
        $order = Order::with('items.productVariant.product.images', 'items.productVariant.size', 'items.productVariant.color', 'user', 'shippingAddress', 'payment')->find($request->order_id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy đơn hàng',
            ]);
        }

        if (!$order->user || !$order->user->email) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không có thông tin email người dùng',
            ]);
        }

        try {
            Mail::send('admin.emails.invoice', compact('order'), function ($message) use ($order) {
                $message->to($order->user->email)
                    ->subject('Hóa đơn mua hàng #' . $order->id);
            });
            return response()->json([
                'status' => true,
                'message' => 'Gửi email hóa đơn thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gửi email thất bại: ' . $e->getMessage(),
            ]);
        }
    }

    public function cancelOrder(Request $request)
    {
        $order = Order::find($request->id);

        if ($order) {
            foreach ($order->items as $item) {
                $variant = $item->productVariant;
                if ($variant) {
                    // Tăng lại stock
                    $variant->stock += $item->quantity;
                    $variant->save();
                }
            }

            $order->status = 'cancelled';
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'Huỷ đơn hàng thành công',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Đơn hàng không tồn tại',
        ]);
    }
}
