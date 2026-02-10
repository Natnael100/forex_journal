<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists to avoid duplicates
        $admin = User::firstOrCreate(
            ['email' => 'admin@forexjournal.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'is_active' => true,
                'verification_status' => 'verified',
            ]
        );

        // Ensure user has admin role
        if (!$admin->hasRole(UserRole::ADMIN->value)) {
            $admin->assignRole(UserRole::ADMIN->value);
        }

        $this->command->info('Admin user verified and role assigned.');
    }
}
