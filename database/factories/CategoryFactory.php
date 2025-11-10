<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $categories = [
            [
                'image' => 'uploads/categories/suit2.png',
                'name' => 'Áo Nam',
                'description' => 'Các loại áo dành cho nam giới bao gồm áo thun, áo sơ mi, áo khoác, áo polo'
            ],
            [
                'image' => 'uploads/categories/suit2.png',
                'name' => 'Quần Nam',
                'description' => 'Các loại quần dành cho nam giới bao gồm quần jean, quần tây, quần short'
            ],
            [
                'image' => 'uploads/categories/suit2.png',
                'name' => 'Áo Nữ',
                'description' => 'Các loại áo dành cho nữ giới bao gồm áo kiểu, áo sơ mi, áo thun, áo khoác'
            ],
            [
                'image' => 'uploads/categories/suit2.png',
                'name' => 'Quần Nữ',
                'description' => 'Các loại quần dành cho nữ giới bao gồm quần jean, quần tây, quần short'
            ],
            [
                'image' => 'uploads/categories/suit2.png',
                'name' => 'Váy Đầm',
                'description' => 'Các loại váy và đầm thời trang dành cho nữ giới'
            ],
            [
                'image' => 'uploads/categories/suit2.png',
                'name' => 'Đồ Thể Thao',
                'description' => 'Trang phục thể thao cho cả nam và nữ'
            ],
            [
                'image' => 'uploads/categories/suit2.png',
                'name' => 'Khác',
                'description' => 'Các loại sản phẩm khác'
            ],
        ];

        $category = $this->faker->unique()->randomElement($categories);
        $name = $category['name'];

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $category['description'],
            'image' => $category['image'], 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}