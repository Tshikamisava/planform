<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EngineerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create engineer users
        $engineers = [
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@planform.com',
                'password' => Hash::make('password'),
                'uuid' => \Str::uuid(),
                'phone' => '+27823456789',
                'department' => 'Engineering',
                'position' => 'Senior Engineer',
                'is_active' => true,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@planform.com',
                'password' => Hash::make('password'),
                'uuid' => \Str::uuid(),
                'phone' => '+27829876543',
                'department' => 'Manufacturing',
                'position' => 'Design Engineer',
                'is_active' => true,
            ],
            [
                'name' => 'James Taylor',
                'email' => 'james.taylor@planform.com',
                'password' => Hash::make('password'),
                'uuid' => \Str::uuid(),
                'phone' => '+27825554321',
                'department' => 'Quality Assurance',
                'position' => 'QA Engineer',
                'is_active' => true,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@planform.com',
                'password' => Hash::make('password'),
                'uuid' => \Str::uuid(),
                'phone' => '+27821239876',
                'department' => 'Engineering',
                'position' => 'Process Engineer',
                'is_active' => true,
            ],
        ];

        // Get Recipient role
        $recipientRole = Role::where('name', 'recipient')->first();

        if (!$recipientRole) {
            $this->command->error('Recipient role not found! Please ensure roles are seeded first.');
            return;
        }

        foreach ($engineers as $engineerData) {
            // Create or update engineer user
            $engineer = User::firstOrCreate(
                ['email' => $engineerData['email']],
                $engineerData
            );

            // Assign Recipient role if not already assigned
            if (!$engineer->hasRole('recipient')) {
                $engineer->roles()->attach($recipientRole->id, [
                    'assigned_by' => $engineer->id,
                    'assigned_at' => now(),
                    'is_active' => true,
                ]);
            }

            $this->command->info("Engineer created: {$engineer->name} ({$engineer->email})");
        }

        $this->command->info('');
        $this->command->info('=== Engineer Accounts Created ===');
        $this->command->info('');
        $this->command->info('1. David Wilson (Senior Engineer - Engineering)');
        $this->command->info('   Email: david.wilson@planform.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('2. Emily Davis (Design Engineer - Manufacturing)');
        $this->command->info('   Email: emily.davis@planform.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('3. James Taylor (QA Engineer - Quality Assurance)');
        $this->command->info('   Email: james.taylor@planform.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('4. Lisa Anderson (Process Engineer - Engineering)');
        $this->command->info('   Email: lisa.anderson@planform.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('All accounts have Recipient role with complete_dcr permission.');
    }
}
