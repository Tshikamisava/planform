<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $authorRole = Role::where('name', 'author')->first();
        $recipientRole = Role::where('name', 'recipient')->first();
        $domRole = Role::where('name', 'dom')->first();

        // Sample users for each role
        $users = [
            [
                'name' => 'John Author',
                'email' => 'john.author@example.com',
                'role' => $authorRole,
                'phone' => '+27123456780',
                'department' => 'Finance',
                'position' => 'Financial Analyst',
            ],
            [
                'name' => 'Jane Author',
                'email' => 'jane.author@example.com',
                'role' => $authorRole,
                'phone' => '+27123456781',
                'department' => 'Operations',
                'position' => 'Operations Manager',
            ],
            [
                'name' => 'Mike Recipient',
                'email' => 'mike.recipient@example.com',
                'role' => $recipientRole,
                'phone' => '+27123456782',
                'department' => 'IT',
                'position' => 'System Administrator',
            ],
            [
                'name' => 'Sarah Recipient',
                'email' => 'sarah.recipient@example.com',
                'role' => $recipientRole,
                'phone' => '+27123456783',
                'department' => 'Finance',
                'position' => 'Finance Manager',
            ],
            [
                'name' => 'David DOM',
                'email' => 'david.dom@example.com',
                'role' => $domRole,
                'phone' => '+27123456784',
                'department' => 'IT',
                'position' => 'IT Director',
            ],
            [
                'name' => 'Lisa DOM',
                'email' => 'lisa.dom@example.com',
                'role' => $domRole,
                'phone' => '+27123456785',
                'department' => 'Operations',
                'position' => 'Operations Director',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'uuid' => \Str::uuid(),
                    'phone' => $userData['phone'],
                    'department' => $userData['department'],
                    'position' => $userData['position'],
                    'is_active' => true,
                ]
            );

            // Assign role if not already assigned
            if ($userData['role'] && !$user->hasRole($userData['role']->name)) {
                $user->roles()->attach($userData['role']->id, [
                    'assigned_by' => 1, // Admin user
                    'assigned_at' => now(),
                    'is_active' => true,
                ]);
            }

            $this->command->info("Created user: {$userData['name']} ({$userData['email']})");
        }

        $this->command->info('Sample users created successfully!');
        $this->command->info('All users have password: password');
    }
}
