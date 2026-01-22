<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'uuid' => Str::uuid(),
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access and user management',
                'level' => 4,
                'is_system' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'dom',
                'display_name' => 'Decision Maker',
                'description' => 'Can assess impact and approve/reject changes',
                'level' => 3,
                'is_system' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'recipient',
                'display_name' => 'Recipient',
                'description' => 'Can execute approved changes',
                'level' => 2,
                'is_system' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'author',
                'display_name' => 'Author',
                'description' => 'Can create and submit change requests',
                'level' => 1,
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
