<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\User;
use App\Models\AuditLog;
use App\Http\Requests\ChangeRequestStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\DcrSubmitted;
use App\Notifications\DcrSubmittedNotification;
use App\Notifications\DcrAssignedToDomNotification;
use App\Notifications\HighImpactDcrEscalationNotification;
use App\Services\DcrAssignmentService;

class DcrController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function dashboard()
    {
        $startTime = microtime(true);
        $user = Auth::user();
        $isAdmin = $user->roles->contains('name', 'Admin');
        
        // Cache KPIs for 5 minutes
        $kpis = Cache::remember("dashboard_kpis_{$user->id}", 300, function () use ($user, $isAdmin) {
            // Single optimized query for all counts
            $counts = ChangeRequest::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status IN ("Approved", "Completed") THEN 1 ELSE 0 END) as completed
            ')
            ->when(!$isAdmin, function($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('recipient_id', $user->id)
                      ->orWhere('author_id', $user->id)
                      ->orWhere('decision_maker_id', $user->id);
                });
            })
            ->first();
            
            // Calculate average completion time efficiently
            $avgCompletionTime = ChangeRequest::whereIn('status', ['Approved', 'Completed'])
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->value('avg_days');
            
            return [
                ['name' => 'Total DCRs', 'value' => $counts->total, 'change' => '+12%', 'trend' => 'up'],
                ['name' => 'My Tasks', 'value' => $counts->total, 'change' => '+8%', 'trend' => 'up'],
                ['name' => 'Pending Approvals', 'value' => $counts->pending, 'change' => '-5%', 'trend' => 'down'],
                ['name' => 'Avg Completion', 'value' => round($avgCompletionTime ?? 0) . ' days', 'change' => '-3%', 'trend' => 'down'],
            ];
        });
        
        // Get user's action items with optimized query
        $actionItems = Cache::remember("dashboard_action_items_{$user->id}", 180, function () use ($user, $isAdmin) {
            return ChangeRequest::with(['author:id,name', 'recipient:id,name', 'decisionMaker:id,name'])
                ->select(['id', 'dcr_id', 'title', 'description', 'reason_for_change', 'status', 'priority', 'due_date', 'impact_rating', 'author_id', 'recipient_id', 'decision_maker_id', 'created_at'])
                ->when(!$isAdmin, function($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('recipient_id', $user->id)
                          ->orWhere('author_id', $user->id)
                          ->orWhere('decision_maker_id', $user->id);
                    });
                })
                ->whereNotIn('status', ['Approved', 'Completed', 'Rejected'])
                ->orderBy('due_date', 'asc')
                ->limit(5)
                ->get();
        });
        
        // Chart data - DCRs by status (cached for 10 minutes)
        $chartData = Cache::remember('dashboard_chart_data', 600, function () {
            $statusCounts = ChangeRequest::selectRaw('status, COUNT(*) as count')
                ->whereIn('status', ['Draft', 'pending', 'In_Progress', 'Approved'])
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            return [
                ['name' => 'Draft', 'count' => $statusCounts['Draft'] ?? 0],
                ['name' => 'Review', 'count' => $statusCounts['pending'] ?? 0],
                ['name' => 'In Progress', 'count' => $statusCounts['In_Progress'] ?? 0],
                ['name' => 'Approved', 'count' => $statusCounts['Approved'] ?? 0],
            ];
        });
        
        // Recent activity with optimized query
        $recentActivity = Cache::remember("dashboard_recent_activity_{$user->id}", 180, function () use ($user, $isAdmin) {
            return ChangeRequest::with(['author:id,name'])
                ->select(['id', 'dcr_id', 'status', 'author_id', 'updated_at'])
                ->when(!$isAdmin, function($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('recipient_id', $user->id)
                          ->orWhere('author_id', $user->id)
                          ->orWhere('decision_maker_id', $user->id);
                    });
                })
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($dcr) {
                    return [
                        'id' => $dcr->id,
                        'user' => $dcr->author->name,
                        'action' => match($dcr->status) {
                            'Approved' => 'approved DCR-' . $dcr->dcr_id,
                            'Rejected' => 'rejected DCR-' . $dcr->dcr_id,
                            'pending' => 'submitted DCR-' . $dcr->dcr_id,
                            default => 'updated DCR-' . $dcr->dcr_id,
                        },
                        'type' => match($dcr->status) {
                            'Approved' => 'success',
                            'Rejected' => 'error',
                            default => 'info',
                        },
                        'timestamp' => $dcr->updated_at->diffForHumans(),
                    ];
                });
        });
        
        // Track response time
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);
        \Log::info("Dashboard loaded", ['user_id' => $user->id, 'response_time_ms' => $responseTime]);
        
        return view('dashboard', compact('kpis', 'actionItems', 'chartData', 'recentActivity'));
    }

    /**
     * Display calendar view with DCR deadlines and events.
     */
    public function calendar(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        
        // Create date object for current view
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        
        // Get DCRs with due dates in this month
        $dcrs = ChangeRequest::with(['author', 'recipient'])
            ->whereYear('due_date', $year)
            ->whereMonth('due_date', $month)
            ->get();
        
        // Map DCRs to calendar events
        $calendarEvents = $dcrs->map(function ($dcr) {
            return [
                'date' => $dcr->due_date->format('Y-m-d'),
                'title' => 'DCR-' . $dcr->dcr_id . ' Due',
                'type' => 'deadline',
                'dcr_id' => $dcr->id,
                'time' => '09:00 AM',
                'location' => 'Main Lab'
            ];
        })->toArray();
        
        // Get upcoming events (next 10)
        $upcomingEvents = ChangeRequest::with(['author', 'recipient'])
            ->where('due_date', '>=', now())
            ->whereIn('status', ['pending', 'In_Progress', 'In_Review'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($dcr) {
                return [
                    'date' => $dcr->due_date->format('Y-m-d'),
                    'day' => $dcr->due_date->format('d'),
                    'month' => $dcr->due_date->format('M'),
                    'title' => 'DCR-' . $dcr->dcr_id . ' Due',
                    'type' => 'deadline',
                    'dcr_id' => $dcr->id,
                    'time' => '09:00 AM',
                    'location' => 'Main Lab'
                ];
            });
        
        // Calculate calendar grid
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfWeek = $currentDate->copy()->startOfMonth()->dayOfWeek;
        
        return view('calendar.index', compact(
            'currentDate',
            'calendarEvents',
            'upcomingEvents',
            'daysInMonth',
            'firstDayOfWeek',
            'year',
            'month'
        ));
    }

    /**
     * Display collaboration/messaging interface.
     */
    public function collaboration()
    {
        $currentUser = Auth::user();
        
        // Get all active users for contacts
        $users = User::with('roles')
            ->where('id', '!=', $currentUser->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff&bold=true',
                    'online' => rand(0, 1) === 1, // Mock online status
                    'lastMessage' => 'Available for collaboration',
                    'isGroup' => false,
                    'role' => $user->roles->first()->name ?? 'User',
                ];
            });
        
        // Mock project groups (in production, these would come from a groups/teams table)
        $groups = collect([
            [
                'id' => 'group-1',
                'name' => 'Engineering Team',
                'avatar' => 'https://ui-avatars.com/api/?name=Engineering+Team&background=1e3a8a&color=fff&bold=true',
                'lastMessage' => 'New DCR submitted for review',
                'isGroup' => true,
                'memberCount' => 12,
                'online' => true,
            ],
            [
                'id' => 'group-2',
                'name' => 'Quality Assurance',
                'avatar' => 'https://ui-avatars.com/api/?name=Quality+Assurance&background=059669&color=fff&bold=true',
                'lastMessage' => 'Compliance check required',
                'isGroup' => true,
                'memberCount' => 8,
                'online' => true,
            ],
            [
                'id' => 'group-3',
                'name' => 'Management Board',
                'avatar' => 'https://ui-avatars.com/api/?name=Management+Board&background=dc2626&color=fff&bold=true',
                'lastMessage' => 'Weekly review scheduled',
                'isGroup' => true,
                'memberCount' => 6,
                'online' => true,
            ],
        ]);
        
        // Combine groups and personnel
        $contacts = $groups->merge($users);
        
        // Mock messages for demo (in production, fetch from messages table)
        $mockMessages = [
            [
                'id' => 1,
                'sender' => $users->first()['name'] ?? 'System',
                'text' => 'Hi, I need your input on the latest DCR submission.',
                'timestamp' => now()->subMinutes(15)->format('h:i A'),
                'isMe' => false,
                'read' => true,
                'type' => 'text',
            ],
            [
                'id' => 2,
                'sender' => $currentUser->name,
                'text' => 'Sure, let me review the technical specifications.',
                'timestamp' => now()->subMinutes(10)->format('h:i A'),
                'isMe' => true,
                'read' => true,
                'type' => 'text',
            ],
            [
                'id' => 3,
                'sender' => $users->first()['name'] ?? 'System',
                'audioUrl' => '#',
                'audioDuration' => '0:45',
                'timestamp' => now()->subMinutes(5)->format('h:i A'),
                'isMe' => false,
                'read' => false,
                'type' => 'audio',
            ],
        ];
        
        return view('collaboration.index', compact('contacts', 'groups', 'users', 'currentUser', 'mockMessages'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Use eager loading to prevent N+1 queries
        $query = ChangeRequest::with(['author', 'recipient', 'decisionMaker']);
        
        if ($user->isAdministrator() || $user->isDecisionMaker()) {
            // Admins and DOMs can see all DCRs for oversight
            $submittedDcrs = $query->orderBy('created_at', 'desc')->limit(50)->get();
            $assignedDcrs = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
                ->where('recipient_id', $user->id)
                ->orderBy('due_date', 'asc')
                ->limit(50)
                ->get();
        } elseif ($user->isAuthor()) {
            // Authors see their submitted DCRs
            $submittedDcrs = $query->where('author_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
            $assignedDcrs = collect();
        } elseif ($user->isRecipient()) {
            // Recipients see their submitted and assigned DCRs
            $submittedDcrs = $query->where('author_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
            $assignedDcrs = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
                ->where('recipient_id', $user->id)
                ->orderBy('due_date', 'asc')
                ->limit(50)
                ->get();
        } else {
            $submittedDcrs = collect();
            $assignedDcrs = collect();
        }
        
        return view('dcr.dashboard', compact('submittedDcrs', 'assignedDcrs'));
    }

    /**
     * Search DCRs by query string.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        
        if (strlen(trim($query)) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();
        $searchQuery = ChangeRequest::query();

        // Apply role-based filtering
        if (!$user->isAdministrator() && !$user->isDecisionMaker()) {
            $searchQuery->where(function ($q) use ($user) {
                $q->where('author_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            });
        }

        // Search in DCR fields
        $results = $searchQuery
            ->where(function ($q) use ($query) {
                $q->where('dcr_number', 'like', "%{$query}%")
                  ->orWhere('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['author', 'recipient'])
            ->limit(10)
            ->get()
            ->map(function ($dcr) {
                return [
                    'id' => $dcr->id,
                    'dcr_number' => $dcr->dcr_number,
                    'title' => $dcr->title,
                    'description' => Str::limit($dcr->description, 100),
                    'status' => $dcr->status,
                    'priority' => $dcr->priority,
                ];
            });

        return response()->json($results);
    }

    /**
     * Display the workflow/tasks page.
     */
    public function workflow()
    {
        $user = Auth::user();
        
        // Get tasks assigned to or authored by the user
        $tasks = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
            ->where(function ($query) use ($user) {
                $query->where('recipient_id', $user->id)
                      ->orWhere('author_id', $user->id)
                      ->orWhere('decision_maker_id', $user->id);
            })
            ->orderBy('due_date', 'asc')
            ->orderByRaw("FIELD(impact, 'High', 'Medium', 'Low')")
            ->get();

        return view('dcr.workflow', compact('tasks'));
    }

    /**
     * Bulk approve DCRs.
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:change_requests,id'
        ]);

        $user = Auth::user();
        
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $count = ChangeRequest::whereIn('id', $request->ids)
            ->where(function ($query) use ($user) {
                $query->where('decision_maker_id', $user->id)
                      ->orWhereNull('decision_maker_id');
            })
            ->update([
                'status' => 'Approved',
                'decision_maker_id' => $user->id,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "Successfully approved {$count} DCRs."
        ]);
    }

    /**
     * Bulk reject DCRs.
     */
    public function bulkReject(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:change_requests,id'
        ]);

        $user = Auth::user();
        
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $count = ChangeRequest::whereIn('id', $request->ids)
            ->where(function ($query) use ($user) {
                $query->where('decision_maker_id', $user->id)
                      ->orWhereNull('decision_maker_id');
            })
            ->update([
                'status' => 'Rejected',
                'decision_maker_id' => $user->id,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "Successfully rejected {$count} DCRs."
        ]);
    }

    /**
     * Display the approval dashboard.
     */
    public function approvalDashboard()
    {
        $user = Auth::user();
        
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            abort(403, 'Access denied');
        }
        
        $pendingDcrs = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
            ->whereIn('status', ['Pending', 'In_Review'])
            ->where(function ($query) use ($user) {
                $query->where('decision_maker_id', $user->id)
                    ->orWhereNull('decision_maker_id');
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();
            
        return view('dcr.approval-dashboard', compact('pendingDcrs'));
    }

    /**
     * Display the project manager dashboard with approval and assignment capabilities.
     */
    public function managerDashboard()
    {
        $user = Auth::user();
        
        // Check if user is DOM (Decision Maker) or Admin
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            abort(403, 'Access denied. Only project managers can access this dashboard.');
        }
        
        // Get pending DCRs for approval (Draft, Pending, In_Review)
        $pendingApprovals = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
            ->whereIn('status', ['Draft', 'Pending', 'In_Review'])
            ->when(!$user->isAdministrator(), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('decision_maker_id', $user->id)
                      ->orWhereNull('decision_maker_id');
                });
            })
            ->orderBy('due_date', 'asc')
            ->paginate(10, ['*'], 'pending');
        
        // Get approved DCRs that need engineer assignment
        $needsAssignment = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
            ->where('status', 'Approved')
            ->whereNull('recipient_id')
            ->where(function ($query) use ($user) {
                if (!$user->isAdministrator()) {
                    $query->where('decision_maker_id', $user->id);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'assignment');
        
        // Get available engineers (recipients)
        $engineers = User::whereHas('activeRoles', function ($query) {
            $query->where('name', 'recipient');
        })
        ->where('is_active', true)
        ->select('id', 'name', 'email', 'department')
        ->orderBy('name')
        ->get();
        
        // KPIs for project manager
        $kpis = [
            [
                'name' => 'Pending Approval',
                'value' => ChangeRequest::whereIn('status', ['Draft', 'Pending', 'In_Review'])
                    ->when(!$user->isAdministrator(), function ($q) use ($user) {
                        $q->where(function ($query) use ($user) {
                            $query->where('decision_maker_id', $user->id)
                                  ->orWhereNull('decision_maker_id');
                        });
                    })
                    ->count(),
                'icon' => 'clock',
                'color' => 'orange'
            ],
            [
                'name' => 'Approved',
                'value' => ChangeRequest::where('status', 'Approved')
                    ->when(!$user->isAdministrator(), function ($q) use ($user) {
                        $q->where('decision_maker_id', $user->id);
                    })
                    ->count(),
                'icon' => 'check',
                'color' => 'green'
            ],
            [
                'name' => 'Rejected',
                'value' => ChangeRequest::where('status', 'Rejected')
                    ->when(!$user->isAdministrator(), function ($q) use ($user) {
                        $q->where('decision_maker_id', $user->id);
                    })
                    ->count(),
                'icon' => 'x',
                'color' => 'red'
            ],
            [
                'name' => 'Needs Assignment',
                'value' => ChangeRequest::where('status', 'Approved')
                    ->whereNull('recipient_id')
                    ->when(!$user->isAdministrator(), function ($q) use ($user) {
                        $q->where('decision_maker_id', $user->id);
                    })
                    ->count(),
                'icon' => 'users',
                'color' => 'blue'
            ],
        ];
        
        return view('dcr.manager-dashboard', compact('pendingApprovals', 'needsAssignment', 'engineers', 'kpis'));
    }

    /**
     * Assign an engineer to a DCR.
     */
    public function assignEngineer(Request $request, ChangeRequest $dcr)
    {
        $user = Auth::user();
        
        // Check permission
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'recipient_id' => 'required|exists:users,id'
        ]);
        
        // Update the DCR
        $dcr->update([
            'recipient_id' => $request->recipient_id,
            'status' => 'In_Progress'
        ]);
        
        // Log the assignment
        AuditLog::create([
            'uuid' => Str::uuid()->toString(),
            'event_type' => 'assigned',
            'event_category' => 'Workflow',
            'action' => 'Assigned DCR-' . $dcr->dcr_id . ' to engineer',
            'user_id' => $user->id,
            'resource_type' => 'ChangeRequest',
            'resource_id' => $dcr->id,
            'ip_address' => $request->ip(),
            'event_timestamp' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Engineer assigned successfully'
        ]);
    }

    /**
     * Display DCRs pending approval for decision makers.
     */
    public function pendingApproval()
    {
        $user = Auth::user();
        
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            abort(403, 'Access denied');
        }
        
        $pendingDcrs = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
            ->whereIn('status', ['Pending', 'In_Review'])
            ->where(function ($query) use ($user) {
                $query->where('decision_maker_id', $user->id)
                    ->orWhereNull('decision_maker_id');
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->paginate(15);
            
        return view('dcr.pending-approval', compact('pendingDcrs'));
    }

    /**
     * Display tasks assigned to the current user (recipients).
     */
    public function myTasks()
    {
        $user = Auth::user();
        
        if (!$user->isRecipient() && !$user->isAdministrator()) {
            abort(403, 'Access denied');
        }
        
        $assignedDcrs = ChangeRequest::with(['author', 'recipient', 'decisionMaker'])
            ->where('recipient_id', $user->id)
            ->whereIn('status', ['Approved', 'In_Progress'])
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate(15);
            
        return view('dcr.my-tasks', compact('assignedDcrs'));
    }

    /**
     * Show the form for creating a new DCR.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['author', 'admin'])) {
            abort(403, 'You do not have permission to create DCRs.');
        }
        
        // Cache recipients and decision makers for 5 minutes
        $recipients = \Cache::remember('recipients_list', 300, function () {
            return User::whereHas('activeRoles', function ($query) {
                $query->where('name', 'recipient');
            })
            ->where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();
        });
        
        $decisionMakers = \Cache::remember('decision_makers_list', 300, function () {
            return User::whereHas('activeRoles', function ($query) {
                $query->where('name', 'dom');
            })
            ->where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();
        });
        
        return view('dcr.create', compact('recipients', 'decisionMakers'));
    }

    /**
     * Store a newly created DCR in storage.
     */
    public function store(ChangeRequestStoreRequest $request)
    {
        try {
            \Log::info('Starting DCR creation', ['data' => $request->all()]);
            
            // Create the change request
            $validated = $request->validated();
            \Log::info('Validation passed', ['validated' => $validated]);
            
            $changeRequest = ChangeRequest::create($validated);
            \Log::info('DCR created', ['id' => $changeRequest->id]);
            
            // Auto-assign Decision Maker if not provided
            if (!$changeRequest->decision_maker_id) {
                $assignmentService = new DcrAssignmentService();
                $dom = $assignmentService->autoAssignDecisionMaker($changeRequest);
                
                if ($dom) {
                    $changeRequest->decision_maker_id = $dom->id;
                    $changeRequest->save();
                    \Log::info('Auto-assigned DOM', ['dom_id' => $dom->id]);
                }
            }
            
            // Handle file attachments
            if ($request->hasFile('attachments')) {
                \Log::info('Processing attachments');
                $this->handleAttachments($request, $changeRequest);
            }
            
            // Send notifications
            \Log::info('Sending notifications');
            $this->sendNotifications($changeRequest);
            
            // Check for high-impact escalation
            if ($changeRequest->impact === 'High' || $changeRequest->priority === 'Critical') {
                \Log::info('Sending high-impact notifications');
                $this->sendHighImpactNotifications($changeRequest);
            }
            
            // Create audit log
            $changeRequest->auditLogs()->create([
                'uuid' => Str::uuid(),
                'event_type' => 'DCR_CREATED',
                'event_category' => 'Workflow',
                'action' => 'DCR created',
                'user_id' => Auth::id(),
                'resource_type' => 'change_request',
                'resource_id' => $changeRequest->id,
                'success' => true,
                'event_timestamp' => now(),
            ]);
            
            \Log::info('DCR creation completed successfully');
            
            return redirect()->route('dcr.show', $changeRequest)
                ->with('success', 'DCR created successfully! Notifications have been sent to relevant parties.');
                
        } catch (\Exception $e) {
            \Log::error('DCR creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create DCR: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified DCR.
     */
    public function edit(ChangeRequest $dcr)
    {
        $user = Auth::user();

        // Check if user can edit this DCR (handled by policy, but additional check)
        if (!$user->isAdministrator() && ($dcr->author_id !== $user->id || $dcr->status !== 'draft')) {
            abort(403, 'You can only edit your own DCRs in draft status.');
        }

        // Cache recipients and decision makers for 5 minutes
        $recipients = \Cache::remember('recipients_list', 300, function () {
            return User::whereHas('activeRoles', function ($query) {
                $query->where('name', 'recipient');
            })
            ->where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();
        });
        
        $decisionMakers = \Cache::remember('decision_makers_list', 300, function () {
            return User::whereHas('activeRoles', function ($query) {
                $query->where('name', 'dom');
            })
            ->where('is_active', true)
            ->select('id', 'name', 'email')
            ->get();
        });

        return view('dcr.edit', compact('dcr', 'recipients', 'decisionMakers'));
    }

    /**
     * Update the specified DCR in storage.
     */
    public function update(ChangeRequestStoreRequest $request, ChangeRequest $dcr)
    {
        try {
            $user = Auth::user();

            // Check if user can edit this DCR
            if (!$user->isAdministrator() && ($dcr->author_id !== $user->id || $dcr->status !== 'draft')) {
                return redirect()->back()->with('error', 'You can only edit your own DCRs in draft status.');
            }

            \Log::info('Updating DCR', ['dcr_id' => $dcr->id, 'data' => $request->all()]);
            
            $validated = $request->validated();
            \Log::info('Validation passed', ['validated' => $validated]);
            
            // Update the DCR
            $dcr->update($validated);
            \Log::info('DCR updated', ['id' => $dcr->id]);
            
            // Handle file attachments
            if ($request->hasFile('attachments')) {
                \Log::info('Processing attachments for update');
                $attachmentPaths = [];
                
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('dcr-attachments', 'public');
                    $attachmentPaths[] = $path;
                    \Log::info('Attachment stored', ['path' => $path]);
                }
                
                // Merge new attachments with existing ones
                $existingAttachments = $dcr->attachments ?? [];
                $dcr->attachments = array_merge($existingAttachments, $attachmentPaths);
                $dcr->save();
            }
            
            // Log the update
            AuditLog::create([
                'event_type' => 'updated',
                'event_category' => 'dcr',
                'action' => 'Updated DCR-' . $dcr->dcr_id,
                'user_id' => $user->id,
                'resource_type' => 'ChangeRequest',
                'resource_id' => $dcr->id,
                'ip_address' => $request->ip(),
                'event_timestamp' => now(),
            ]);
            
            \Log::info('DCR update completed successfully');
            
            return redirect()->route('dcr.show', $dcr)
                ->with('success', 'DCR updated successfully!');
                
        } catch (\Exception $e) {
            \Log::error('DCR update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update DCR: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified DCR from storage (Admin only).
     */
    public function destroy(ChangeRequest $dcr)
    {
        try {
            $user = Auth::user();
            
            \Log::info('Delete attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'is_admin' => $user->isAdministrator(),
                'dcr_id' => $dcr->dcr_id
            ]);

            // Check admin permission
            if (!$user->isAdministrator()) {
                \Log::warning('Non-admin user attempted to delete DCR', ['user_id' => $user->id]);
                return redirect()->back()->with('error', 'Only administrators can delete DCRs.');
            }

            $dcrId = $dcr->dcr_id;
            
            // Delete associated files
            if ($dcr->attachments) {
                foreach ($dcr->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment);
                }
            }
            
            // Log the deletion before deleting
            AuditLog::create([
                'uuid' => Str::uuid()->toString(),
                'event_type' => 'deleted',
                'event_category' => 'dcr',
                'action' => 'Deleted DCR-' . $dcrId,
                'user_id' => $user->id,
                'resource_type' => 'ChangeRequest',
                'resource_id' => $dcr->id,
                'ip_address' => request()->ip(),
                'event_timestamp' => now(),
            ]);
            
            // Delete the DCR
            $dcr->delete();
            
            \Log::info('DCR deleted successfully', ['dcr_id' => $dcrId]);
            
            return redirect()->route('dashboard')
                ->with('success', 'DCR deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('DCR deletion failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete DCR: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified DCR.
     */
    public function show(ChangeRequest $dcr)
    {
        $user = Auth::user();

        // Check if user can view this DCR
        if (!$user->canViewDcr($dcr)) {
            abort(403, 'You do not have permission to view this DCR.');
        }
        
        // Check if user can view this DCR
        if (!$user->isAdministrator() && 
            !$user->isDecisionMaker() &&
            $dcr->author_id !== $user->id && 
            $dcr->recipient_id !== $user->id) {
            abort(403, 'Access denied');
        }
        
        // Eager load relationships to prevent N+1 queries
        $dcr = $dcr->load([
            'author',
            'recipient',
            'decisionMaker',
            'documents',
            'approvals.approver',
            'impactAssessment',
            'auditLogs.user'
        ]);
        
        // Define workflow steps
        $workflowSteps = ['Initial', 'Conceptual', 'Preliminary', 'Detailed', 'Closeout'];
        $currentStepIndex = array_search($dcr->status, $workflowSteps) ?: 0;
        
        // Check if user can perform actions
        $canPerformAction = $dcr->recipient_id === $user->id || 
                           $dcr->decision_maker_id === $user->id || 
                           $user->isAdministrator();
        
        // Get activity log (comments and updates)
        $activityLog = $dcr->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user->name ?? 'System',
                    'action' => $log->action,
                    'timestamp' => $log->created_at->diffForHumans(),
                    'type' => $log->event_type === 'approved' ? 'success' : ($log->event_type === 'rejected' ? 'error' : 'info'),
                ];
            });
        
        // Get formal audit trail (approvals/rejections only)
        $auditTrail = $dcr->auditLogs()
            ->with('user')
            ->whereIn('event_type', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user->name ?? 'System',
                    'type' => $log->event_type === 'approved' ? 'success' : 'error',
                    'timestamp' => $log->created_at->format('M d, Y H:i'),
                ];
            });
        
        // Parse attachments - fetch full document details
        $attachments = [];
        if ($dcr->attachments) {
            foreach ($dcr->attachments as $attachment) {
                if (isset($attachment['document_id'])) {
                    $document = \App\Models\Document::find($attachment['document_id']);
                    if ($document) {
                        $attachments[] = [
                            'document_id' => $document->id,
                            'original_name' => $attachment['original_name'] ?? $document->original_filename,
                            'stored_filename' => $attachment['stored_filename'] ?? $document->stored_filename,
                            'file_path' => $document->file_path,
                            'size' => $attachment['size'] ?? null,
                            'mime_type' => $attachment['mime_type'] ?? null,
                        ];
                    }
                } else {
                    // Legacy attachment format (just path string)
                    $attachments[] = [
                        'original_name' => basename($attachment),
                        'file_path' => $attachment,
                    ];
                }
            }
        }
        
        // Calculate impact cost
        $impactCost = $dcr->impactAssessment->cost_estimate ?? 0;
        $inventoryScrap = $dcr->impact === 'High';
        
        return view('dcr.show', compact(
            'dcr', 
            'workflowSteps', 
            'currentStepIndex', 
            'canPerformAction',
            'activityLog',
            'auditTrail',
            'attachments',
            'impactCost',
            'inventoryScrap'
        ));
    }

    /**
     * Approve a DCR with PIN verification.
     */
    public function approve(Request $request, ChangeRequest $dcr)
    {
        $user = Auth::user();
        
        // Check if user has permission to approve DCRs
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to approve DCRs.'
                ], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to approve DCRs.');
        }

        // Check if DCR is already approved or rejected
        if (in_array($dcr->status, ['Approved', 'Rejected', 'Completed', 'Closed'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This DCR has already been processed and cannot be approved.'
                ], 400);
            }
            return redirect()->back()->with('error', 'This DCR has already been processed.');
        }

        // Optional: Add comments with approval
        $comments = $request->input('comments');

        // Update status
        $dcr->update([
            'status' => 'Approved',
            'decision_maker_id' => $user->id,
        ]);

        // If comments provided, store them in recommendations
        if ($comments) {
            $dcr->update(['recommendations' => $comments]);
        }

        // Log the approval
        AuditLog::create([
            'uuid' => Str::uuid()->toString(),
            'event_type' => 'approved',
            'event_category' => 'Workflow',
            'action' => 'Approved DCR-' . $dcr->dcr_id . ($comments ? ' with comments' : ''),
            'user_id' => $user->id,
            'resource_type' => 'ChangeRequest',
            'resource_id' => $dcr->id,
            'ip_address' => $request->ip(),
            'event_timestamp' => now(),
        ]);

        // Send notification to author
        try {
            $dcr->author->notify(new \App\Notifications\DcrSubmittedNotification($dcr));
        } catch (\Exception $e) {
            \Log::warning('Failed to send approval notification: ' . $e->getMessage());
        }

        // Check if request expects JSON (for AJAX calls)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'DCR approved successfully',
                'dcr' => [
                    'id' => $dcr->id,
                    'dcr_id' => $dcr->dcr_id,
                    'status' => $dcr->status
                ]
            ]);
        }

        return redirect()->back()->with('success', 'DCR approved successfully');
    }

    /**
     * Reject a DCR with reason.
     */
    public function reject(Request $request, ChangeRequest $dcr)
    {
        $validator = \Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10|max:1000'
        ], [
            'rejection_reason.required' => 'Please provide a reason for rejection.',
            'rejection_reason.min' => 'Rejection reason must be at least 10 characters.',
            'rejection_reason.max' => 'Rejection reason cannot exceed 1000 characters.'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        // Check if user has permission to reject DCRs
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to reject DCRs.'
                ], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to reject DCRs.');
        }

        // Check if DCR is already approved or rejected
        if (in_array($dcr->status, ['Approved', 'Rejected', 'Completed', 'Closed'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This DCR has already been processed and cannot be rejected.'
                ], 400);
            }
            return redirect()->back()->with('error', 'This DCR has already been processed.');
        }

        // Update status
        $dcr->update([
            'status' => 'Rejected',
            'recommendations' => $request->rejection_reason,
            'decision_maker_id' => $user->id,
        ]);

        // Log the rejection
        AuditLog::create([
            'uuid' => Str::uuid()->toString(),
            'event_type' => 'rejected',
            'event_category' => 'Workflow',
            'action' => 'Rejected DCR-' . $dcr->dcr_id,
            'user_id' => $user->id,
            'resource_type' => 'ChangeRequest',
            'resource_id' => $dcr->id,
            'ip_address' => $request->ip(),
            'event_timestamp' => now(),
        ]);

        // Send notification to author
        try {
            $dcr->author->notify(new \App\Notifications\DcrSubmittedNotification($dcr));
        } catch (\Exception $e) {
            \Log::warning('Failed to send rejection notification: ' . $e->getMessage());
        }

        // Check if request expects JSON (for AJAX calls)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'DCR rejected successfully',
                'dcr' => [
                    'id' => $dcr->id,
                    'dcr_id' => $dcr->dcr_id,
                    'status' => $dcr->status
                ]
            ]);
        }

        return redirect()->back()->with('success', 'DCR rejected successfully');
    }

    /**
     * Handle file attachments
     */
    private function handleAttachments(ChangeRequestStoreRequest $request, ChangeRequest $changeRequest): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }
        
        $attachments = [];
        
        foreach ($request->file('attachments') as $key => $file) {
            if ($file->isValid()) {
                // Generate unique filename
                $filename = 'dcr_' . $changeRequest->id . '_' . $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Store file in documents disk
                $path = $file->storeAs('dcr_attachments', $filename, 'documents');
                
                // Create document record
                $document = \App\Models\Document::create([
                    'uuid' => Str::uuid(),
                    'change_request_id' => $changeRequest->id,
                    'document_type' => 'Attachment',
                    'title' => $file->getClientOriginalName(),
                    'description' => 'Attachment for DCR ' . $changeRequest->formatted_dcr_id,
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_filename' => $filename,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'file_hash' => hash('sha256', $file->get()),
                    'uploaded_by' => Auth::id(),
                ]);
                
                $attachments[] = [
                    'document_id' => $document->id,
                    'original_name' => $file->getClientOriginalName(),
                    'stored_filename' => $filename,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }
        
        // Update change request with attachment data
        if (!empty($attachments)) {
            $changeRequest->attachments = $attachments;
            $changeRequest->save();
        }
    }

    /**
     * Send notifications to relevant users
     */
    private function sendNotifications(ChangeRequest $changeRequest): void
    {
        // Notify recipient if assigned
        if ($changeRequest->recipient_id) {
            $recipient = $changeRequest->recipient;
            $recipient->notify(new DcrSubmittedNotification($changeRequest));
        }
        
        // Notify decision maker if assigned
        if ($changeRequest->decision_maker_id) {
            $decisionMaker = $changeRequest->decisionMaker;
            $decisionMaker->notify(new DcrAssignedToDomNotification($changeRequest));
        }
    }

    /**
     * Send high-impact escalation notifications
     */
    private function sendHighImpactNotifications(ChangeRequest $changeRequest): void
    {
        $assignmentService = new DcrAssignmentService();
        $escalationRecipients = $assignmentService->getEscalationRecipients();
        
        $reason = $changeRequest->impact === 'High' 
            ? 'High impact rating requires immediate attention'
            : 'Critical priority DCR requires expedited processing';
        
        foreach ($escalationRecipients as $recipient) {
            $recipient->notify(new HighImpactDcrEscalationNotification($changeRequest, $reason));
        }
        
        // Mark as escalated
        if ($changeRequest->impact === 'High') {
            $changeRequest->update(['auto_escalated' => true]);
        }
    }

    /**
     * Submit DCR for review
     */
    public function submit(ChangeRequest $dcr)
    {
        $user = Auth::user();
        
        // Check authorization - user must be author or admin
        if ($dcr->author_id !== $user->id && !$user->isAdministrator()) {
            abort(403, 'Access denied');
        }
        
        try {
            $dcr->submit();
            
            return redirect()->route('dcr.show', $dcr)
                ->with('success', 'DCR submitted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to submit DCR: ' . $e->getMessage());
        }
    }

    /**
     * Get available recipients for selection
     */
    public function getRecipients(Request $request): JsonResponse
    {
        $searchQuery = $request->get('q', '');
        
        $recipients = User::whereHas('activeRoles', function ($query) use ($searchQuery) {
                $query->where('name', 'recipient');
            })
            ->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('email', 'like', "%{$searchQuery}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'department']);
        
        return response()->json($recipients);
    }

    /**
     * Get available decision makers for selection
     */
    public function getDecisionMakers(Request $request): JsonResponse
    {
        $searchQuery = $request->get('q', '');
        
        $decisionMakers = User::whereHas('activeRoles', function ($query) use ($searchQuery) {
                $query->where('name', 'dom');
            })
            ->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('email', 'like', "%{$searchQuery}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'department']);
        
        return response()->json($decisionMakers);
    }

    // ==================== COMPLIANCE METHODS ====================

    /**
     * Verify DCR
     */
    public function verify(ChangeRequest $dcr)
    {
        try {
            if ($dcr->isReadOnly()) {
                return back()->with('error', 'Cannot verify: DCR is locked or closed.');
            }

            $dcr->verify(auth()->id(), request('verification_notes'));

            return back()->with('success', 'DCR verified successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Validate DCR
     */
    public function validate(ChangeRequest $dcr)
    {
        try {
            if ($dcr->isReadOnly()) {
                return back()->with('error', 'Cannot validate: DCR is locked or closed.');
            }

            $dcr->validate(auth()->id(), request('validation_notes'));

            return back()->with('success', 'DCR validated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process DCR closure with full compliance
     */
    public function processClosure(ChangeRequest $dcr)
    {
        try {
            $validated = request()->validate([
                'closure_notes' => 'required|string|min:10',
                'checklist' => 'array',
                'checklist.*' => 'boolean'
            ]);

            $dcr->closeDcr(
                auth()->id(),
                $validated['closure_notes'],
                $validated['checklist'] ?? []
            );

            return back()->with('success', 'DCR closed successfully and locked for compliance.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Lock DCR
     */
    public function lock(ChangeRequest $dcr)
    {
        try {
            if ($dcr->is_locked) {
                return back()->with('error', 'DCR is already locked.');
            }

            $dcr->lockRecord(auth()->id(), request('lock_reason', 'Manually locked'));

            return back()->with('success', 'DCR locked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Unlock DCR (Admin only)
     */
    public function unlock(ChangeRequest $dcr)
    {
        try {
            if (!auth()->user()->isAdministrator()) {
                return back()->with('error', 'Only administrators can unlock DCRs.');
            }

            $dcr->unlockRecord(auth()->id(), request('unlock_reason', 'Manually unlocked'));

            return back()->with('success', 'DCR unlocked successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Serve a document file for viewing
     */
    public function serveDocument(ChangeRequest $dcr, $filename)
    {
        $user = Auth::user();

        // Check if user can view this DCR
        if (!$user->canViewDcr($dcr)) {
            abort(403, 'You do not have permission to view this DCR.');
        }

        // Find the document by filename
        $document = \App\Models\Document::where('change_request_id', $dcr->id)
            ->where('stored_filename', $filename)
            ->first();

        if (!$document) {
            abort(404, 'Document not found');
        }

        // Build the full path
        $path = storage_path('app/documents/' . $document->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found on disk');
        }

        // Return the file with appropriate headers for inline viewing
        return response()->file($path, [
            'Content-Type' => $document->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"'
        ]);
    }

    /**
     * Archive DCR documents
     */
    public function archive(ChangeRequest $dcr)
    {
        try {
            if (!$dcr->canBeClosed()) {
                return back()->with('error', 'DCR must be completed before archiving.');
            }

            $dcr->archiveDocuments(auth()->id());

            return back()->with('success', 'DCR documents archived successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ... existing methods for approval, rejection, etc.
}
