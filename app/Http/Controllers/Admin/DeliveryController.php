<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Flasher\Toastr\Prime\toastr;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        //Đơn cân giao
        $query = Order::with(['user', 'deliveryUser', 'shippingAddress']);

        //Lọc trạng thái
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        //Lọc shipper
        if ($request->has('delivery_user_id') && $request->delivery_user_id != '') {
            $query->where('delivery_user_id', $request->delivery_user_id);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        //Danh sách shipper
        $deliveryRole = Role::where('name', 'delivery_user')->first();
        $deliveryUsers = $deliveryRole ? $deliveryRole->users : collect();
        return view('admin.deliveries.index', compact('orders', 'deliveryUsers'));
    }
    //trang gán đơn
    public function assignForm(Order $order)
    {

        //check trạng thái đơn
        $validateStatuses = ['pending', 'processing', 'confirmed'];
        if (!in_array($order->status, $validateStatuses)) {
            toastr()->error('Đơn hàng không hợp lệ để phân công giao hàng.' . $order->status);
            return redirect()->route('admin.deliveries.index');
        }
        //Danh sách shipper
        $deliveryRole = Role::where('name', 'delivery_user')->first();
        $deliveryUsers = $deliveryRole ? $deliveryRole->users : collect();

        return view('admin.deliveries.assign', compact('order', 'deliveryUsers'));
    }

    //xử lý gán
    public function assign(Request $request, Order $order)
    {
        $request->validate([
            'delivery_user_id' => 'required|exists:users,id',
            'delivery_note' => 'nullable|string|max:1000',
        ]);

        //check trạng thái đơn
        $validateStatuses = ['pending', 'processing', 'confirmed'];
        if (!in_array($order->status, $validateStatuses)) {
            toastr()->error('Đơn hàng không hợp lệ để phân công giao hàng.');
            return redirect()->route('admin.deliveries.index');
        }

        $order->update([
            'delivery_user_id' => $request->delivery_user_id,
            'delivery_note' => $request->delivery_note,
            'status' => 'assigned',
        ]);

        toastr()->success('Phân công giao hàng thành công');
        return redirect()->route('admin.deliveries.index');
    }

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

        $completedCount = Order::where('delivery_user_id', $user->id)
            ->where('status', 'completed')->count();

        // Đơn hàng gần đây
        $recentOrders = Order::with(['user', 'shippingAddress'])
            ->where('delivery_user_id', $user->id)
            ->whereIn('status', ['assigned', 'shipping', 'delivered', 'completed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('delivery.dashboard', compact('assignedCount', 'shippingCount', 'deliveredCount', 'completedCount', 'recentOrders'));
    }

    // Đơn hàng của tôi
    public function myOrders()
    {
        $orders = Order::with(['user', 'shippingAddress', 'items.productVariant.product'])
            ->where('delivery_user_id', Auth::guard('admin')->id())
            ->whereIn('status', ['assigned', 'shipping', 'delivered', 'completed'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('delivery.orders.index', compact('orders'));
    }

    // Chi tiết đơn
    public function showOrder(Order $order)
    {
        if ($order->delivery_user_id !== Auth::guard('admin')->id()) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }
        $order->load(['user', 'shippingAddress', 'items.productVariant.product', 'items.productVariant.size', 'items.productVariant.color']);
        return view('delivery.orders.show', compact('order'));
    }

    // Bắt đầu giao
    public function startDelivery(Order $order)
    {
        if ($order->delivery_user_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        if ($order->status != 'assigned') {
            toastr()->error('Đơn hàng không hợp lệ để bắt đầu giao.');
            return back();
        }

        $order->update([
            'status' => 'shipping',
            'delivery_started_at' => now(),
        ]);
        toastr()->success('Bắt đầu giao hàng');
        return back();
    }

    // Hoàn thành giao hàng
    public function completeDelivery(Order $order)
    {
        if ($order->delivery_user_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        if ($order->status != 'shipping') {
            toastr()->error('Đơn hàng chưa được giao.');
            return back();
        }
        $order->update([
            'status' => 'delivered',
            'delivery_completed_at' => now(),
        ]);
        toastr()->success('Hoàn thành giao hàng');
        return back();
    }

    //sce kh từ chối 
   

    

    
}
