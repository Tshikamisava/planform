<?php

namespace App\Http\Controllers;

use App\Models\Dcr;
use App\Models\User;
use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

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
        
        // Cache key based on date range
        $cacheKey = "dcr_summary_{$startDate}_{$endDate}";
        
        // Cache for 1 hour with tags for easy invalidation
        $reportData = Cache::tags(['dcr_reports'])->remember($cacheKey, 3600, function () use ($startDate, $endDate) {
            $query = ChangeRequest::whereBetween('created_at', [$startDate, $endDate]);
            
            // Overall statistics
            $totalDcrs = $query->count();
            $approvedDcrs = (clone $query)->where('status', 'Approved')->count();
            $rejectedDcrs = (clone $query)->where('status', 'Rejected')->count();
            $completedDcrs = (clone $query)->where('status', 'Completed')->count();
            $closedDcrs = (clone $query)->where('status', 'Closed')->count();
            $pendingDcrs = (clone $query)->where('status', 'Pending')->count();
            
            // Impact rating statistics
            $impactStats = ChangeRequest::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('impact, COUNT(*) as count')
                ->groupBy('impact')
                ->get();
                
            // Monthly trends
            $monthlyTrends = ChangeRequest::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, status, COUNT(*) as count')
                ->groupBy('month', 'status')
                ->orderBy('month')
                ->get();
                
            // Top priorities
            $topPriorities = ChangeRequest::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->orderBy('count', 'desc')
                ->get();
                
            // Escalation statistics
            $escalatedDcrs = ChangeRequest::whereBetween('created_at', [$startDate, $endDate])
                ->where('impact', 'High')
                ->count();
                
            // Average processing time (turnaround time)
            $avgProcessingTime = ChangeRequest::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['Approved', 'Rejected', 'Completed', 'Closed'])
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->first();
            
            return compact(
                'totalDcrs', 'approvedDcrs', 'rejectedDcrs', 'completedDcrs', 'closedDcrs', 'pendingDcrs',
                'impactStats', 'monthlyTrends', 'topPriorities', 'escalatedDcrs',
                'avgProcessingTime'
            );
        });
        
        return view('reports.dcr-summary', array_merge($reportData, compact('startDate', 'endDate')));
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
            ->whereIn('status', ['Approved', 'Rejected', 'Completed', 'Closed'])
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
                      ->whereIn('status', ['Approved', 'Rejected', 'Completed', 'Closed']);
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
            ->whereIn('status', ['Approved', 'Rejected', 'Completed', 'Closed'])
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
     * Export report to CSV, Excel, or PDF.
     */
    public function export(Request $request, $type)
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $format = $request->get('format', 'csv'); // csv, excel, pdf
        
        // Get data based on report type
        $data = match($type) {
            'summary' => $this->getSummaryData($startDate, $endDate),
            'impact' => $this->getImpactData($startDate, $endDate),
            'performance' => $this->getPerformanceData($startDate, $endDate),
            'compliance' => $this->getComplianceData($startDate, $endDate),
            default => null
        };
        
        if (!$data) {
            return redirect()->back()->with('error', 'Invalid report type');
        }
        
        $filename = "dcr-report-{$type}-{$startDate}-to-{$endDate}";
        
        // Export based on format
        return match($format) {
            'excel' => $this->exportToExcel($data, $filename, $type),
            'pdf' => $this->exportToPdf($data, $filename, $type, $startDate, $endDate),
            default => $this->exportToCsv($data, $filename)
        };
    }
    
    /**
     * Export to CSV format.
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];
        
        return response()->stream(function() use ($data) {
            $file = fopen('php://output', 'w');
            
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
            }
            
            fclose($file);
        }, 200, $headers);
    }
    
    /**
     * Export to Excel format.
     */
    private function exportToExcel($data, $filename, $type)
    {
        $export = new class($data, ucfirst($type) . ' Report') implements FromCollection, WithHeadings, ShouldAutoSize {
            private $data;
            private $title;
            
            public function __construct($data, $title) {
                $this->data = $data;
                $this->title = $title;
            }
            
            public function collection()
            {
                return new Collection($this->data);
            }
            
            public function headings(): array
            {
                return !empty($this->data) ? array_keys($this->data[0]) : [];
            }
        };
        
        return Excel::download($export, $filename . '.xlsx');
    }
    
    /**
     * Export to PDF format.
     */
    private function exportToPdf($data, $filename, $type, $startDate, $endDate)
    {
        $pdf = Pdf::loadView('reports.pdf.export', [
            'data' => $data,
            'type' => $type,
            'title' => ucfirst($type) . ' Report',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now()->format('Y-m-d H:i:s')
        ]);
        
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download($filename . '.pdf');
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
