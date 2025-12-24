<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Bắt buộc upload ảnh, max 2MB
        ], [
            'image.required' => 'Vui lòng upload ảnh ',
            'image.image' => 'File phải là ảnh',
            'image.mimes' => 'Ảnh phải có định dạng jpeg, png hoặc jpg',
            'image.max' => 'Ảnh không được vượt quá 2MB',
        ]);
        $orderItem = OrderItem::with('order')->findOrFail($request->order_item_id);

        //Kiểm tra phải deliverd mới đc refund
        if ($orderItem->order->status !== 'delivered') {
            return response()->json([
                'status' => false,
                'message' => 'Chỉ có thể yêu cầu hoàn tiền cho các đơn hàng đã giao hàng'
            ]);
        }

        //kiểm tra date < 3 thì cho rf
        $deliveredDate = $orderItem->order->delivery_completed_at ?? $orderItem->order->updated_at;
        $daysSinceDelivery = Carbon::parse($deliveredDate)->diffInDays(Carbon::now());

        if ($daysSinceDelivery > 3) {
            return response()->json([
                'status' => false,
                'message' => 'Đã quá thời hạn yêu cầu hoàn trả .'
            ]);
        }

        //Kiểm tra số lượng < số lượng đã mua
        if ($request->quantity > $orderItem->quantity) {
            return response()->json([
                'status' => false,
                'message' => 'Số lượng hoàn trả không thể vượt quá số lượng đã mua'
            ]);
        }

        //check có yêu cầu hoàn tiền chưa
        $existingRefund = Refund::where('order_item_id', $orderItem->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            return response()->json([
                'status' => false,
                'message' => 'Đã có yêu cầu hoàn tiền cho mục đơn hàng này'
            ]);
        }

        //up ảnh 
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('refunds', 'public');
        }

        //Tính tiền hoàn 
        $amount = $orderItem->price * $request->quantity;

        // Tạo yêu cầu rf
        Refund::create([
            'user_id' => Auth::id(),
            'order_item_id' => $request->order_item_id,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'image' => $imagePath,
            'refund_type' => 'money',
            'amount' => $amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Gửi yêu cầu hoàn trả thành công! Chúng tôi sẽ xử lý trong 24-48h.'
        ]);
    }
}
