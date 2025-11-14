<?php

namespace Database\Seeders;

use App\Models\User;
use Attribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    
    public function run(): void
    {
        User::create([
            'name' => 'Nguyen Van A',
            'email'=> 'nguyenvana@example.com',
            'password'=> bcrypt('123456'),
            'phone_number' =>'0123456789',
            'status' => 'pending',
            'avatar' => '', 
            'address'=> 'Quan 8, HCM',
            'role_id'=> 1,
             'created_at'=>now(),
            'updated_at'=> now(),
        ]);

        User::create([
            'name' => 'Le Van B',
            'email'=> 'levanb@example.com',
            'password'=> bcrypt('123456'),
            'phone_number' =>'0123456789',
            'status' => 'pending',
            'avatar' => '', 
            'address'=> 'Chau Phu, An Giang',
            'role_id'=> 2,
             'created_at'=>now(),
            'updated_at'=> now(),
        ]);

       User::create([
            'name' => 'Tran Le Minh Duy',
            'email'=> 'minhduy@example.com',
            'password'=> bcrypt('123456'),
            'phone_number' =>'0123456789',
            'status' => 'pending',
            'avatar' => '', 
            'address'=> 'Q8, Ho Chi Minh',
            'role_id'=> 2,
             'created_at'=>now(),
            'updated_at'=> now(),
        ]);
    }
}
