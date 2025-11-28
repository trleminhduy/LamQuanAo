<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AddMissingPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $newPermissions = [
            'manage_variants',
            'manage_suppliers',
        ];

        foreach ($newPermissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $this->command->info('Đã thêm permissions: manage_variants, manage_suppliers');
    }
}
