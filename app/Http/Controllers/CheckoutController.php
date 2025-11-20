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
}
