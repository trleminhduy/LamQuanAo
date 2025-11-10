<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            [
                'name' => 'Áo thun nam basic',
                'price' => [79000, 99000, 129000]
            ],
            [
                'name' => 'Áo sơ mi nữ công sở',
                'price' => [159000, 189000, 219000]
            ],
            [
                'name' => 'Quần jean unisex',
                'price' => [259000, 299000, 359000]
            ],
            [
                'name' => 'Áo hoodie nam form rộng',
                'price' => [199000, 249000, 299000]
            ],
            [
                'name' => 'Váy suông nữ thanh lịch',
                'price' => [179000, 229000, 279000]
            ],
            [
                'name' => 'Quần short nam thể thao',
                'price' => [129000, 159000, 189000]
            ],
            [
                'name' => 'Áo khoác bomber unisex',
                'price' => [359000, 399000, 459000]
            ],
            [
                'name' => 'Áo len nữ mùa đông',
                'price' => [199000, 249000, 299000]
            ],
            [
                'name' => 'Quần tây nam cao cấp',
                'price' => [279000, 329000, 379000]
            ],
            [
                'name' => 'Áo polo unisex',
                'price' => [159000, 189000, 219000]
            ],
        ];

        $product = $this->faker->randomElement($products);
        $name = $product['name'];
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 100),
            'category_id' => $this->getCategoryIdByProductName($name),
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
            'description' => 'Chất liệu cotton cao cấp, form dáng hiện đại, phù hợp với nhiều độ tuổi. Sản phẩm được sản xuất tại Việt Nam với quy trình kiểm soát chất lượng nghiêm ngặt.',
            'price' => $this->faker->randomElement($product['price']),
            'stock' => $this->faker->numberBetween(10, 100),
        ];
    }

    private function getCategoryIdByProductName(string $productName): int
    {
        if ($this->containsAny($productName, ['Áo', 'Hoodie', 'Polo', 'Len'])) {
            return Category::where('slug', 'ao')->first()->id;
        }

        if ($this->containsAny($productName, ['Quần', 'Jean', 'Short', 'Tây'])) {
            return Category::where('slug', 'quan')->first()->id;
        }

        if ($this->containsAny($productName, ['Bomber', 'Phụ kiện'])) {
            return Category::where('slug', 'phu-kien')->first()->id;
        }

        return Category::inRandomOrder()->first()->id; // Fallback
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }
}

