<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $colors = [
            ['name' => 'Black',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'White',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Red',    'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Blue',   'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Green',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Yellow', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gray',   'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pink',   'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Purple', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Orange', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('colors')->upsert($colors, ['name']);
    }
}
