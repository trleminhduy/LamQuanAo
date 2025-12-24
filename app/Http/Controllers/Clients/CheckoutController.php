<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use function Flasher\Toastr\Prime\toastr;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('is_default', 1)->first();
        if (is_null($addresses) || is_null($defaultAddress)) {
            toastr()->error('Vui lòng thêm địa chỉ giao hàng trước khi thanh toán');
            return redirect()->route('account');
        }
        //Tổng giỏ hàng
        $cartItems = CartItem::where('user_id', $user->id)
            ->with(['productVariant.product', 'productVariant.color', 'productVariant.size'])
            ->get();

        // Tính tổng tiền
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->productVariant->price; // Lấy giá từ variant, không phải product
            $subtotal += $price * $item->quantity;
        }

        // Phí vận chuyển (có thể tùy chỉnh)
        $shippingFee = 30000; // 30k

        // Tổng cộng
        $total = $subtotal + $shippingFee;

        return view('clients.pages.checkout', compact('addresses', 'defaultAddress', 'cartItems', 'subtotal', 'shippingFee', 'total'));
    }

    public function getAddress(Request $request)
    {

        $address = ShippingAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())->first();
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ']);
        }
        return response()->json(
            [
                'success' => true,
                'data' => $address
            ]
        );
    }

    public  function placeOrder(Request $request)
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống.');
        }
        DB::beginTransaction();

        try {
            //Tạo đơn hàng

            // Tính tổng tiền sản phẩm
            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            });

            // Phí vận chuyển
            $shippingFee = 30000;

            // Tổng đơn hàng = tổng sản phẩm + phí ship
            $totalPrice = $subtotal + $shippingFee;
            
            // Xử lý coupon 
            $couponId = null;
            $discountAmount = 0;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                if ($coupon) {
                    $couponId = $coupon->id;
                    $discountAmount = $request->discount_amount ?? 0;
                    $totalPrice -= $discountAmount;
                }
            }

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->address_id;
            $order->total_price = $totalPrice;
            $order->status = 'pending';
            $order->coupon_id = $couponId;
            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->productVariant->price
                ]);

                // Trừ stock
                $variant = $item->productVariant;
                if ($variant->stock < $item->quantity) {
                    throw new \Exception('Sản phẩm ' . $variant->product->name . ' không đủ số lượng trong kho.');
                }
                $variant->stock -= $item->quantity;
                $variant->save();
            }

            //Payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null
            ]);

            //Khi thành công thì xoá giỏ
            CartItem::where('user_id', $user->id)->delete();
            DB::commit();
            toastr()->success('Đặt hàng thành công');
            return redirect()->route('account');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.');
            return redirect()->route('checkout.index');
        }
    }


    public function vnpay_payment(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                toastr()->error('Giỏ hàng của bạn đang trống.');
                return redirect()->route('cart.index');
            }

            // Tính tổng tiền
            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            });
            $shippingFee = 30000;
            $totalPrice = $subtotal + $shippingFee;

            // Tạo đơn hàng
            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->address_id;
            $order->total_price = $totalPrice;
            $order->status = 'pending';
            $order->coupon_id = null;
            $order->save();

            // Tạo OrderItems
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->productVariant->price
                ]);

                // Trừ stock
                $variant = $item->productVariant;
                if ($variant->stock < $item->quantity) {
                    throw new \Exception('Sản phẩm ' . $variant->product->name . ' không đủ số lượng trong kho.');
                }
                $variant->stock -= $item->quantity;
                $variant->save();
            }

            // Tạo Payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'vnpay',
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null
            ]);

            DB::commit();

            // Cấu hình VNPay
            $vnp_TmnCode = "AQ8SUX3U";
            $vnp_HashSecret = "G0M8AM9GRJM6OXCMCNRHBDJYNIFQECEK";
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('checkout.vnpay.return');

            $vnp_TxnRef = $order->id . '_' . time();
            $vnp_OrderInfo = 'Thanh toan don hang #' . $order->id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $totalPrice * 100;
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
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Có lỗi xảy ra: ' . $e->getMessage());
            return redirect()->route('checkout.index');
        }
    }

    // Callback từ VNPay
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = "G0M8AM9GRJM6OXCMCNRHBDJYNIFQECEK";
        $inputData = array();

        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            $vnp_TxnRef = $request->vnp_TxnRef;
            $vnp_ResponseCode = $request->vnp_ResponseCode;
            $vnp_TransactionNo = $request->vnp_TransactionNo;

            $orderIdParts = explode("_", $vnp_TxnRef);
            $dbOrderId = $orderIdParts[0];

            if ($vnp_ResponseCode == '00') {
                $payment = Payment::whereHas('order', function ($query) use ($dbOrderId) {
                    $query->where('id', $dbOrderId);
                })->where('payment_method', 'vnpay')->first();

                if ($payment && $payment->status == 'pending') {
                    $payment->status = 'completed';
                    $payment->paid_at = now();
                    $payment->transaction_id = $vnp_TransactionNo;
                    $payment->save();

                    $order = Order::find($dbOrderId);
                    if ($order) {
                        CartItem::where('user_id', $order->user_id)->delete();
                    }
                }

                toastr()->success('Thanh toán VNPay thành công!');
            } else {
                $payment = Payment::whereHas('order', function ($query) use ($dbOrderId) {
                    $query->where('id', $dbOrderId);
                })->where('payment_method', 'vnpay')->first();

                if ($payment) {
                    $payment->status = 'failed';
                    $payment->save();
                }

                toastr()->error('Thanh toán VNPay thất bại hoặc bị hủy.');
            }
        } else {
            toastr()->error('Chữ ký không hợp lệ!');
        }

        return redirect()->route('account');
    }

    //cp api
    public function applyCoupon(Request $request)
    {
        $code = $request->code;
        $total = $request->total;

        //tìm cp
        $coupon = Coupon::where('code', $code)
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
        }

        //check date
        $now = now();
        if ($now < $coupon->start_date || $now > $coupon->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
        }

        //tính giaem giá
        $idscount = 0;
        if ($coupon->discount_type == 'percent') {
            $discount = ($total * $coupon->discount_value) / 100;
        } else {
            $discount = $coupon->discount_value;
        }

        //giảm kh vượt tổng tiền
        if ($discount > $total) {
            $discount = $total;
        }

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => $discount,
            'new_total' => $total - $discount,
            'coupon_code' => $code
        ]);
    }
}
