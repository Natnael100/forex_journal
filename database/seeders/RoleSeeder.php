<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => UserRole::ADMIN->value]);
        $traderRole = Role::create(['name' => UserRole::TRADER->value]);
        $analystRole = Role::create(['name' => UserRole::ANALYST->value]);

        // Create permissions for different modules
        $permissions = [
            // User management
            'manage users',
            'view users',
            
            // Trade management
            'create trades',
            'edit own trades',
            'delete own trades',
            'view own trades',
            'view all trades',
            
            // Feedback management
            'create feedback',
            'edit feedback',
            'delete feedback',
            'view feedback',
            
            // Analytics
            'view own analytics',
            'view all analytics',
            
            // System
            'manage system',
            'view logs',
            'manage backups',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to Admin (all permissions)
        $adminRole->givePermissionTo(Permission::all());

        // Assign permissions to Trader
        $traderRole->givePermissionTo([
            'create trades',
            'edit own trades',
            'delete own trades',
            'view own trades',
            'view own analytics',
            'view feedback',
        ]);

        // Assign permissions to Analyst
        $analystRole->givePermissionTo([
            'view all trades',
            'create feedback',
            'edit feedback',
            'delete feedback',
            'view feedback',
            'view all analytics',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('✓ Admin role with all permissions');
        $this->command->info('✓ Trader role with trade management permissions');
        $this->command->info('✓ Analyst role with feedback and analytics permissions');
    }
}
