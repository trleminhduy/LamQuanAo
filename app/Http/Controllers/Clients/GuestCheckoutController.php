<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestCheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        //nếu giỏ trống back home

        if (empty($cart)) {
            toastr()->info('Giỏ hàng của bạn đang trống.');
            return redirect()->route('home');
        }

        $cartItems = collect();
        $subTotal = 0;

        foreach ($cart as $item) {
            $variant = ProductVariant::with(['product.firstImage', 'size', 'color'])
                ->find($item['product_variant_id']);

            if ($variant) {
                $cartItems->push((object)[
                    'productVariant' => $variant,
                    'quantity' => $item['quantity'],
                ]);
                $subTotal += $variant->price * $item['quantity'];
            }
        }
        $shippingFee = 30000; //phí vận chuyển cố định 
        $total = $subTotal + $shippingFee;
        return view('clients.pages.guest-checkout', compact('cartItems', 'subTotal', 'shippingFee', 'total'));
    }

    /// xử lý đặt hàng vãng lai
    public function placeOrder(Request $request)
    {

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'province' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string',
            'address' => 'required|string',
            'payment_method' => 'required|in:cod,vnpay',
            'district_id' => 'required',
            'ward_code' => 'required',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            toastr()->info('Giỏ hàng của bạn đang trống.');
            return redirect()->route('home');
        }

        DB::beginTransaction();

        try {
            //tạo, get session id
            $sessionId = session()->getId();


            //Tính tổng tiền
            $subTotal = 0;
            foreach ($cart as $item) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if (!$variant) {
                    throw new \Exception('Sản phẩm không tồn tại.');
                }
                $subTotal += $variant->price * $item['quantity'];
            }
            $shippingFee = 30000;
            $totalPrice = $subTotal + $shippingFee;

            //tạo địa chỉ giao hàng
            $fullAddress = $request->address . ', ' . $request->ward . ', ' . $request->district . ', ' . $request->province;

            $shippingAddress = ShippingAddress::create([
                'user_id' => null,
                'session_id' => $sessionId,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $fullAddress,
                'city' => $request->province,
                'district_id' => $request->district_id,
                'ward_code' => $request->ward_code,
                'is_default' => false,
            ]);


            //tạo đơn hàng
            $order = Order::create([
                'user_id' => null,
                'shipping_address_id' => $shippingAddress->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'guest_name' => $request->full_name,
                'guest_phone' => $request->phone,
                'guest_email' => $request->email,
            ]);

            //tạo order items với trừ stok

            foreach ($cart as $item) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant->stock < $item['quantity']) {
                    throw new \Exception('Sản phẩm ' . $variant->product->name . ' không đủ số lượng trong kho');
                }


                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $variant->price,
                ]);

                //trừ stock
                $variant->stock -= $item['quantity'];
                $variant->save();
            }


            //Thanh toán
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null,
            ]);

            //Xoá giỏ
            session()->forget('cart');
            DB::commit();

            // Nếu chọn VNPay -> Redirect sang VNPay
            if ($request->payment_method === 'vnpay') {
                return $this->vnpayPayment($order, $request);
            }

            //COD -> Lưu order_id vào session
            session()->put('guest_order_id', $order->id);
            toastr()->success('Đặt hàng thành công!');
            return redirect()->route('guest.order.success');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Đặt hàng thất bại. Vui lòng thử lại.');
            return redirect()->back();
        }
    }

    //trang order thành công vãng lai
    public function orderSuccess()
    {
        $orderId = session()->get('guest_order_id');

        if (!$orderId) {
            return redirect()->route('home');
        }

        $order = Order::with(['items.productVariant.product', 'shippingAddress', 'payment'])
            ->find($orderId);

        if (!$order) {
            return redirect()->route('home');
        }

        session()->forget('guest_order_id');

        return view('clients.pages.guest-order-success', compact('order'));
    }

    //track đơn = sdt
    public function trackOrder(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // Tìm tất cả đơn với sdt
        $orders = Order::with(['items.productVariant.product.firstImage', 'shippingAddress', 'payment'])
            ->where('guest_phone', $request->phone)
            ->whereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            toastr()->error('Không tìm thấy đơn hàng với số điện thoại này');
            return redirect()->back();
        }

        return view('clients.pages.guest-track-order', compact('orders'));
    }

    // Xử lý thanh toán VNPay cho guest
    private function vnpayPayment($order, $request)
    {
        // Cấu hình VNPay
        $vnp_TmnCode = "AQ8SUX3U";
        $vnp_HashSecret = "G0M8AM9GRJM6OXCMCNRHBDJYNIFQECEK";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('guest.vnpay.return');

        $vnp_TxnRef = $order->id . '_' . time();
        $vnp_OrderInfo = 'Thanh toan don hang #' . $order->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->total_price * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return redirect()->to($vnp_Url);
    }

    // Xử lý callback từ VNPay
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = "G0M8AM9GRJM6OXCMCNRHBDJYNIFQECEK";
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = array();
        
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // Thanh toán thành công
                $orderId = explode('_', $request->vnp_TxnRef)[0];
                $order = Order::find($orderId);
                
                if ($order) {
                    $payment = Payment::where('order_id', $order->id)->first();
                    $payment->status = 'paid';
                    $payment->paid_at = now();
                    $payment->save();
                    
                    session()->put('guest_order_id', $order->id);
                    toastr()->success('Thanh toán VNPay thành công!');
                    return redirect()->route('guest.order.success');
                }
            } else {
                // Thanh toán thất bại
                toastr()->error('Thanh toán VNPay thất bại!');
                return redirect()->route('home');
            }
        } else {
            toastr()->error('Chữ ký không hợp lệ!');
            return redirect()->route('home');
        }
    }
}
