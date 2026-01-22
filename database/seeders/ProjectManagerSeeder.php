<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProjectManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create project manager users
        $managers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@planform.com',
                'password' => Hash::make('password'),
                'uuid' => \Str::uuid(),
                'phone' => '+27821234567',
                'department' => 'Engineering',
                'position' => 'Senior Project Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@planform.com',
                'password' => Hash::make('password'),
                'uuid' => \Str::uuid(),
                'phone' => '+27829876543',
                'department' => 'Operations',
                'position' => 'Project Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@planform.com',
                'password' => Hash::make('password'),
                'uuid' => \Str::uuid(),
                'phone' => '+27825551234',
                'department' => 'Manufacturing',
                'position' => 'Lead Project Manager',
                'is_active' => true,
            ],
        ];

        // Get DOM (Decision Maker) role
        $domRole = Role::where('name', 'dom')->first();

        if (!$domRole) {
            $this->command->error('DOM role not found! Please ensure roles are seeded first.');
            return;
        }

        foreach ($managers as $managerData) {
            // Create or update manager user
            $manager = User::firstOrCreate(
                ['email' => $managerData['email']],
                $managerData
            );

            // Assign DOM role if not already assigned
            if (!$manager->hasRole('dom')) {
                $manager->roles()->attach($domRole->id, [
                    'assigned_by' => $manager->id,
                    'assigned_at' => now(),
                    'is_active' => true,
                ]);
            }

            $this->command->info("Project Manager created: {$manager->name} ({$manager->email})");
        }

        $this->command->info('');
        $this->command->info('=== Project Manager Accounts Created ===');
        $this->command->info('');
        $this->command->info('1. John Smith (Senior PM - Engineering)');
        $this->command->info('   Email: john.smith@planform.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('2. Sarah Johnson (PM - Operations)');
        $this->command->info('   Email: sarah.johnson@planform.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('3. Michael Brown (Lead PM - Manufacturing)');
        $this->command->info('   Email: michael.brown@planform.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('All accounts have DOM (Decision Maker) role with approve_dcr permission.');
    }
}
