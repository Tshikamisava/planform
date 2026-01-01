<?php

namespace App\Http\Controllers;

use App\Models\Dcr;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        return view('reports.dashboard', compact('startDate', 'endDate'));
    }

    /**
     * Generate DCR Summary Report.
     */
    public function dcrSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $query = Dcr::whereBetween('created_at', [$startDate, $endDate]);
        
        // Overall statistics
        $totalDcrs = $query->count();
        $approvedDcrs = $query->where('status', 'Approved')->count();
        $rejectedDcrs = $query->where('status', 'Rejected')->count();
        $pendingDcrs = $query->where('status', 'Pending')->count();
        
        // Impact rating statistics
        $impactStats = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('impact_rating, COUNT(*) as count')
            ->groupBy('impact_rating')
            ->get();
            
        // Monthly trends
        $monthlyTrends = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, status, COUNT(*) as count')
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();
            
        // Top request types
        $topRequestTypes = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('request_type, COUNT(*) as count')
            ->groupBy('request_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
            
        // Escalation statistics
        $escalatedDcrs = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->where('auto_escalated', true)
            ->count();
            
        // Average processing time
        $avgProcessingTime = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['Approved', 'Rejected'])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->first();
            
        return view('reports.dcr-summary', compact(
            'startDate', 'endDate',
            'totalDcrs', 'approvedDcrs', 'rejectedDcrs', 'pendingDcrs',
            'impactStats', 'monthlyTrends', 'topRequestTypes', 'escalatedDcrs',
            'avgProcessingTime'
        ));
    }

    /**
     * Generate Impact Analysis Report.
     */
    public function impactAnalysis(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Impact distribution
        $impactDistribution = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('impact_rating, COUNT(*) as count')
            ->groupBy('impact_rating')
            ->get();
            
        // Impact vs Status correlation
        $impactStatusCorrelation = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('impact_rating, status, COUNT(*) as count')
            ->groupBy('impact_rating', 'status')
            ->get();
            
        // High impact analysis
        $highImpactDcrs = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->where('impact_rating', 'High')
            ->with(['author', 'recipient', 'escalatedTo'])
            ->get();
            
        // Escalation analysis
        $escalationAnalysis = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->where('auto_escalated', true)
            ->selectRaw('impact_rating, COUNT(*) as count')
            ->groupBy('impact_rating')
            ->get();
            
        // Processing time by impact
        $processingTimeByImpact = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['Approved', 'Rejected'])
            ->selectRaw('impact_rating, AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->groupBy('impact_rating')
            ->get();
            
        return view('reports.impact-analysis', compact(
            'startDate', 'endDate',
            'impactDistribution', 'impactStatusCorrelation', 'highImpactDcrs',
            'escalationAnalysis', 'processingTimeByImpact'
        ));
    }

    /**
     * Generate Performance Metrics Report.
     */
    public function performanceMetrics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // User performance metrics
        $userMetrics = User::withCount(['dcrs as submitted_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['assignedDcrs as reviewed_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('updated_at', [$startDate, $endDate])
                      ->whereIn('status', ['Approved', 'Rejected']);
            }])
            ->where('role', '!=', 'Admin')
            ->get();
            
        // Approval rates by user
        $approvalRates = User::where('role', '!=', 'Admin')
            ->with(['assignedDcrs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('updated_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($user) {
                $total = $user->assignedDcrs->count();
                $approved = $user->assignedDcrs->where('status', 'Approved')->count();
                $rate = $total > 0 ? ($approved / $total) * 100 : 0;
                
                return [
                    'user' => $user,
                    'total_reviewed' => $total,
                    'approved_count' => $approved,
                    'approval_rate' => round($rate, 2)
                ];
            });
            
        // Processing time metrics
        $processingMetrics = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['Approved', 'Rejected'])
            ->selectRaw('
                AVG(DATEDIFF(updated_at, created_at)) as avg_processing_time,
                MIN(DATEDIFF(updated_at, created_at)) as min_processing_time,
                MAX(DATEDIFF(updated_at, created_at)) as max_processing_time,
                COUNT(*) as total_processed
            ')
            ->first();
            
        // Due date compliance
        $dueDateCompliance = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN due_date < CURDATE() AND status = "Pending" THEN 1 ELSE 0 END) as overdue,
                SUM(CASE WHEN due_date >= CURDATE() OR status != "Pending" THEN 1 ELSE 0 END) as on_time
            ')
            ->first();
            
        return view('reports.performance-metrics', compact(
            'startDate', 'endDate',
            'userMetrics', 'approvalRates', 'processingMetrics', 'dueDateCompliance'
        ));
    }

    /**
     * Generate Compliance Audit Report.
     */
    public function complianceAudit(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Missing impact ratings
        $missingImpactRatings = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('impact_rating')
            ->where('status', 'Pending')
            ->with(['author', 'recipient'])
            ->get();
            
        // Overdue DCRs
        $overdueDcrs = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->where('due_date', '<', now())
            ->where('status', 'Pending')
            ->with(['author', 'recipient'])
            ->get();
            
        // Escalation compliance
        $escalationCompliance = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->where('impact_rating', 'High')
            ->selectRaw('
                COUNT(*) as total_high_impact,
                SUM(CASE WHEN auto_escalated = 1 THEN 1 ELSE 0 END) as escalated_count,
                SUM(CASE WHEN auto_escalated = 0 THEN 1 ELSE 0 END) as not_escalated_count
            ')
            ->first();
            
        // Recommendation compliance
        $recommendationCompliance = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'Approved')
            ->selectRaw('
                COUNT(*) as total_approved,
                SUM(CASE WHEN recommendations IS NOT NULL AND recommendations != "" THEN 1 ELSE 0 END) as with_recommendations,
                SUM(CASE WHEN recommendations IS NULL OR recommendations = "" THEN 1 ELSE 0 END) as without_recommendations
            ')
            ->first();
            
        // Audit trail
        $recentActivity = Dcr::whereBetween('updated_at', [$startDate, $endDate])
            ->with(['author', 'recipient', 'escalatedTo'])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();
            
        return view('reports.compliance-audit', compact(
            'startDate', 'endDate',
            'missingImpactRatings', 'overdueDcrs', 'escalationCompliance',
            'recommendationCompliance', 'recentActivity'
        ));
    }

    /**
     * Generate User Activity Report.
     */
    public function userActivity(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // User activity summary
        $userActivity = User::where('role', '!=', 'Admin')
            ->with(['dcrs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['assignedDcrs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('updated_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($user) {
                return [
                    'user' => $user,
                    'submitted_count' => $user->dcrs->count(),
                    'reviewed_count' => $user->assignedDcrs->count(),
                    'last_activity' => $user->dcrs->max('created_at') ?? $user->assignedDcrs->max('updated_at')
                ];
            });
            
        // Daily activity trends
        $dailyActivity = Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as submitted_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Role-based activity
        $roleActivity = User::selectRaw('role, COUNT(*) as user_count')
            ->groupBy('role')
            ->get();
            
        return view('reports.user-activity', compact(
            'startDate', 'endDate',
            'userActivity', 'dailyActivity', 'roleActivity'
        ));
    }

    /**
     * Export report to CSV.
     */
    public function export(Request $request, $type)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $filename = "dcr-report-{$type}-{$startDate}-to-{$endDate}.csv";
        
        switch($type) {
            case 'summary':
                $data = $this->getSummaryData($startDate, $endDate);
                break;
            case 'impact':
                $data = $this->getImpactData($startDate, $endDate);
                break;
            case 'performance':
                $data = $this->getPerformanceData($startDate, $endDate);
                break;
            case 'compliance':
                $data = $this->getComplianceData($startDate, $endDate);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        return response()->stream(function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header row
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
            }
            
            // Data rows
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Get summary data for export.
     */
    private function getSummaryData($startDate, $endDate)
    {
        return Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->with(['author', 'recipient'])
            ->get()
            ->map(function($dcr) {
                return [
                    'DCR ID' => $dcr->dcr_id,
                    'Request Type' => $dcr->request_type,
                    'Author' => $dcr->author->name,
                    'Recipient' => $dcr->recipient->name,
                    'Status' => $dcr->status,
                    'Impact Rating' => $dcr->impact_rating ?? 'Not Rated',
                    'Due Date' => $dcr->due_date->format('Y-m-d'),
                    'Created Date' => $dcr->created_at->format('Y-m-d'),
                    'Updated Date' => $dcr->updated_at->format('Y-m-d'),
                    'Auto Escalated' => $dcr->auto_escalated ? 'Yes' : 'No',
                ];
            })->toArray();
    }

    /**
     * Get impact data for export.
     */
    private function getImpactData($startDate, $endDate)
    {
        return Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('impact_rating')
            ->with(['author', 'recipient'])
            ->get()
            ->map(function($dcr) {
                return [
                    'DCR ID' => $dcr->dcr_id,
                    'Impact Rating' => $dcr->impact_rating,
                    'Impact Summary' => $dcr->impact_summary,
                    'Recommendations' => $dcr->recommendations,
                    'Status' => $dcr->status,
                    'Auto Escalated' => $dcr->auto_escalated ? 'Yes' : 'No',
                    'Escalated To' => $dcr->escalatedTo->name ?? 'N/A',
                    'Processing Days' => $dcr->updated_at->diffInDays($dcr->created_at),
                ];
            })->toArray();
    }

    /**
     * Get performance data for export.
     */
    private function getPerformanceData($startDate, $endDate)
    {
        return User::where('role', '!=', 'Admin')
            ->with(['dcrs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['assignedDcrs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('updated_at', [$startDate, $endDate])
                      ->whereIn('status', ['Approved', 'Rejected']);
            }])
            ->get()
            ->map(function($user) {
                $totalReviewed = $user->assignedDcrs->count();
                $approvedCount = $user->assignedDcrs->where('status', 'Approved')->count();
                $approvalRate = $totalReviewed > 0 ? ($approvedCount / $totalReviewed) * 100 : 0;
                
                return [
                    'User' => $user->name,
                    'Role' => $user->role,
                    'Email' => $user->email,
                    'Submitted DCRs' => $user->dcrs->count(),
                    'Reviewed DCRs' => $totalReviewed,
                    'Approved DCRs' => $approvedCount,
                    'Approval Rate (%)' => round($approvalRate, 2),
                ];
            })->toArray();
    }

    /**
     * Get compliance data for export.
     */
    private function getComplianceData($startDate, $endDate)
    {
        return Dcr::whereBetween('created_at', [$startDate, $endDate])
            ->with(['author', 'recipient'])
            ->get()
            ->map(function($dcr) {
                $isOverdue = $dcr->due_date->isPast() && $dcr->status === 'Pending';
                $hasImpactRating = !is_null($dcr->impact_rating);
                $needsEscalation = $dcr->impact_rating === 'High' && !$dcr->auto_escalated;
                $hasRecommendations = !empty($dcr->recommendations);
                
                return [
                    'DCR ID' => $dcr->dcr_id,
                    'Status' => $dcr->status,
                    'Is Overdue' => $isOverdue ? 'Yes' : 'No',
                    'Has Impact Rating' => $hasImpactRating ? 'Yes' : 'No',
                    'Impact Rating' => $dcr->impact_rating ?? 'Not Rated',
                    'Needs Escalation' => $needsEscalation ? 'Yes' : 'No',
                    'Auto Escalated' => $dcr->auto_escalated ? 'Yes' : 'No',
                    'Has Recommendations' => $hasRecommendations ? 'Yes' : 'No',
                    'Compliance Score' => $this->calculateComplianceScore($dcr),
                ];
            })->toArray();
    }

    /**
     * Calculate compliance score for a DCR.
     */
    private function calculateComplianceScore($dcr)
    {
        $score = 100;
        
        // Deduct points for compliance issues
        if ($dcr->due_date->isPast() && $dcr->status === 'Pending') {
            $score -= 25;
        }
        
        if (is_null($dcr->impact_rating)) {
            $score -= 20;
        }
        
        if ($dcr->impact_rating === 'High' && !$dcr->auto_escalated) {
            $score -= 30;
        }
        
        if ($dcr->status === 'Approved' && empty($dcr->recommendations)) {
            $score -= 15;
        }
        
        return max(0, $score);
    }
}
