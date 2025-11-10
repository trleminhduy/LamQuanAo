<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesPermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('name', 'admin')->first();
        $staff = Role::where('name', 'staff')->first();
        $customer = Role::where('name', 'customer')->first();

        $permissions = Permission::pluck('id')->toArray();

        // Gán toàn quyền cho admin
        $admin->permissions()->sync($permissions);

        // Nhân viên chỉ có quyền quản lý sản phẩm và đơn hàng
        $staff->permissions()->sync(
            Permission::whereIn('name', ['manage_products', 'manage_orders'])->pluck('id')->toArray()
        );

        // Khách hàng không có quyền gì trong bảng permission
        $customer->permissions()->sync([]);
    }
}
