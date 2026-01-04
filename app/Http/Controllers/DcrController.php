<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\User;
use App\Http\Requests\ChangeRequestStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\DcrSubmitted;

class DcrController extends Controller
{
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
     * Display the approval dashboard.
     */
    public function approvalDashboard()
    {
        $user = Auth::user();
        
        if (!$user->isDecisionMaker() && !$user->isAdministrator()) {
            abort(403, 'Access denied');
        }
        
        $pendingDcrs = ChangeRequest::whereIn('status', ['Pending', 'In_Review'])
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
     * Show the form for creating a new DCR.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAuthor() && !$user->isAdministrator()) {
            abort(403, 'Access denied');
        }
        
        // Get available recipients and decision makers
        $recipients = User::whereHas('activeRoles', function ($query) {
            $query->where('name', 'recipient');
        })->get();
        
        $decisionMakers = User::whereHas('activeRoles', function ($query) {
            $query->where('name', 'dom');
        })->get();
        
        return view('dcr.create', compact('recipients', 'decisionMakers'));
    }

    /**
     * Store a newly created DCR in storage.
     */
    public function store(ChangeRequestStoreRequest $request)
    {
        try {
            // Create the change request
            $changeRequest = ChangeRequest::create($request->validated());
            
            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $this->handleAttachments($request, $changeRequest);
            }
            
            // Send notifications
            $this->sendNotifications($changeRequest);
            
            // Create audit log
            $changeRequest->auditLogs()->create([
                'event_type' => 'DCR_CREATED',
                'event_category' => 'Workflow',
                'action' => 'DCR created',
                'user_id' => auth()->id(),
                'resource_type' => 'change_request',
                'resource_id' => $changeRequest->id,
                'success' => true,
                'event_timestamp' => now(),
            ]);
            
            return redirect()->route('dcr.show', $changeRequest)
                ->with('success', 'DCR created successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create DCR: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified DCR.
     */
    public function show(ChangeRequest $changeRequest)
    {
        $user = Auth::user();
        
        // Check if user can view this DCR
        if (!$user->isAdministrator() && 
            !$user->isDecisionMaker() &&
            $changeRequest->author_id !== $user->id && 
            $changeRequest->recipient_id !== $user->id) {
            abort(403, 'Access denied');
        }
        
        return view('dcr.show', compact('changeRequest'));
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
                    'uploaded_by' => auth()->id(),
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
            
            // Create notification record
            \App\Models\Notification::create([
                'uuid' => Str::uuid(),
                'type' => 'DCR_ASSIGNED',
                'category' => 'Info',
                'title' => 'New DCR Assigned',
                'message' => 'DCR ' . $changeRequest->formatted_dcr_id . ' has been assigned to you for implementation.',
                'user_id' => $recipient->id,
                'related_resource_type' => 'change_request',
                'related_resource_id' => $changeRequest->id,
                'action_url' => route('dcr.show', $changeRequest),
                'priority' => $changeRequest->priority === 'Critical' ? 'High' : 'Normal',
                'scheduled_at' => now(),
            ]);
            
            // Send email notification
            try {
                Mail::to($recipient->email)->send(new DcrSubmitted($changeRequest));
            } catch (\Exception $e) {
                \Log::error('Failed to send DCR notification email: ' . $e->getMessage());
            }
        }
        
        // Notify decision maker if assigned
        if ($changeRequest->decision_maker_id) {
            $decisionMaker = $changeRequest->decisionMaker;
            
            // Create notification record
            \App\Models\Notification::create([
                'uuid' => Str::uuid(),
                'type' => 'DCR_REVIEW',
                'category' => 'Info',
                'title' => 'DCR Pending Review',
                'message' => 'DCR ' . $changeRequest->formatted_dcr_id . ' is pending your review and approval.',
                'user_id' => $decisionMaker->id,
                'related_resource_type' => 'change_request',
                'related_resource_id' => $changeRequest->id,
                'action_url' => route('dcr.approval.dashboard'),
                'priority' => $changeRequest->priority === 'Critical' ? 'High' : 'Normal',
                'scheduled_at' => now(),
            ]);
        }
    }

    /**
     * Submit DCR for review
     */
    public function submit(ChangeRequest $changeRequest)
    {
        $user = Auth::user();
        
        if (!$user->can('edit-dcr', $changeRequest)) {
            abort(403, 'Access denied');
        }
        
        try {
            $changeRequest->submit();
            
            return redirect()->route('dcr.show', $changeRequest)
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

    // ... existing methods for approval, rejection, etc.
}
