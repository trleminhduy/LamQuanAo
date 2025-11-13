<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Size;
use App\Models\Color;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tạo variants cho sản phẩm ID 1 (Áo thun nam basic)
     */
    public function run(): void
    {
        // Kiểm tra product tồn tại
        $product = Product::find(1);
        if (!$product) {
            $this->command->warn('Product ID 1 không tồn tại. Bỏ qua seeder.');
            return;
        }

        // Lấy sizes và colors
        $sizes = Size::all();
        $colors = Color::all();

        if ($sizes->isEmpty() || $colors->isEmpty()) {
            $this->command->warn('Sizes hoặc Colors chưa có dữ liệu. Chạy SizeSeeder và ColorSeeder trước.');
            return;
        }

        $now = now();
        $variants = [];

        // Tạo variants cho các size phổ biến (S, M, L, XL) × 5 màu đầu
        $selectedSizes = $sizes->whereIn('name', ['S', 'M', 'L', 'XL']); // 4 sizes
        $selectedColors = $colors->take(5); // 5 màu đầu: Black, White, Red, Blue, Green

        $basePrice = $product->price; // Giá gốc của sản phẩm

        foreach ($selectedSizes as $size) {
            foreach ($selectedColors as $color) {
                // Giá biến thể: một số giống product, một số khác để test
                $variantPrice = match($size->name) {
                    'S' => $basePrice - 10000,  // Rẻ hơn 10k
                    'M' => 0,                    // 0 = dùng giá product
                    'L' => $basePrice,           // Bằng giá product
                    'XL' => $basePrice + 20000,  // Đắt hơn 20k
                    default => $basePrice
                };

                // Stock khác nhau để test
                $stock = match($color->name) {
                    'Black' => 50,   // Nhiều
                    'White' => 30,   // Trung bình
                    'Red' => 10,     // Ít
                    'Blue' => 0,     // Hết hàng để test
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

        // Sử dụng upsert để idempotent (có thể chạy lại)
        DB::table('product_variants')->upsert(
            $variants,
            ['product_id', 'size_id', 'color_id'], // Unique keys
            ['price', 'stock', 'updated_at'] // Update columns nếu đã tồn tại
        );

        $this->command->info('✓ Đã tạo ' . count($variants) . ' variants cho sản phẩm: ' . $product->name);
    }
}
