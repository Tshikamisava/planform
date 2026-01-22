<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $adminRole = Role::where('name', 'admin')->first();
        $domRole = Role::where('name', 'dom')->first();
        $recipientRole = Role::where('name', 'recipient')->first();
        $authorRole = Role::where('name', 'author')->first();

        if (!$adminRole || !$domRole || !$recipientRole || !$authorRole) {
            $this->command->error('Roles not found. Please run RoleSeeder first.');
            return;
        }

        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@planform.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department' => 'IT',
                'position' => 'System Administrator',
                'is_active' => true,
            ]
        );

        // Assign admin role
        if (!$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id, [
                'assigned_by' => $admin->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }

        // Create or update decision maker user
        $dom = User::updateOrCreate(
            ['email' => 'dom@planform.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'John Decision Maker',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department' => 'Engineering',
                'position' => 'Engineering Manager',
                'is_active' => true,
            ]
        );

        if (!$dom->roles()->where('role_id', $domRole->id)->exists()) {
            $dom->roles()->attach($domRole->id, [
                'assigned_by' => $admin->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }

        // Create or update recipient user
        $recipient = User::updateOrCreate(
            ['email' => 'recipient@planform.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Sarah Recipient',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department' => 'Operations',
                'position' => 'Operations Specialist',
                'is_active' => true,
            ]
        );

        if (!$recipient->roles()->where('role_id', $recipientRole->id)->exists()) {
            $recipient->roles()->attach($recipientRole->id, [
                'assigned_by' => $admin->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }

        // Create or update author user
        $author = User::updateOrCreate(
            ['email' => 'author@planform.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Alex Author',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'department' => 'Engineering',
                'position' => 'Engineer',
                'is_active' => true,
            ]
        );

        if (!$author->roles()->where('role_id', $authorRole->id)->exists()) {
            $author->roles()->attach($authorRole->id, [
                'assigned_by' => $admin->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);
        }

        $this->command->info('User roles assigned successfully!');
        $this->command->info('Admin: admin@planform.com / password');
        $this->command->info('DOM: dom@planform.com / password');
        $this->command->info('Recipient: recipient@planform.com / password');
        $this->command->info('Author: author@planform.com / password');
    }
}
