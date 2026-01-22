# Role and Permission System Documentation

## Overview

The DCR Management System implements a comprehensive Role-Based Access Control (RBAC) system that controls user access to features and data. This system uses roles and permissions to ensure that users can only perform actions appropriate to their position in the organization.

## Roles

The system defines four hierarchical roles:

### 1. Author (Level 1)
- **Description**: Can create and submit change requests
- **Permissions**:
  - Create DCRs
  - View own DCRs
  - Edit own DCRs (before approval)
  - Upload documents
  - View documents

### 2. Recipient (Level 2)
- **Description**: Can execute approved changes
- **Permissions**:
  - View assigned DCRs
  - Complete DCRs
  - All Author permissions (inherited)

### 3. DOM - Decision Maker (Level 3)
- **Description**: Can assess impact and approve/reject changes
- **Permissions**:
  - Approve DCRs
  - Reject DCRs
  - Create impact assessments
  - View all DCRs
  - Access reports
  - Escalate DCRs
  - All Recipient and Author permissions (inherited)

### 4. Admin (Level 4)
- **Description**: Full system access and user management
- **Permissions**:
  - Manage users
  - Assign roles
  - Manage system settings
  - View audit logs
  - Delete DCRs
  - Close DCRs
  - All DOM, Recipient, and Author permissions (inherited)

## Permission Categories

### DCR Permissions
- `create_dcr` - Create new change requests
- `view_own_dcr` - View own DCRs
- `view_assigned_dcr` - View assigned DCRs
- `view_all_dcr` - View all DCRs in the system
- `edit_own_dcr` - Edit own DCRs
- `approve_dcr` - Approve change requests
- `reject_dcr` - Reject change requests
- `complete_dcr` - Mark DCRs as complete
- `close_dcr` - Close DCRs
- `delete_dcr` - Delete DCRs
- `escalate_dcr` - Escalate DCRs to higher authority

### Assessment Permissions
- `create_assessment` - Create impact assessments
- `view_assessment` - View assessments
- `edit_assessment` - Edit assessments

### Document Permissions
- `upload_documents` - Upload attachments
- `view_documents` - View documents
- `delete_documents` - Delete documents

### User Management Permissions
- `manage_users` - Create, edit, delete users
- `view_users` - View user list
- `assign_roles` - Assign/remove user roles

### System Permissions
- `manage_system` - Access system settings
- `access_reports` - View and generate reports
- `view_audit_logs` - View system audit logs
- `manage_notifications` - Manage notification settings

## Usage in Code

### Middleware

#### Protect routes by role:
```php
Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware('role:admin');

// Multiple roles
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('role:admin,dom');
```

#### Protect routes by permission:
```php
Route::post('/dcr', [DcrController::class, 'store'])
    ->middleware('permission:create_dcr');

// Multiple permissions (user needs ANY one)
Route::get('/reports', [ReportController::class, 'index'])
    ->middleware('permission:access_reports,view_audit_logs');
```

### Controller Methods

```php
// Check if user has a role
if (Auth::user()->hasRole('admin')) {
    // Admin-only code
}

// Check if user has any of multiple roles
if (Auth::user()->hasAnyRole(['admin', 'dom'])) {
    // Code for admins or decision makers
}

// Check specific permissions
if (Auth::user()->canApproveDcr()) {
    // Approval code
}

if (Auth::user()->canViewDcr($dcr)) {
    // View DCR code
}
```

### Blade Templates

```blade
{{-- Check for specific role --}}
@role('admin')
    <a href="{{ route('admin.users.index') }}">Manage Users</a>
@endrole

{{-- Check for any of multiple roles --}}
@anyrole(['admin', 'dom'])
    <a href="{{ route('reports.dashboard') }}">Reports</a>
@endanyrole

{{-- Check for specific permission --}}
@permission('create_dcr')
    <a href="{{ route('dcr.create') }}" class="btn btn-primary">Create DCR</a>
@endpermission

{{-- Shorthand for common roles --}}
@admin
    <button>Admin Panel</button>
@endadmin

@dom
    <button>Approval Dashboard</button>
@enddom

@author
    <button>My DCRs</button>
@endauthor

@recipient
    <button>My Tasks</button>
@endrecipient
```

