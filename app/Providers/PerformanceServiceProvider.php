<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\View;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register performance monitoring service
        $this->app->singleton('performance.monitor', function ($app) {
            return new \stdClass();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Prevent lazy loading in production to catch N+1 issues
        Model::preventLazyLoading(!app()->isProduction());
        
        // Optimize queries with strictness in development
        if (app()->environment('local')) {
            Model::shouldBeStrict();
            
            // Log slow queries
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    \Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms',
                        'connection' => $query->connectionName,
                    ]);
                }
            });
        }
        
        // Enable query result caching for production
        if (app()->isProduction()) {
            // DB::enableQueryLog() is disabled in production
            DB::disableQueryLog();
        }

        // Register model observers for cache invalidation
        $this->registerModelObservers();
        
        // Share cached data with views
        $this->shareCachedDataWithViews();
    }

    /**
     * Register model observers to clear caches on updates
     */
    protected function registerModelObservers(): void
    {
        // Clear user-related caches when user roles change
        \App\Models\User::updated(function ($user) {
            $this->clearUserCaches($user->id);
        });

        \App\Models\User::deleted(function ($user) {
            $this->clearUserCaches($user->id);
        });

        // Clear DCR report caches when DCRs change
        \App\Models\ChangeRequest::saved(function ($dcr) {
            Cache::tags(['dcr_reports'])->flush();
            $this->clearDashboardCaches();
        });

        \App\Models\ChangeRequest::deleted(function ($dcr) {
            Cache::tags(['dcr_reports'])->flush();
            $this->clearDashboardCaches();
        });
    }
    
    /**
     * Clear user-specific caches.
     */
    protected function clearUserCaches($userId): void
    {
        Cache::forget("user_{$userId}_has_role_admin");
        Cache::forget("user_{$userId}_has_role_dom");
        Cache::forget("user_{$userId}_has_role_recipient");
        Cache::forget("user_{$userId}_has_role_author");
        Cache::forget("user_{$userId}_highest_role_level");
        Cache::forget('active_users_list');
        Cache::forget('recipients_list');
        Cache::forget('decision_makers_list');
    }
    
    /**
     * Clear dashboard caches.
     */
    protected function clearDashboardCaches(): void
    {
        Cache::forget('dashboard_chart_data');
        // Pattern-based cache clearing for user-specific dashboards
        // In production, consider using cache tags instead
    }
    
    /**
     * Share commonly used cached data with all views.
     */
    protected function shareCachedDataWithViews(): void
    {
        // Share application settings or frequently accessed data
        View::composer('*', function ($view) {
            // Only for authenticated users
            if (auth()->check()) {
                $view->with('_app_version', config('app.version', '1.0.0'));
            }
        });
    }
}
