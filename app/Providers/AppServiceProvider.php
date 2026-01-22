<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Enums\Role as RoleEnum;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Blade directives for role/permission checking
        
        // @role('admin') ... @endrole
        Blade::if('role', function (string $role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
        
        // @hasrole('admin') ... @endhasrole
        Blade::if('hasrole', function (string $role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
        
        // @anyrole(['admin', 'dom']) ... @endanyrole
        Blade::if('anyrole', function (array $roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });
        
        // @permission('create_dcr') ... @endpermission
        Blade::if('permission', function (string $permission) {
            if (!auth()->check()) {
                return false;
            }
            try {
                $permissionEnum = Permission::from($permission);
                return PermissionService::hasPermission(auth()->user(), $permissionEnum);
            } catch (\ValueError $e) {
                return false;
            }
        });
        
        // @haspermission('create_dcr') ... @endhaspermission
        Blade::if('haspermission', function (string $permission) {
            if (!auth()->check()) {
                return false;
            }
            try {
                $permissionEnum = Permission::from($permission);
                return PermissionService::hasPermission(auth()->user(), $permissionEnum);
            } catch (\ValueError $e) {
                return false;
            }
        });
        
        // @admin ... @endadmin
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->isAdministrator();
        });
        
        // @author ... @endauthor
        Blade::if('author', function () {
            return auth()->check() && auth()->user()->isAuthor();
        });
        
        // @recipient ... @endrecipient
        Blade::if('recipient', function () {
            return auth()->check() && auth()->user()->isRecipient();
        });
        
        // @dom ... @enddom (Decision Maker)
        Blade::if('dom', function () {
            return auth()->check() && auth()->user()->isDecisionMaker();
        });
    }
}

