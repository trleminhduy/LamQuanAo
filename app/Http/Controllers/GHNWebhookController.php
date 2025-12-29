<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class GHNWebhookController extends Controller
{
  
    public function handle(Request $request)
    {
        // log data để debug
        Log::info('GHN Webhook received:', $request->all());

        // lấy mã đơn và status từ GHN
        $orderCode = $request->input('OrderCode');
        $status = $request->input('Status');

        if (!$orderCode || !$status) {
            Log::warning('GHN Webhook: Thiếu OrderCode hoặc Status');
            return response()->json(['success' => false, 'message' => 'Missing data'], 400);
        }
        

        // tìm đơn hàng theo mã GHN (eager load items + variant để cộng stock)
        $order = Order::with('items.productVariant')
            ->where('ghn_order_code', $orderCode)
            ->first();

        if (!$order) {
            Log::warning("GHN Webhook: Không tìm thấy đơn hàng với mã $orderCode");
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if ($order->delivery_user_id) {
            
            Log::info("GHN Webhook: Đơn $orderCode đang giao nội bộ");
            return response()->json(['success' => true, 'message' => 'Internal delivery'], 200);
        }

        // cập nhật status 
        $order->ghn_status = $status;

        // tự động chuyển status 
        switch ($status) {
            case 'ready_to_pick':
                $order->status = 'processing'; 
                break;

            case 'picking':
            case 'picked':
                $order->status = 'processing'; 
                break;

            case 'storing':
            case 'transporting':
                $order->status = 'processing'; 
                break;

            case 'delivering':
                $order->status = 'shipping';
                break;

            case 'delivered':
                $order->status = 'delivered'; 
                $order->delivery_completed_at = now();
                break;

            case 'return':
            case 'returned':
                $order->status = 'cancelled';
                
                $this->restoreStock($order);
                break;

            case 'cancel':
            case 'exception':
                $order->status = 'cancelled';
            
                $this->restoreStock($order);
                break;

            default:
               
                break;
        }

        $order->save();

        Log::info("GHN Webhook: Cập nhật đơn $orderCode → status: {$order->status}, ghn_status: {$status}");

        // trả về success cho GHN biết đã nhận wh
        return response()->json([
            'success' => true,
            'message' => 'Webhook processed successfully'
        ], 200);
    }

    
    private function restoreStock(Order $order)
    {
        foreach ($order->items as $item) {
            $variant = $item->productVariant;
            if ($variant) {
                $variant->stock += $item->quantity;
                $variant->save();
                
                Log::info("GHN Webhook: Restored stock for variant #{$variant->id} (+{$item->quantity})");
            }
        }
    }
}
