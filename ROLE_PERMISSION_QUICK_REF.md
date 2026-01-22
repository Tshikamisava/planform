# Role & Permission Quick Reference

## Test Users (All passwords: `password`)

```
Admin:     admin@planform.com
DOM:       dom@planform.com  
Recipient: recipient@planform.com
Author:    author@planform.com
```

## Role Hierarchy (1-4)

```
Author → Recipient → DOM → Admin
  (1)       (2)      (3)    (4)
```

## Blade Directives

```blade
@role('admin')               {{-- Check single role --}}
@anyrole(['admin', 'dom'])   {{-- Check multiple roles --}}
@permission('create_dcr')    {{-- Check permission --}}
@admin                       {{-- Shorthand for admin --}}
@dom                         {{-- Shorthand for DOM --}}
@author                      {{-- Shorthand for author --}}
@recipient                   {{-- Shorthand for recipient --}}
```

## Route Middleware

```php
->middleware('role:admin')                    // Single role
->middleware('role:admin,dom')                // Multiple roles
->middleware('permission:create_dcr')         // Single permission
->middleware('permission:approve_dcr,reject_dcr') // Multiple permissions
->middleware('active')                        // Ensure user is active
```

## Controller Checks

```php
// Role checks
Auth::user()->hasRole('admin')
Auth::user()->hasAnyRole(['admin', 'dom'])
Auth::user()->isAdministrator()
Auth::user()->isDecisionMaker()

// Permission checks
Auth::user()->canApproveDcr()
Auth::user()->canRejectDcr()
Auth::user()->canDeleteDcr()
Auth::user()->canManageUsers()
Auth::user()->canViewDcr($dcr)
Auth::user()->canEditDcr($dcr)
```

## Common Permissions

| Permission | Roles |
|-----------|-------|
| `create_dcr` | Author, Recipient, DOM, Admin |
| `approve_dcr` | DOM, Admin |
| `reject_dcr` | DOM, Admin |
| `view_all_dcr` | DOM, Admin |
| `manage_users` | Admin |
| `access_reports` | DOM, Admin |
| `delete_dcr` | Admin |

## Admin Routes

```
GET  /admin/users              - List users
GET  /admin/users/create       - Create form
POST /admin/users              - Store user
GET  /admin/users/{id}/edit    - Edit form
PUT  /admin/users/{id}         - Update user
DELETE /admin/users/{id}       - Deactivate user
POST /admin/users/{id}/toggle-active - Toggle status
```

## Running Seeders

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserRoleSeeder
```

## Clearing Cache

```bash
php artisan cache:clear           # Clear all cache
$user->clearRoleCache()           # Clear user role cache (in code)
```

## Quick Implementation Checklist

- [x] Roles table created and seeded
- [x] User_roles pivot table created
- [x] Middleware registered in bootstrap/app.php
- [x] Blade directives added to AppServiceProvider
- [x] Helper methods added to User model
- [x] Controllers updated with permission checks
- [x] Routes protected with middleware
- [x] Test users created

## Need Help?

See [ROLE_PERMISSION_SYSTEM.md](ROLE_PERMISSION_SYSTEM.md) for complete documentation.
