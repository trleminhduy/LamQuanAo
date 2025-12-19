<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use App\Models\CartItem;

class JMeterLoadTestSeeder extends Seeder
{
    public function run(): void
    {
        $variantId = env('JM_VARIANT_ID');
        if (!$variantId) {
            $this->command->error('Vui lòng cấu hình JM_VARIANT_ID trong .env để chỉ định product_variant_id.');
            return;
        }

        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            $this->command->error('Không tìm thấy ProductVariant với id=' . $variantId);
            return;
        }

        // Đặt tồn kho = 5 cho biến thể mục tiêu
        $variant->stock = 5;
        $variant->save();

        DB::transaction(function () use ($variant) {
            for ($i = 1; $i <= 50; $i++) {
                $email = "jm_user_{$i}@test.local";

                // Tạo user test (nếu chưa có)
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => "jm_user_{$i}",
                        'password' => Hash::make('Password123!'),
                        'email_verified_at' => now(),
                    ]
                );

                // Tạo địa chỉ mặc định
                $address = ShippingAddress::firstOrCreate(
                    ['user_id' => $user->id, 'is_default' => 1],
                    [
                        'name' => 'JM Address',
                        'phone' => '0900000000',
                        'address_line' => '123 Test',
                        'city' => 'HCM',
                        'district' => 'Q1',
                        'ward' => 'P1',
                        'is_default' => 1,
                    ]
                );

                // Tạo item trong giỏ cho cùng một biến thể
                CartItem::updateOrCreate(
                    ['user_id' => $user->id, 'product_variant_id' => $variant->id],
                    ['quantity' => 1]
                );

                // In ra dòng CSV phục vụ JMeter
                $this->command->line("{$email},Password123!,{$address->id}");
            }
        });

        $this->command->info('Seeder đã tạo 50 user, địa chỉ mặc định, giỏ hàng và set stock=5 cho biến thể.');
        $this->command->info('Copy các dòng CSV ở trên vào docs/jmeter/users.csv');
    }
}
