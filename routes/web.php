<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DcrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RbacController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DCR Routes with RBAC protection
    Route::get('/dcr', [DcrController::class, 'index'])->name('dcr.dashboard');
    Route::get('/dcr/approval-dashboard', [DcrController::class, 'approvalDashboard'])
        ->middleware('permission:view_all_dcr')->name('dcr.approval.dashboard');
    Route::get('/dcr/create', [DcrController::class, 'create'])
        ->middleware('permission:create_dcr')->name('dcr.create');
    Route::post('/dcr', [DcrController::class, 'store'])
        ->middleware('permission:create_dcr')->name('dcr.store');
    Route::get('/dcr/{dcr}', [DcrController::class, 'show'])
        ->middleware('can:view,dcr')->name('dcr.show');
    Route::get('/dcr/{dcr}/impact-rating', [DcrController::class, 'impactRating'])
        ->middleware('permission:create_assessment')->name('dcr.impact.rating');
    Route::post('/dcr/{dcr}/impact-rating', [DcrController::class, 'storeImpactRating'])
        ->middleware('permission:create_assessment')->name('dcr.impact.store');
    Route::get('/dcr/{dcr}/pdf/{attachment}', [DcrController::class, 'viewPdf'])
        ->middleware('permission:view_documents')->name('dcr.pdf.view');
    Route::post('/dcr/{dcr}/approve', [DcrController::class, 'approve'])
        ->middleware('permission:approve_dcr')->name('dcr.approve');
    Route::post('/dcr/{dcr}/reject', [DcrController::class, 'reject'])
        ->middleware('permission:approve_dcr')->name('dcr.reject');
    Route::post('/dcr/{dcr}/approve-with-recommendations', [DcrController::class, 'approveWithRecommendations'])
        ->middleware('permission:approve_dcr')->name('dcr.approve.with.recommendations');
    Route::post('/dcr/{dcr}/reassign', [DcrController::class, 'reassign'])
        ->middleware('permission:manage_users')->name('dcr.reassign');
    Route::post('/dcr/{dcr}/complete', [DcrController::class, 'complete'])
        ->middleware('permission:complete_dcr')->name('dcr.complete');
    Route::post('/dcr/{dcr}/close', [DcrController::class, 'close'])
        ->middleware('permission:close_dcr')->name('dcr.close');

    // Report Routes with RBAC protection
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('permission:access_reports')->name('reports.dashboard');
    Route::get('/reports/dcr-summary', [ReportController::class, 'dcrSummary'])
        ->middleware('permission:access_reports')->name('reports.dcr.summary');
    Route::get('/reports/impact-analysis', [ReportController::class, 'impactAnalysis'])
        ->middleware('permission:access_reports')->name('reports.impact.analysis');
    Route::get('/reports/performance-metrics', [ReportController::class, 'performanceMetrics'])
        ->middleware('permission:access_reports')->name('reports.performance.metrics');
    Route::get('/reports/compliance-audit', [ReportController::class, 'complianceAudit'])
        ->middleware('permission:view_audit_logs')->name('reports.compliance.audit');
    Route::get('/reports/user-activity', [ReportController::class, 'userActivity'])
        ->middleware('permission:view_audit_logs')->name('reports.user.activity');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])
        ->middleware('permission:access_reports')->name('reports.export');

    // Admin User Management Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // RBAC Management Routes
    Route::prefix('rbac')->group(function () {
        Route::get('/permissions', [RbacController::class, 'getUserPermissions'])->name('rbac.permissions');
        Route::get('/check/{permission}', [RbacController::class, 'checkPermission'])->name('rbac.check.permission');
        Route::get('/matrix', [RbacController::class, 'getPermissionMatrix'])->name('rbac.matrix');
        Route::get('/all-permissions', [RbacController::class, 'getAllPermissions'])->name('rbac.all.permissions');
        Route::get('/test', [RbacController::class, 'testRbac'])->name('rbac.test');
        Route::post('/clear-cache', [RbacController::class, 'clearPermissionCache'])->name('rbac.clear.cache');
    });
});

require __DIR__.'/auth.php';
