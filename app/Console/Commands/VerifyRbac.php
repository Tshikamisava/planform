<?php

namespace App\Console\Commands;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use App\Models\Dcr;
use App\Services\PermissionService;
use Illuminate\Console\Command;

class VerifyRbac extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:verify {--detailed : Show detailed permission checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify RBAC implementation for all roles (Author, Recipient, DOM, Admin)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 RBAC Verification Report');
        $this->info('====================================');
        $this->newLine();

        // Check if roles exist
        $this->info('📋 Step 1: Checking Role Configuration...');
        $this->verifyRoles();
        $this->newLine();

        // Check users for each role
        $this->info('👥 Step 2: Checking User Assignments...');
        $this->verifyUsers();
        $this->newLine();

        // Check permissions for each role
        $this->info('🔑 Step 3: Checking Role Permissions...');
        $this->verifyPermissions();
        $this->newLine();

        // Test DCR access control
        $this->info('📝 Step 4: Testing DCR Access Control...');
        $this->verifyDcrAccess();
        $this->newLine();

        // Summary
        $this->info('✅ RBAC Verification Complete!');
        $this->newLine();
        
        return Command::SUCCESS;
    }

    protected function verifyRoles()
    {
        $roles = \App\Models\Role::all();
        
        if ($roles->isEmpty()) {
            $this->error('❌ No roles found in database!');
            $this->warn('   Run: php artisan db:seed --class=RoleSeeder');
            return;
        }

        $this->table(
            ['Role Name', 'Display Name', 'Level', 'Status'],
            $roles->map(function ($role) {
                return [
                    $role->name,
                    $role->display_name,
                    $role->level,
                    '✓ Active'
                ];
            })
        );

        // Check if all required roles exist
        $requiredRoles = ['author', 'recipient', 'dom', 'admin'];
        $existingRoles = $roles->pluck('name')->toArray();
        $missingRoles = array_diff($requiredRoles, $existingRoles);

        if (empty($missingRoles)) {
            $this->info('   ✓ All required roles exist');
        } else {
            $this->error('   ❌ Missing roles: ' . implode(', ', $missingRoles));
        }
    }

    protected function verifyUsers()
    {
        $rolesData = [];

        foreach (['author', 'recipient', 'dom', 'admin'] as $roleName) {
            $users = User::whereHas('activeRoles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })->get();

            $rolesData[] = [
                'role' => ucfirst($roleName),
                'count' => $users->count(),
                'examples' => $users->take(3)->pluck('email')->implode(', ') ?: 'None',
                'status' => $users->count() > 0 ? '✓' : '❌'
            ];
        }

        $this->table(
            ['Role', 'User Count', 'Example Users', 'Status'],
            $rolesData
        );

        // Check for users without roles
        $usersWithoutRoles = User::whereDoesntHave('activeRoles')->count();
        if ($usersWithoutRoles > 0) {
            $this->warn("   ⚠ {$usersWithoutRoles} users have no active roles");
        }
    }

    protected function verifyPermissions()
    {
        $permissionData = [];

        foreach ([Role::AUTHOR, Role::RECIPIENT, Role::DOM, Role::ADMIN] as $role) {
            $permissions = Permission::getForRole($role);
            
            $keyPermissions = array_filter($permissions, function ($perm) {
                return in_array($perm, [
                    Permission::CREATE_DCR,
                    Permission::APPROVE_DCR,
                    Permission::COMPLETE_DCR,
                    Permission::VIEW_ALL_DCR,
                    Permission::MANAGE_USERS,
                    Permission::DELETE_DCR
                ]);
            });

            $permissionData[] = [
                'role' => $role->getDisplayName(),
                'total' => count($permissions),
                'key_perms' => implode(', ', array_map(fn($p) => $p->value, $keyPermissions)),
            ];
        }

        $this->table(
            ['Role', 'Total Permissions', 'Key Permissions'],
            $permissionData
        );

        if ($this->option('detailed')) {
            $this->newLine();
            $this->info('   📄 Detailed Permission Breakdown:');
            foreach ([Role::AUTHOR, Role::RECIPIENT, Role::DOM, Role::ADMIN] as $role) {
                $permissions = Permission::getForRole($role);
                $this->line("   {$role->getDisplayName()}: " . count($permissions) . " permissions");
                foreach ($permissions as $perm) {
                    $this->line("      - {$perm->value}");
                }
                $this->newLine();
            }
        }
    }

    protected function verifyDcrAccess()
    {
        // Get sample users for each role
        $author = User::whereHas('activeRoles', fn($q) => $q->where('name', 'author'))->first();
        $recipient = User::whereHas('activeRoles', fn($q) => $q->where('name', 'recipient'))->first();
        $dom = User::whereHas('activeRoles', fn($q) => $q->where('name', 'dom'))->first();
        $admin = User::whereHas('activeRoles', fn($q) => $q->where('name', 'admin'))->first();

        $accessData = [];

        // Test Author
        if ($author) {
            $ownDcr = Dcr::where('author_id', $author->id)->first();
            $otherDcr = Dcr::where('author_id', '!=', $author->id)->first();
            
            $accessData[] = [
                'role' => 'Author',
                'can_create' => $author->isAuthor() ? '✓' : '❌',
                'can_view_own' => $ownDcr && PermissionService::hasPermission($author, Permission::VIEW_OWN_DCR) ? '✓' : 'N/A',
                'can_approve' => PermissionService::hasPermission($author, Permission::APPROVE_DCR) ? '✓' : '❌',
                'can_complete' => PermissionService::hasPermission($author, Permission::COMPLETE_DCR) ? '✓' : '❌',
            ];
        }

        // Test Recipient
        if ($recipient) {
            $assignedDcr = Dcr::where('recipient_id', $recipient->id)->first();
            
            $accessData[] = [
                'role' => 'Recipient',
                'can_create' => PermissionService::hasPermission($recipient, Permission::CREATE_DCR) ? '✓' : '❌',
                'can_view_own' => 'N/A',
                'can_approve' => PermissionService::hasPermission($recipient, Permission::APPROVE_DCR) ? '✓' : '❌',
                'can_complete' => PermissionService::hasPermission($recipient, Permission::COMPLETE_DCR) ? '✓' : '❌',
            ];
        }

        // Test DOM
        if ($dom) {
            $accessData[] = [
                'role' => 'DOM',
                'can_create' => PermissionService::hasPermission($dom, Permission::CREATE_DCR) ? '✓' : '❌',
                'can_view_own' => 'N/A',
                'can_approve' => PermissionService::hasPermission($dom, Permission::APPROVE_DCR) ? '✓' : '❌',
                'can_complete' => PermissionService::hasPermission($dom, Permission::COMPLETE_DCR) ? '✓' : '❌',
            ];
        }

        // Test Admin
        if ($admin) {
            $accessData[] = [
                'role' => 'Admin',
                'can_create' => PermissionService::hasPermission($admin, Permission::CREATE_DCR) ? '✓' : '❌',
                'can_view_own' => 'N/A',
                'can_approve' => PermissionService::hasPermission($admin, Permission::APPROVE_DCR) ? '✓' : '❌',
                'can_complete' => PermissionService::hasPermission($admin, Permission::COMPLETE_DCR) ? '✓' : '❌',
            ];
        }

        $this->table(
            ['Role', 'Create DCR', 'View Own', 'Approve', 'Complete'],
            $accessData
        );

        // Check DCR policy
        $dcr = Dcr::first();
        if ($dcr) {
            $this->newLine();
            $this->info('   🔍 Policy Authorization Test (Sample DCR):');
            $this->line("   DCR: {$dcr->reference_number} (Status: {$dcr->status})");
            
            if ($author) {
                $canView = $author->can('view', $dcr);
                $canUpdate = $author->can('update', $dcr);
                $this->line("   Author: View={$this->bool($canView)}, Update={$this->bool($canUpdate)}");
            }
            
            if ($recipient) {
                $canView = $recipient->can('view', $dcr);
                $canComplete = $recipient->can('complete', $dcr);
                $this->line("   Recipient: View={$this->bool($canView)}, Complete={$this->bool($canComplete)}");
            }
            
            if ($dom) {
                $canView = $dom->can('view', $dcr);
                $canApprove = $dom->can('approve', $dcr);
                $this->line("   DOM: View={$this->bool($canView)}, Approve={$this->bool($canApprove)}");
            }
            
            if ($admin) {
                $canView = $admin->can('view', $dcr);
                $canDelete = $admin->can('delete', $dcr);
                $this->line("   Admin: View={$this->bool($canView)}, Delete={$this->bool($canDelete)}");
            }
        }
    }

    protected function bool($value): string
    {
        return $value ? '✓' : '❌';
    }
}
