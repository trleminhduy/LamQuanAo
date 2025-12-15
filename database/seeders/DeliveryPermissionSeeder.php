<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class DeliveryPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo permission manage_deliveries
        $permission = Permission::firstOrCreate([
            'name' => 'manage_deliveries',
            
        ]);

        // Tạo role delivery_user nếu chưa có
        $deliveryRole = Role::firstOrCreate([
            'name' => 'delivery_user',
            
        ]);

        // Gán permission cho role delivery_user
        if (!$deliveryRole->permissions()->where('permission_id', $permission->id)->exists()) {
            $deliveryRole->permissions()->attach($permission->id);
        }

        // Admin có tất cả quyền
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$adminRole->permissions()->where('permission_id', $permission->id)->exists()) {
            $adminRole->permissions()->attach($permission->id);
        }

        $this->command->info('Permission manage_deliveries created successfully!');
    }
}
