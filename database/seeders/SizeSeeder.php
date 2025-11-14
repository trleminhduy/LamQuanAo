<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SizeSeeder extends Seeder
{
    
    public function run(): void
    {
        $now = now();
        $sizes = [
            ['name' => 'S',   'created_at' => $now, 'updated_at' => $now],
            ['name' => 'M',   'created_at' => $now, 'updated_at' => $now],
            ['name' => 'L',   'created_at' => $now, 'updated_at' => $now],
            ['name' => 'XL',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'XXL', 'created_at' => $now, 'updated_at' => $now],
        ];

        
        DB::table('sizes')->upsert($sizes, ['name']);
    }
}
