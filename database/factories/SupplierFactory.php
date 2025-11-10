<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        $companies = [
            'Việt Tiến',
            'May 10'
        ];

        $addresses = [
            'Số 7 Lê Minh Xuân, P.7, Q.Tân Bình, TP.HCM',
            '765 Nguyễn Văn Linh, Long Biên, Hà Nội'
        ];

        $descriptions = [
            'Tổng công ty may mặc hàng đầu Việt Nam, chuyên sản xuất và xuất khẩu các sản phẩm may mặc cao cấp',
            'Doanh nghiệp dệt may lớn tại Việt Nam, chuyên sản xuất và cung cấp trang phục công sở'
        ];

        $phones = [
            '028 38644326',
            '024 38257532'
        ];

        return [
            'name' => $this->faker->randomElement($companies),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->randomElement($phones),
            'address' => $this->faker->randomElement($addresses),
            'description' => $this->faker->randomElement($descriptions),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}