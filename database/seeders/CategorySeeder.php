<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Nam',
                'slug' => 'do-nam',
                'description' => 'Thời trang nam: áo thun, áo sơ mi, quần jean, quần tây, áo khoác,....',
                'image' => 'uploads/categories/suit2.png'
            ],
            [
                'name' => 'Nữ',
                'slug' => 'do-nu',
                'description' => 'Thời trang nữ: đầm, áo kiểu, chân váy, quần jean, quần tây, áo khoác,....',
                'image' => 'uploads/categories/suit2.png'
            ],
            [
                'name' => 'Unisex',
                'slug' => 'do-unisex',
                'description' => 'Thời trang unisex phù hợp mọi giới tính',
                'image' => 'uploads/categories/suit2.png'
            ],
            [
                'name' => 'Áo',
                'slug' => 'ao',
                'description' => 'Danh mục các loại áo: áo thun, áo sơ mi, áo khoác,....',
                'image' => 'uploads/categories/suit2.png'
            ],
            [
                'name' => 'Quần',
                'slug' => 'quan',
                'description' => 'Danh mục các loại quần: quần jean, quần tây, quần short,....',
                'image' => 'uploads/categories/suit2.png'
            ],
            [
                'name' => 'Phụ kiện',
                'slug' => 'phu-kien',
                'description' => 'Danh mục các loại phụ kiện: nón, giày, túi xách,....',
                'image' => 'uploads/categories/suit2.png'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
