<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Change this to a secure password
                'uuid' => \Str::uuid(),
                'phone' => '+27123456789',
                'department' => 'IT',
                'position' => 'System Administrator',
                'is_active' => true,
            ]
        );

        // Assign admin role if not already assigned
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$adminUser->hasRole('admin')) {
            $adminUser->roles()->attach($adminRole->id, [
                'assigned_by' => $adminUser->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
    }
}
