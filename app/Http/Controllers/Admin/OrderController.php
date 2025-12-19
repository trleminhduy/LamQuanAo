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



    //to to to ghn to to to
    public function sendToGHN(Request $request, $id)
    {
        $order = Order::with('shippingAddress', 'items.productVariant.product')->findOrFail($id);

        //Kiểm tra đơn có mã GHN hay chưa

        if ($order->ghn_order_code) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng đã được gửi đến GHN' . $order->ghn_order_code,
            ]);
        }

        //kiểm tra địa chỉ có quận và xã chưa

        $address = $order->shippingAddress;
        if (!$address->district_id || !$address->ward_code) {
            return response()->json([
                'status' => false,
                'message' => 'Địa chỉ giao hàng chưa có thông tin quận/huyện và xã/phường',
            ]);
        }

        try {
            $ghnService = new \App\Services\GHNService();

            //Tính cân

            $totalWeight = $order->items->sum('quantity') * 500; //sản p nặng 500g


            //cbi data đơn ghn
            
            $orderData = [
                'from_district_id' => (int)config('ghn.from_district_id'),
                'to_district_id' => (int)$address->district_id,
                'to_ward_code' => $address->ward_code,
                'to_name' => $address->full_name,
                'to_phone' => $address->phone,
                'to_address' => $address->address,
                'weight' => $totalWeight,
                'service_type_id' => 2, //giao hàng tiêu chuẩn
                'payment_type_id' => 1, // shop của mình trả phí
                'required_note' => 'KHONGCHOXEMHANG',
                'items' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->productVariant->product->name,
                        'quantity' => $item->quantity,
                        'price' => (int)$item->price,
                    ];
                })->toArray(),
            ];

            //Gọi API tạo don
            $result = $ghnService->createOrder($orderData);

            if (!$result['success']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lỗi khi tạo đơn hàng GHN: ' . $result['message'],
                ]);
            }

            //Lưu thông tin ghn vào order
            $order->shipping_method = 'ghn';
            $order->ghn_order_code = $result['order_code'];
            $order->ghn_shipping_fee = $result['total_fee'];
            $order->ghn_expected_delivery = \Carbon\Carbon::parse($result['expected_delivery'])->format('Y-m-d H:i:s');
            $order->ghn_status = 'ready_to_pick';
            $order->status = 'processing'; //Cập nhật trạng thái đơn hàng
            $order->save();


            return response()->json([
                'status' => true,
                'message' => 'Gửi đơn hàng đến GHN thành công. Mã đơn: ' . $result['order_code'],
                'order_code' => $result['order_code'],
                'total_fee' => number_format($result['total_fee']) . 'đ'
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi gửi đơn hàng đến GHN: ' . $e->getMessage(),
            ]);
        }
    }
}
