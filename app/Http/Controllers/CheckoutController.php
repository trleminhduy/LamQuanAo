<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->address_id;
            $order->total_price = $totalPrice;
            $order->status = 'pending';
            $order->coupon_id = null; // kh có mgg
            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->productVariant->price
                ]);
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

    public function placeOrderPayPal(Request $request)
    {

     DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)->get();
            // Tính tổng tiền sản phẩm
            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            });

            // Phí vận chuyển
            $shippingFee = 30000;

            // Tổng đơn hàng = tổng sản phẩm + phí ship
            $totalPrice = $subtotal + $shippingFee;

            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->address_id;
            $order->total_price = $request->amount *26000;
            $order->status = 'pending';
            $order->coupon_id = null;
            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->productVariant->price
                ]);
            }

            // Payment với PayPal
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'paypal',
                'amount' => $order->total_price,
                'status' => 'completed', // PayPal đã thanh toán
                'paid_at' => now(),
                'transaction_id' => $request->transactionID
            ]);

            // Xóa giỏ hàng
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json(['success'=>true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //execute post
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result;
}

    public function momo_payment(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                toastr()->error('Giỏ hàng của bạn đang trống.');
                return redirect()->route('cart.index');
            }

            // Tính tổng tiền sản phẩm
            $subtotal = $cartItems->sum(function ($item) {
                return $item->productVariant->price * $item->quantity;
            });

            // Phí vận chuyển
            $shippingFee = 30000;

            // Tổng đơn hàng = tổng sản phẩm + phí ship
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
            }

            // Tạo Payment với status pending
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'momo',
                'amount' => $order->total_price,
                'status' => 'pending', // Chờ thanh toán
                'paid_at' => null
            ]);

            DB::commit();

            

            // Gọi API MoMo
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

            // Credentials MoMo Test 
            $partnerCode = 'MOMOBKUN20180529';
            $accessKey = 'klm05TvNBzhg7h7j';
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
            $orderInfo = "Thanh toán đơn hàng #" . $order->id;
            $amount = (string)$totalPrice; // Convert to string
            $orderId = $order->id . "_" . time();
            $redirectUrl = route('checkout.momo.return');
            $ipnUrl = route('checkout.momo.notify');
            $extraData = "";

            $requestId = time() . "";
            $requestType = "payWithATM"; 

            $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
            $signature = hash_hmac("sha256", $rawHash, $secretKey);

            $data = array(
                'partnerCode' => $partnerCode,
                'partnerName' => "Test",
                "storeId" => "MomoTestStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            );

            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);

            // Debug: Log response từ MoMo
            Log::info('MoMo Response:', $jsonResult);

            if (isset($jsonResult['payUrl'])) {
                return redirect()->to($jsonResult['payUrl']);
            } else {
                // Nếu MoMo trả về lỗi, rollback order
                DB::beginTransaction();
                $order->delete();
                DB::commit();
                
                $errorMessage = isset($jsonResult['message']) ? $jsonResult['message'] : 'Không thể kết nối đến MoMo';
                toastr()->error($errorMessage);
                return redirect()->route('checkout.index');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Có lỗi xảy ra: ' . $e->getMessage());
            return redirect()->route('checkout.index');
        }
    }

    // Xử lý khi user quay về từ MoMo (redirectUrl)
    public function momoReturn(Request $request)
    {
        $resultCode = $request->resultCode;
        $orderId = $request->orderId; // Format: "order_id_timestamp"
        $transId = $request->transId;
        
        // Lấy order_id từ orderId
        $orderIdParts = explode("_", $orderId);
        $dbOrderId = $orderIdParts[0];
        
        if ($resultCode == 0) {
            // Thanh toán thành công - Cập nhật Payment
            $payment = Payment::whereHas('order', function($query) use ($dbOrderId) {
                $query->where('id', $dbOrderId);
            })->where('payment_method', 'momo')->first();
            
            if ($payment && $payment->status == 'pending') {
                $payment->status = 'completed';
                $payment->paid_at = now();
                $payment->transaction_id = $transId;
                $payment->save();
                
                // Xóa giỏ hàng
                $order = Order::find($dbOrderId);
                if ($order) {
                    CartItem::where('user_id', $order->user_id)->delete();
                }
            }
            
            toastr()->success('Thanh toán MoMo thành công!');
        } else {
            // Thanh toán thất bại - Cập nhật Payment
            $payment = Payment::whereHas('order', function($query) use ($dbOrderId) {
                $query->where('id', $dbOrderId);
            })->where('payment_method', 'momo')->first();
            
            if ($payment) {
                $payment->status = 'failed';
                $payment->save();
            }
            
            toastr()->error('Thanh toán MoMo thất bại hoặc bị hủy.');
        }
        
        return redirect()->route('account');
    }

    // Webhook từ MoMo gọi đến (ipnUrl) - Cập nhật status thanh toán
    public function momoNotify(Request $request)
    {
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        
        // Verify signature từ MoMo
        $partnerCode = $request->partnerCode;
        $orderId = $request->orderId;
        $requestId = $request->requestId;
        $amount = $request->amount;
        $orderInfo = $request->orderInfo;
        $orderType = $request->orderType;
        $transId = $request->transId;
        $resultCode = $request->resultCode;
        $message = $request->message;
        $payType = $request->payType;
        $responseTime = $request->responseTime;
        $extraData = $request->extraData;
        
        $rawHash = "accessKey=" . $request->accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&message=" . $message . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&orderType=" . $orderType . "&partnerCode=" . $partnerCode . "&payType=" . $payType . "&requestId=" . $requestId . "&responseTime=" . $responseTime . "&resultCode=" . $resultCode . "&transId=" . $transId;
        
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        if ($signature == $request->signature) {
            // Signature hợp lệ
            
            // Lấy order_id từ orderId (format: "order_id_timestamp")
            $orderIdParts = explode("_", $orderId);
            $dbOrderId = $orderIdParts[0];
            
            if ($resultCode == 0) {
                // Thanh toán thành công - Cập nhật Payment
                $payment = Payment::whereHas('order', function($query) use ($dbOrderId) {
                    $query->where('id', $dbOrderId);
                })->where('payment_method', 'momo')->first();
                
                if ($payment) {
                    $payment->status = 'completed';
                    $payment->paid_at = now();
                    $payment->transaction_id = $transId;
                    $payment->save();
                    
                    // Xóa giỏ hàng
                    $order = Order::find($dbOrderId);
                    if ($order) {
                        CartItem::where('user_id', $order->user_id)->delete();
                    }
                }
            } else {
                // Thanh toán thất bại - Cập nhật status
                $payment = Payment::whereHas('order', function($query) use ($dbOrderId) {
                    $query->where('id', $dbOrderId);
                })->where('payment_method', 'momo')->first();
                
                if ($payment) {
                    $payment->status = 'failed';
                    $payment->save();
                }
            }
            
            return response()->json(['message' => 'Notification received']);
        } else {
            // Signature không hợp lệ
            return response()->json(['message' => 'Invalid signature'], 400);
        }
    }

}
