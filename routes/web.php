<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DcrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
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

    // DCR Routes
    Route::get('/dcr', [DcrController::class, 'index'])->name('dcr.dashboard');
    Route::get('/dcr/approval-dashboard', [DcrController::class, 'approvalDashboard'])->name('dcr.approval.dashboard');
        Route::get('/dcr/create', [DcrController::class, 'create'])->name('dcr.create');
    Route::post('/dcr', [DcrController::class, 'store'])->name('dcr.store');
    Route::get('/dcr/{dcr}', [DcrController::class, 'show'])->name('dcr.show');
    Route::get('/dcr/{dcr}/impact-rating', [DcrController::class, 'impactRating'])->name('dcr.impact.rating');
    Route::post('/dcr/{dcr}/impact-rating', [DcrController::class, 'storeImpactRating'])->name('dcr.impact.store');
    Route::get('/dcr/{dcr}/pdf/{attachment}', [DcrController::class, 'viewPdf'])->name('dcr.pdf.view');
    Route::post('/dcr/{dcr}/approve', [DcrController::class, 'approve'])->name('dcr.approve');
    Route::post('/dcr/{dcr}/reject', [DcrController::class, 'reject'])->name('dcr.reject');
    Route::post('/dcr/{dcr}/approve-with-recommendations', [DcrController::class, 'approveWithRecommendations'])->name('dcr.approve.with.recommendations');

    // Report Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.dashboard');
    Route::get('/reports/dcr-summary', [ReportController::class, 'dcrSummary'])->name('reports.dcr.summary');
    Route::get('/reports/impact-analysis', [ReportController::class, 'impactAnalysis'])->name('reports.impact.analysis');
    Route::get('/reports/performance-metrics', [ReportController::class, 'performanceMetrics'])->name('reports.performance.metrics');
    Route::get('/reports/compliance-audit', [ReportController::class, 'complianceAudit'])->name('reports.compliance.audit');
    Route::get('/reports/user-activity', [ReportController::class, 'userActivity'])->name('reports.user.activity');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    // Admin User Management Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });
});

require __DIR__.'/auth.php';
