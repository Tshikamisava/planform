<?php

namespace App\Enums;

enum Role: string
{
    case AUTHOR = 'author';
    case RECIPIENT = 'recipient';
    case DOM = 'dom';
    case ADMIN = 'admin';

    /**
     * Get the display name for the role
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::AUTHOR => 'Author',
            self::RECIPIENT => 'Recipient',
            self::DOM => 'Decision Maker',
            self::ADMIN => 'Administrator',
        };
    }

    /**
     * Get the role level for hierarchy
     */
    public function getLevel(): int
    {
        return match($this) {
            self::AUTHOR => 1,
            self::RECIPIENT => 2,
            self::DOM => 3,
            self::ADMIN => 4,
        };
    }

    /**
     * Get the role description
     */
    public function getDescription(): string
    {
        return match($this) {
            self::AUTHOR => 'Can create and submit change requests',
            self::RECIPIENT => 'Can execute approved changes',
            self::DOM => 'Can assess impact and approve/reject changes',
            self::ADMIN => 'Full system access and user management',
        };
    }

    /**
     * Check if this role can access admin functions
     */
    public function canAccessAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if this role can approve DCRs
     */
    public function canApproveDcr(): bool
    {
        return $this === self::DOM || $this === self::ADMIN;
    }

    /**
     * Check if this role can create DCRs
     */
    public function canCreateDcr(): bool
    {
        return $this === self::AUTHOR || $this === self::ADMIN;
    }

    /**
     * Check if this role can complete DCRs
     */
    public function canCompleteDcr(): bool
    {
        return $this === self::RECIPIENT || $this === self::ADMIN;
    }

    /**
     * Check if this role can access reports
     */
    public function canAccessReports(): bool
    {
        return $this === self::DOM || $this === self::ADMIN;
    }

    /**
     * Get all roles as array for select options
     */
    public static function getAllForSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($role) => [
            $role->value => $role->getDisplayName()
        ])->toArray();
    }

    /**
     * Get role hierarchy for permissions
     */
    public static function getHierarchy(): array
    {
        return [
            self::AUTHOR->value => [
                'permissions' => ['create_dcr', 'view_own_dcr', 'upload_documents'],
                'inherits' => []
            ],
            self::RECIPIENT->value => [
                'permissions' => ['view_assigned_dcr', 'complete_dcr', 'upload_documents'],
                'inherits' => [self::AUTHOR->value]
            ],
            self::DOM->value => [
                'permissions' => ['approve_dcr', 'reject_dcr', 'impact_assessment', 'view_all_dcr', 'access_reports'],
                'inherits' => [self::RECIPIENT->value, self::AUTHOR->value]
            ],
            self::ADMIN->value => [
                'permissions' => ['manage_users', 'manage_system', 'view_all_dcr', 'access_reports', 'approve_dcr', 'complete_dcr', 'delete_dcr'],
                'inherits' => [self::DOM->value, self::RECIPIENT->value, self::AUTHOR->value]
            ]
        ];
    }
}
