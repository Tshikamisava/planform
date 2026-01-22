# Registration, Role Selection & Email Verification Implementation

## Overview
The registration system now includes:
1. ✅ Role selection during registration
2. ✅ Role-based redirection after registration
3. ✅ Email verification requirement
4. ✅ Protected routes requiring verified email

---

## Features Implemented

### 1. Role Selection During Registration
**Location:** [resources/views/auth/register.blade.php](resources/views/auth/register.blade.php)

Users can now select their role during registration:
- **Author** - Submit DCR Requests
- **Recipient** - Implement Changes
- **Decision Maker (DOM)** - Approve/Reject DCRs
- **Viewer** - View Only Access

### 2. Email Verification
**Model:** [app/Models/User.php](app/Models/User.php)

The User model now implements `MustVerifyEmail` contract, requiring all users to verify their email before accessing protected features.

**Verification Flow:**
1. User registers → receives verification email
2. User clicks link in email → email verified
3. User can now access all protected routes

**Email Configuration:**
- Default: Emails logged to `storage/logs/laravel.log` (MAIL_MAILER=log)
- Production: Update `.env` with SMTP settings:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.mailtrap.io
  MAIL_PORT=2525
  MAIL_USERNAME=your_username
  MAIL_PASSWORD=your_password
  MAIL_FROM_ADDRESS=noreply@yourapp.com
  MAIL_FROM_NAME="${APP_NAME}"
  ```

### 3. Role-Based Redirection
**Controller:** [app/Http/Controllers/Auth/RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php)

After registration, users are automatically redirected based on their role:

| Role | Redirect To | Description |
|------|-------------|-------------|
| Author | `/dcr/create` | Create new DCR form |
| Recipient | `/dcr/my-tasks` | View assigned tasks |
| DOM | `/dcr/pending-approval` | View DCRs needing approval |
| Viewer | `/dcr` | DCR dashboard (read-only) |

### 4. New Routes & Pages

**Routes Added:** [routes/web.php](routes/web.php)
```php
// Decision Maker landing page
Route::get('/dcr/pending-approval', [DcrController::class, 'pendingApproval'])
    ->middleware('permission:approve_dcr')->name('dcr.pending-approval');

// Recipient landing page
Route::get('/dcr/my-tasks', [DcrController::class, 'myTasks'])
    ->middleware('permission:complete_dcr')->name('dcr.my-tasks');
```

**New Views:**
- [resources/views/dcr/pending-approval.blade.php](resources/views/dcr/pending-approval.blade.php) - DOM's pending approvals
- [resources/views/dcr/my-tasks.blade.php](resources/views/dcr/my-tasks.blade.php) - Recipient's assigned tasks

### 5. Protected Routes
**All DCR routes now require email verification** via `verified` middleware:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // All DCR routes here
});
```

---

## Testing the Implementation

### Test Registration Flow:

1. **Register as Author:**
   ```
   - Go to /register
   - Fill in details
   - Select "Author" role
   - Submit → Redirected to /dcr/create
   ```

2. **Register as Recipient:**
   ```
   - Select "Recipient" role
   - Submit → Redirected to /dcr/my-tasks
   ```

3. **Register as Decision Maker:**
   ```
   - Select "Decision Maker" role
   - Submit → Redirected to /dcr/pending-approval
   ```

### Test Email Verification:

1. **Check Logs (Development):**
   ```bash
   Get-Content storage/logs/laravel.log | Select-String -Pattern "verify-email"
   ```
   The verification link will be logged here.

2. **Manual Verification (Bypass for Testing):**
   ```php
   // In tinker
   php artisan tinker
   $user = User::find(1);
   $user->email_verified_at = now();
   $user->save();
   ```

3. **Test Protected Routes:**
   - Try accessing `/dcr/create` without verification → redirected to verify-email
   - Verify email → can access all protected routes

---

## Database Changes

No new migrations required. The system uses existing:
- `users.email_verified_at` column (already exists)
- `roles` table and `user_roles` pivot table (already exists)

---

## Configuration Files Modified

1. ✅ [app/Models/User.php](app/Models/User.php)
2. ✅ [app/Http/Controllers/Auth/RegisteredUserController.php](app/Http/Controllers/Auth/RegisteredUserController.php)
3. ✅ [app/Http/Controllers/DcrController.php](app/Http/Controllers/DcrController.php)
4. ✅ [resources/views/auth/register.blade.php](resources/views/auth/register.blade.php)
5. ✅ [routes/web.php](routes/web.php)

---

## Email Configuration for Production

### Using Gmail:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="DCR System"
```

### Using Mailtrap (Testing):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_FROM_ADDRESS=noreply@dcrapp.com
MAIL_FROM_NAME="DCR System"
```

### Using SendGrid:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="DCR System"
```

---

## Security Features

1. **Email Verification Required** - Users must verify email before accessing protected routes
2. **Role-Based Access Control** - Routes protected by role permissions
3. **Secure Password Validation** - Laravel's default password rules applied
4. **CSRF Protection** - All forms protected with @csrf token

---

## Next Steps

1. **Configure Email Service** - Update `.env` with production SMTP settings
2. **Test Email Delivery** - Register test user and verify email receipt
3. **Customize Email Template** - Modify verification email design if needed
4. **Add Role Seeder** - Ensure roles (author, recipient, dom, viewer) exist in database

---

## Troubleshooting

### Email Not Sending:
```bash
# Check mail configuration
php artisan config:cache
php artisan queue:work  # If using queued emails
```

### Role Not Assigned:
```bash
# Verify roles exist in database
php artisan tinker
\App\Models\Role::all();

# Run role seeder if needed
php artisan db:seed --class=RoleSeeder
```

### Routes Not Working:
```bash
# Clear route cache
php artisan route:cache
php artisan config:cache
php artisan optimize:clear
```

---

## Summary

✅ Role selection dropdown added to registration form  
✅ Users automatically assigned selected role upon registration  
✅ Role-based redirection implemented (Author→Create, DOM→Approvals, Recipient→Tasks)  
✅ Email verification required for all protected routes  
✅ New landing pages created for DOM and Recipients  
✅ All DCR routes protected with 'verified' middleware  
✅ Email verification views already included in Laravel  

The system is ready for testing! Register new users and verify the role-based workflow.
