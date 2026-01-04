<?php

namespace App\Enums;

enum Permission: string
{
    // DCR Permissions
    case CREATE_DCR = 'create_dcr';
    case VIEW_OWN_DCR = 'view_own_dcr';
    case VIEW_ASSIGNED_DCR = 'view_assigned_dcr';
    case VIEW_ALL_DCR = 'view_all_dcr';
    case EDIT_OWN_DCR = 'edit_own_dcr';
    case APPROVE_DCR = 'approve_dcr';
    case REJECT_DCR = 'reject_dcr';
    case COMPLETE_DCR = 'complete_dcr';
    case CLOSE_DCR = 'close_dcr';
    case DELETE_DCR = 'delete_dcr';
    case ESCALATE_DCR = 'escalate_dcr';

    // Impact Assessment Permissions
    case CREATE_ASSESSMENT = 'create_assessment';
    case VIEW_ASSESSMENT = 'view_assessment';
    case EDIT_ASSESSMENT = 'edit_assessment';

    // Document Permissions
    case UPLOAD_DOCUMENTS = 'upload_documents';
    case VIEW_DOCUMENTS = 'view_documents';
    case DELETE_DOCUMENTS = 'delete_documents';

    // User Management Permissions
    case MANAGE_USERS = 'manage_users';
    case VIEW_USERS = 'view_users';
    case ASSIGN_ROLES = 'assign_roles';

    // System Permissions
    case MANAGE_SYSTEM = 'manage_system';
    case ACCESS_REPORTS = 'access_reports';
    case VIEW_AUDIT_LOGS = 'view_audit_logs';
    case MANAGE_NOTIFICATIONS = 'manage_notifications';

    /**
     * Get the display name for the permission
     */
    public function getDisplayName(): string
    {
        return match($this) {
            self::CREATE_DCR => 'Create DCR',
            self::VIEW_OWN_DCR => 'View Own DCR',
            self::VIEW_ASSIGNED_DCR => 'View Assigned DCR',
            self::VIEW_ALL_DCR => 'View All DCR',
            self::EDIT_OWN_DCR => 'Edit Own DCR',
            self::APPROVE_DCR => 'Approve DCR',
            self::REJECT_DCR => 'Reject DCR',
            self::COMPLETE_DCR => 'Complete DCR',
            self::CLOSE_DCR => 'Close DCR',
            self::DELETE_DCR => 'Delete DCR',
            self::ESCALATE_DCR => 'Escalate DCR',
            self::CREATE_ASSESSMENT => 'Create Assessment',
            self::VIEW_ASSESSMENT => 'View Assessment',
            self::EDIT_ASSESSMENT => 'Edit Assessment',
            self::UPLOAD_DOCUMENTS => 'Upload Documents',
            self::VIEW_DOCUMENTS => 'View Documents',
            self::DELETE_DOCUMENTS => 'Delete Documents',
            self::MANAGE_USERS => 'Manage Users',
            self::VIEW_USERS => 'View Users',
            self::ASSIGN_ROLES => 'Assign Roles',
            self::MANAGE_SYSTEM => 'Manage System',
            self::ACCESS_REPORTS => 'Access Reports',
            self::VIEW_AUDIT_LOGS => 'View Audit Logs',
            self::MANAGE_NOTIFICATIONS => 'Manage Notifications',
        };
    }

    /**
     * Get the permission category
     */
    public function getCategory(): string
    {
        return match($this) {
            self::CREATE_DCR, self::VIEW_OWN_DCR, self::VIEW_ASSIGNED_DCR, self::VIEW_ALL_DCR,
            self::EDIT_OWN_DCR, self::APPROVE_DCR, self::REJECT_DCR, self::COMPLETE_DCR,
            self::CLOSE_DCR, self::DELETE_DCR, self::ESCALATE_DCR => 'DCR',
            self::CREATE_ASSESSMENT, self::VIEW_ASSESSMENT, self::EDIT_ASSESSMENT => 'Assessment',
            self::UPLOAD_DOCUMENTS, self::VIEW_DOCUMENTS, self::DELETE_DOCUMENTS => 'Document',
            self::MANAGE_USERS, self::VIEW_USERS, self::ASSIGN_ROLES => 'User',
            self::MANAGE_SYSTEM, self::ACCESS_REPORTS, self::VIEW_AUDIT_LOGS, self::MANAGE_NOTIFICATIONS => 'System',
        };
    }

    /**
     * Get all permissions grouped by category
     */
    public static function getAllGroupedByCategory(): array
    {
        $permissions = [];
        foreach (self::cases() as $permission) {
            $category = $permission->getCategory();
            $permissions[$category][$permission->value] = $permission->getDisplayName();
        }
        return $permissions;
    }

    /**
     * Get permissions for a specific role
     */
    public static function getForRole(Role $role): array
    {
        return match($role) {
            Role::AUTHOR => [
                self::CREATE_DCR,
                self::VIEW_OWN_DCR,
                self::EDIT_OWN_DCR,
                self::UPLOAD_DOCUMENTS,
                self::VIEW_DOCUMENTS,
            ],
            Role::RECIPIENT => [
                self::VIEW_ASSIGNED_DCR,
                self::COMPLETE_DCR,
                self::UPLOAD_DOCUMENTS,
                self::VIEW_DOCUMENTS,
                // Inherit from Author
                self::CREATE_DCR,
                self::VIEW_OWN_DCR,
                self::EDIT_OWN_DCR,
            ],
            Role::DOM => [
                self::APPROVE_DCR,
                self::REJECT_DCR,
                self::CREATE_ASSESSMENT,
                self::VIEW_ASSESSMENT,
                self::VIEW_ALL_DCR,
                self::ACCESS_REPORTS,
                self::ESCALATE_DCR,
                // Inherit from Recipient and Author
                self::VIEW_ASSIGNED_DCR,
                self::COMPLETE_DCR,
                self::UPLOAD_DOCUMENTS,
                self::VIEW_DOCUMENTS,
                self::CREATE_DCR,
                self::VIEW_OWN_DCR,
                self::EDIT_OWN_DCR,
            ],
            Role::ADMIN => [
                self::MANAGE_USERS,
                self::VIEW_USERS,
                self::ASSIGN_ROLES,
                self::MANAGE_SYSTEM,
                self::VIEW_AUDIT_LOGS,
                self::MANAGE_NOTIFICATIONS,
                self::DELETE_DCR,
                self::CLOSE_DCR,
                // Inherit all from DOM
                self::APPROVE_DCR,
                self::REJECT_DCR,
                self::CREATE_ASSESSMENT,
                self::VIEW_ASSESSMENT,
                self::VIEW_ALL_DCR,
                self::ACCESS_REPORTS,
                self::ESCALATE_DCR,
                self::VIEW_ASSIGNED_DCR,
                self::COMPLETE_DCR,
                self::UPLOAD_DOCUMENTS,
                self::VIEW_DOCUMENTS,
                self::DELETE_DOCUMENTS,
                self::CREATE_DCR,
                self::VIEW_OWN_DCR,
                self::EDIT_OWN_DCR,
            ],
        };
    }
}
