<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Size;
use App\Models\Color;

class ProductVariantSeeder extends Seeder
{
    
    public function run(): void
    {
        // Kiểm tra product tồn tại
        $product = Product::find(1);
        if (!$product) {
            $this->command->warn('Product ID 1 không tồn tại. Bỏ qua seeder.');
            return;
        }

        // Lấy size với màu 
        $sizes = Size::all();
        $colors = Color::all();

        if ($sizes->isEmpty() || $colors->isEmpty()) {
            $this->command->warn('Sizes hoặc Colors chưa có dữ liệu. Chạy SizeSeeder và ColorSeeder trước.');
            return;
        }

        $now = now();
        $variants = [];

        // Tạo biến thể cho các size SMLXL (size thường dùng) cộng thêm 5 màu đầu
        $selectedSizes = $sizes->whereIn('name', ['S', 'M', 'L', 'XL']); 
        $selectedColors = $colors->take(5); // 5 màu đầu trong color seeder

        $basePrice = $product->price; // Giá gốc 

        foreach ($selectedSizes as $size) {
            foreach ($selectedColors as $color) {
                // Giá biến thể 
                $variantPrice = match($size->name) {
                    'S' => $basePrice - 10000,  
                    'M' => 0,                    
                    'L' => $basePrice,           
                    'XL' => $basePrice + 20000,  
                    default => $basePrice
                };

                // Stock khác nhau để test
                $stock = match($color->name) {
                    'Black' => 50,   
                    'White' => 30,   
                    'Red' => 10,     
                    'Blue' => 0,     
                    default => 20
                };

                $variants[] = [
                    'product_id' => $product->id,
                    'size_id' => $size->id,
                    'color_id' => $color->id,
                    'price' => $variantPrice,
                    'stock' => $stock,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Sử dụng upsert để có thể chạy lại bao nhiu lần cũm được 
        DB::table('product_variants')->upsert(
            $variants,
            ['product_id', 'size_id', 'color_id'], // Unique keys
            ['price', 'stock', 'updated_at'] // Update columns nếu đã tồn tại
        );

        $this->command->info('✓ Đã tạo ' . count($variants) . ' variants cho sản phẩm: ' . $product->name);
    }
}
