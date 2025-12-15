<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Flasher\Toastr\Prime\toastr;

class DeliveryOrderController extends Controller
{
    // Dashboard cho delivery user
    public function dashboard()
    {
        $user = Auth::guard('admin')->user();
        
        // Thống kê đơn hàng
        $assignedCount = Order::where('delivery_user_id', $user->id)
            ->where('status', 'assigned')->count();
        
        $shippingCount = Order::where('delivery_user_id', $user->id)
            ->where('status', 'shipping')->count();
        
        $deliveredCount = Order::where('delivery_user_id', $user->id)
            ->where('status', 'delivered')->count();
        
        // Đơn hàng gần đây
        $recentOrders = Order::with(['user', 'shippingAddress'])
            ->where('delivery_user_id', $user->id)
            ->whereIn('status', ['assigned', 'shipping', 'delivered'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('delivery.dashboard', compact('assignedCount', 'shippingCount', 'deliveredCount', 'recentOrders'));
    }

    public function index(){
        $orders = Order::with(['user','shippingAddress','items.productVariant.product'])
          ->where('delivery_user_id',Auth::guard('admin')->id())
          ->whereIn('status',['assigned','shipping','delivered'])
          ->orderBy('created_at','desc')
          ->paginate(15);

        return view('delivery.orders.index', compact('orders'));
    }

    //Chi tiết đơn
    public function show(Order $order){
        if($order->delivery_user_id !== Auth::guard('admin')->id()){
            abort(403,'Bạn không có quyền truy cập đơn hàng này.');
        }
        $order->load(['user','shippingAddress','items.productVariant.product','items.productVariant.size','items.productVariant.color']);
        return view('delivery.orders.show', compact('order'));
    }

    //Shipper giao

    public function start(Order $order){
        if($order->delivery_user_id !== Auth::id()){
            abort(403);
        }

        if($order->status !='assigned'){
            toastr()->error('Đơn hàng không hợp lệ để bắt đầu giao.');
            return back();
        }
        $order -> update([
            'status'=>'shipping',
            'delivery_started_at'=> now(),
        ]);
        toastr()->success('Bắt đầu giao');
        return back();
    }

    //Shipper done
    public function complete (Order $order){
        if($order->delivery_user_id !== Auth::id()){
            abort(403);
        }

        if($order->status !='shipping'){
            toastr()->error('Đơn hàng chưa được giao.');
            return back();
        }
        $order -> update([
            'status'=>'delivered',
            'delivery_completed_at'=> now(),
        ]);
        toastr()->success('Hoàn thành giao');
        return back();
    }
}
