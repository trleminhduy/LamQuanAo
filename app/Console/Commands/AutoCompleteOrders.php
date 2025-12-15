<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoCompleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hoàn tất đơn sau 7 ngày nếu khách hàng không phản hồi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('status','delivered')
        ->where('delivery_completed_at', '<=', Carbon::now()->subDays(3))
        ->get();

        $count = 0;
        foreach($orders as $order){
            $order->update([
                'status' => 'completed',
            ]);
            if($order->payment){
                $order->payment->update([
                    'status' => 'completed',
                ]);
            }
            $count++;
        }
        $this->info("Đã tự động hoàn tất $count đơn hàng.");
        return 0;
    }
}
