<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ShippingAddress;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $user= Auth::user();
        $addresses=ShippingAddress::where('user_id',$user->id)->get();
        $defaultAddress=$addresses->where('is_default',1)->first();
        if(is_null($addresses)|| is_null ($defaultAddress)){
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

    public function getAddress(Request $request){

        $address=ShippingAddress::where('id',$request->address_id)
        ->where('user_id',Auth::id())->first();
        if(!$address){
            return response()->json(['success'=>false,'message'=>'Không tìm thấy địa chỉ']);
        }
        return response()->json(
            [
                'success'=>true,
                'data'=>$address
            ]
        );
    }
}