## User Model Methods

The User model includes these helpful methods:

```php
// Role checking
$user->hasRole('admin')
$user->hasAnyRole(['admin', 'dom'])
$user->isAdministrator()
$user->isDecisionMaker()
$user->isRecipient()
$user->isAuthor()

// Permission checking
$user->hasPermission(Permission::CREATE_DCR)
$user->hasAnyPermission([Permission::APPROVE_DCR, Permission::REJECT_DCR])
$user->hasAllPermissions([Permission::CREATE_DCR, Permission::VIEW_OWN_DCR])

// Specific action checks
$user->canApproveDcr()
$user->canRejectDcr()
$user->canDeleteDcr()
$user->canManageUsers()
$user->canAccessReports()
$user->canViewDcr($dcr)
$user->canEditDcr($dcr)

// Cache management
$user->clearRoleCache() // Clear cached role/permission data
```

## Database Schema

### roles table
- `id` - Primary key
- `uuid` - Unique identifier
- `name` - Role name (unique)
- `display_name` - Human-readable name
- `description` - Role description
- `level` - Hierarchy level (1-4)
- `is_system` - System role flag

### user_roles pivot table
- `user_id` - Foreign key to users
- `role_id` - Foreign key to roles
- `assigned_by` - User who assigned the role
- `assigned_at` - When role was assigned
- `expires_at` - Optional expiration date
- `is_active` - Active status flag

## Test Users

After running the seeders, these test accounts are available:

| Email | Password | Role | Description |
|-------|----------|------|-------------|
| admin@planform.com | password | Admin | Full system access |
| dom@planform.com | password | DOM | Decision maker |
| recipient@planform.com | password | Recipient | Operations specialist |
| author@planform.com | password | Author | Engineer |

## Admin Features

Admins can access the User Management interface at `/admin/users` to:

- Create new users
- Edit user details
- Assign/remove roles
- Activate/deactivate accounts
- View user activity

## Security Features

1. **Active User Check**: The `EnsureUserIsActive` middleware automatically logs out deactivated users.

2. **Role Caching**: Role and permission checks are cached for 5 minutes to improve performance.

3. **Audit Trail**: All role assignments are tracked with who assigned them and when.

4. **Self-Protection**: Users cannot deactivate their own admin account.

5. **Role Expiration**: Roles can be assigned with an expiration date for temporary access.

## Performance Optimization

- Role and permission checks are cached using Laravel's cache system
- Database queries are optimized with proper indexes
- Eager loading of relationships prevents N+1 queries
- Cache keys are structured for efficient invalidation

## Best Practices

1. **Always check permissions in controllers** - Don't rely solely on middleware
2. **Use specific permission checks** - Prefer `canApproveDcr()` over checking role directly
3. **Clear cache after role changes** - Call `$user->clearRoleCache()` after modifying roles
4. **Use Blade directives in views** - Keep authorization logic consistent
5. **Log permission denials** - Track unauthorized access attempts for security

## Extending the System

### Adding New Permissions

1. Add the permission to `app/Enums/Permission.php`
2. Update the `getForRole()` method to assign it to appropriate roles
3. Update the display name in `getDisplayName()`

### Adding New Roles

1. Add the role to `app/Enums/Role.php`
2. Define its permissions in `Permission::getForRole()`
3. Run migrations to add the role to the database
4. Update seeders if needed

### Custom Permission Logic

Create custom methods in the User model for complex permission checks:

```php
public function canViewDcr($dcr)
{
    if ($this->isAdministrator()) {
        return true;
    }
    
    return $dcr->author_id === $this->id 
        || $dcr->recipient_id === $this->id 
        || $dcr->decision_maker_id === $this->id;
}
```

## Troubleshooting

### "Permission denied" errors
- Check if user has active roles
- Verify role assignments in database
- Clear cache: `$user->clearRoleCache()`
- Check middleware order in routes

### Roles not updating
- Clear application cache: `php artisan cache:clear`
- Clear role cache: `$user->clearRoleCache()`
- Verify `is_active` flag on role assignments

### Performance issues
- Check cache configuration
- Verify database indexes are present
- Monitor cache hit rates
- Consider using Redis for better cache performance
