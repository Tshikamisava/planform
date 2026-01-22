<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Dcr;
use App\Models\ChangeRequest;
use App\Models\User;
use App\Policies\DcrPolicy;
use App\Policies\UserPolicy;
use App\Policies\DocumentPolicy;
use App\Services\PermissionService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Dcr::class => DcrPolicy::class,
        ChangeRequest::class => DcrPolicy::class,
        User::class => UserPolicy::class,
        'App\Models\Document' => DocumentPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define comprehensive permission gates
        Gate::define('admin', function ($user) {
            return PermissionService::hasPermission($user, Permission::MANAGE_SYSTEM);
        });

        Gate::define('view-admin-dashboard', function ($user) {
            return PermissionService::hasPermission($user, Permission::MANAGE_SYSTEM);
        });

        Gate::define('manage-users', function ($user) {
            return PermissionService::hasPermission($user, Permission::MANAGE_USERS);
        });

        Gate::define('access-reports', function ($user) {
            return PermissionService::hasPermission($user, Permission::ACCESS_REPORTS);
        });

        Gate::define('view-audit-logs', function ($user) {
            return PermissionService::hasPermission($user, Permission::VIEW_AUDIT_LOGS);
        });

        // DCR-specific gates
        Gate::define('create-dcr', function ($user) {
            return PermissionService::hasPermission($user, Permission::CREATE_DCR);
        });

        Gate::define('edit-dcr', function ($user, $dcr) {
            return PermissionService::hasPermission($user, Permission::EDIT_OWN_DCR) && 
                   $user->id === $dcr->author_id &&
                   $dcr->status === 'draft';
        });

        Gate::define('approve-dcr', function ($user, $dcr) {
            return PermissionService::hasPermission($user, Permission::APPROVE_DCR) &&
                   ($user->id === $dcr->decision_maker_id || PermissionService::hasPermission($user, Permission::MANAGE_SYSTEM));
        });

        Gate::define('reject-dcr', function ($user, $dcr) {
            return Gate::allows('approve-dcr', $dcr);
        });

        Gate::define('complete-dcr', function ($user, $dcr) {
            return PermissionService::hasPermission($user, Permission::COMPLETE_DCR) &&
                   ($user->id === $dcr->recipient_id || PermissionService::hasPermission($user, Permission::MANAGE_SYSTEM));
        });

        Gate::define('delete-dcr', function ($user) {
            return PermissionService::hasPermission($user, Permission::DELETE_DCR);
        });

        Gate::define('close-dcr', function ($user) {
            return PermissionService::hasPermission($user, Permission::CLOSE_DCR);
        });

        Gate::define('escalate-dcr', function ($user) {
            return PermissionService::hasPermission($user, Permission::ESCALATE_DCR);
        });

        // Document gates
        Gate::define('upload-documents', function ($user) {
            return PermissionService::hasPermission($user, Permission::UPLOAD_DOCUMENTS);
        });

        Gate::define('delete-documents', function ($user) {
            return PermissionService::hasPermission($user, Permission::DELETE_DOCUMENTS);
        });

        // Impact assessment gates
        Gate::define('create-assessment', function ($user) {
            return PermissionService::hasPermission($user, Permission::CREATE_ASSESSMENT);
        });

        Gate::define('edit-assessment', function ($user) {
            return PermissionService::hasPermission($user, Permission::EDIT_ASSESSMENT);
        });

        // Role-based gates
        Gate::define('is-author', function ($user) {
            return PermissionService::hasRole($user, Role::AUTHOR);
        });

        Gate::define('is-recipient', function ($user) {
            return PermissionService::hasRole($user, Role::RECIPIENT);
        });

        Gate::define('is-dom', function ($user) {
            return PermissionService::hasRole($user, Role::DOM);
        });

        Gate::define('is-admin', function ($user) {
            return PermissionService::hasRole($user, Role::ADMIN);
        });

        // Super admin gate (has all permissions)
        Gate::before(function ($user, $ability) {
            if ($user && PermissionService::hasRole($user, Role::ADMIN)) {
                return true;
            }
        });
    }
}
