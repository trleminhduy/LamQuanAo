<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesPermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        
        $allPermissions = [
            'manage_users',
            'manage_products',
            'manage_orders',
            'manage_categories',
            'manage_contacts',
            'manage_variants',
            'manage_suppliers',
            'manage_deliveries',
            'manage_coupons',
            'manage_refunds',
        ];

        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

      

        
        $admin = Role::where('name', 'admin')->first();
        $staff = Role::where('name', 'staff')->first();
        $customer = Role::where('name', 'customer')->first();
        $delivery = Role::where('name', 'delivery_user')->first();

        
        if (!$delivery) {
            $delivery = Role::create(['name' => 'delivery_user']);
         
        }

        
        $adminPermissions = Permission::pluck('id')->toArray();
        $admin->permissions()->sync($adminPermissions);
       

        
        $staffPermissions = Permission::whereIn('name', [
            'manage_products',
            'manage_orders'
        ])->pluck('id')->toArray();
        
        $staff->permissions()->sync($staffPermissions);
       
        $deliveryPermissions = Permission::whereIn('name', [
            'manage_deliveries'
        ])->pluck('id')->toArray();
        $delivery->permissions()->sync($deliveryPermissions);
        

        // khhang - Không có quyền gì
        $customer->permissions()->sync([]);
       
    }
}
