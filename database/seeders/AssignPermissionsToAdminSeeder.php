<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class AssignPermissionsToAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy user admin (có thể là user đầu tiên hoặc user có role admin)
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->first();
        
        if (!$admin || !$admin->role) {
            $this->command->error('Không tìm thấy admin user hoặc role!');
            return;
        }

        $variantsPermission = Permission::where('name', 'manage_variants')->first();
        $suppliersPermission = Permission::where('name', 'manage_suppliers')->first();

        if ($variantsPermission) {
            $admin->role->permissions()->syncWithoutDetaching([$variantsPermission->id]);
        }

        if ($suppliersPermission) {
            $admin->role->permissions()->syncWithoutDetaching([$suppliersPermission->id]);
        }

        $this->command->info('Đã gán permissions manage_variants và manage_suppliers cho role: ' . $admin->role->name);
    }
}
