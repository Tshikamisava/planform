<?php

namespace App\Http\Controllers;

use App\Models\Dcr;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\DcrSubmitted;

class DcrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'Admin') {
            // Admins can see all DCRs
            $submittedDcrs = Dcr::where('author_id', $user->id)->get();
            $assignedDcrs = Dcr::where('recipient_id', $user->id)->get();
        } elseif ($user->role === 'Author') {
            // Authors see their submitted DCRs
            $submittedDcrs = Dcr::where('author_id', $user->id)->get();
            $assignedDcrs = collect(); // Empty collection
        } else {
            // Recipients see their assigned DCRs
            $submittedDcrs = collect(); // Empty collection
            $assignedDcrs = Dcr::where('recipient_id', $user->id)->get();
        }
        
        return view('dcr.dashboard', compact('submittedDcrs', 'assignedDcrs'));
    }

    /**
     * Display the approval dashboard.
     */
    public function approvalDashboard()
    {
        $user = Auth::user();
        
        // Only recipients and admins can access the approval dashboard
        if ($user->role === 'Author') {
            return redirect()->route('dcr.dashboard')->with('error', 'Access denied. Approval dashboard is for reviewers only.');
        }
        
        // Get pending DCRs for the user
        $pendingDcrs = Dcr::where('recipient_id', $user->id)
            ->where('status', 'Pending')
            ->with(['author', 'escalatedTo'])
            ->get();
            
        // If admin, also get escalated DCRs
        if ($user->role === 'Admin') {
            $escalatedDcrs = Dcr::where('auto_escalated', true)
                ->where('status', 'Pending')
                ->with(['author', 'recipient', 'escalatedTo'])
                ->get();
            $pendingDcrs = $pendingDcrs->merge($escalatedDcrs);
        }
        
        // Get statistics
        $highImpactDcrs = $pendingDcrs->where('impact_rating', 'High');
        $escalatedDcrs = $pendingDcrs->where('auto_escalated', true);
        $approvedToday = Dcr::where('status', 'Approved')
            ->whereDate('updated_at', today())
            ->where(function($query) use ($user) {
                $query->where('recipient_id', $user->id)
                      ->orWhere('escalated_to', $user->id);
            })->get();
        
        return view('dcr.approval-dashboard', compact(
            'pendingDcrs', 
            'highImpactDcrs', 
            'escalatedDcrs', 
            'approvedToday'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        
        if ($users->isEmpty()) {
            return redirect()->route('dcr.dashboard')
                ->with('error', 'No users available to assign DCR to. Please contact an administrator to add more users.');
        }
        
        return view('dcr.create', compact('users'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Dcr $dcr)
    {
        // Check if user is authorized to view this DCR
        if ($dcr->author_id !== Auth::id() && 
            $dcr->recipient_id !== Auth::id() && 
            Auth::user()->role !== 'Admin') {
            return redirect()->route('dcr.dashboard')->with('error', 'You are not authorized to view this DCR.');
        }

        return view('dcr.show', compact('dcr'));
    }

    /**
     * Show the impact rating form for a DCR.
     */
    public function impactRating(Dcr $dcr)
    {
        // Check if user is authorized to rate this DCR
        if ($dcr->recipient_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            return redirect()->route('dcr.show', $dcr)->with('error', 'You are not authorized to rate this DCR.');
        }

        // Check if DCR is still pending
        if ($dcr->status !== 'Pending') {
            return redirect()->route('dcr.show', $dcr)->with('error', 'Impact rating can only be done on pending DCRs.');
        }

        return view('dcr.impact-rating', compact('dcr'));
    }

    /**
     * Store the impact rating for a DCR.
     */
    public function storeImpactRating(Request $request, Dcr $dcr)
    {
        // Check authorization
        if ($dcr->recipient_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            return redirect()->route('dcr.show', $dcr)->with('error', 'You are not authorized to rate this DCR.');
        }

        $validated = $request->validate([
            'impact_rating' => 'required|in:Low,Medium,High',
            'impact_summary' => 'required|string',
            'recommendations' => 'nullable|string',
            'auto_escalate' => 'boolean',
        ]);

        // Update DCR with impact rating
        $dcr->update([
            'impact_rating' => $validated['impact_rating'],
            'impact_summary' => $validated['impact_summary'],
            'recommendations' => $validated['recommendations'] ?? null,
        ]);

        // Handle auto-escalation for high impact
        if ($validated['impact_rating'] === 'High' && $validated['auto_escalate']) {
            $this->autoEscalateDcr($dcr);
        }

        return redirect()->route('dcr.show', $dcr)
            ->with('success', "Impact rating ({$validated['impact_rating']}) has been assigned to DCR {$dcr->dcr_id}.");
    }

    /**
     * Auto-escalate a high-impact DCR to senior management.
     */
    private function autoEscalateDcr(Dcr $dcr)
    {
        // Find a senior manager or admin user for escalation
        $escalatedTo = User::where('role', 'Admin')->first();
        
        if ($escalatedTo) {
            $dcr->update([
                'auto_escalated' => true,
                'escalated_at' => now(),
                'escalated_to' => $escalatedTo->id,
            ]);

            // Log the escalation
            \Log::info("DCR {$dcr->dcr_id} auto-escalated to {$escalatedTo->name} due to high impact rating");

            // Send notification to escalated user (email can be implemented later)
            // try {
            //     Mail::to($escalatedTo->email)->send(new DcrEscalated($dcr));
            // } catch (\Exception $e) {
            //     \Log::error('Escalation email failed: ' . $e->getMessage());
            // }
        }
    }

    /**
     * View PDF attachment
     */
    public function viewPdf(Dcr $dcr, $attachment)
    {
        // Check if user is authorized to view this DCR
        if ($dcr->author_id !== Auth::id() && 
            $dcr->recipient_id !== Auth::id() && 
            Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized');
        }

        // Get attachments array
        $attachments = json_decode($dcr->attachments, true) ?? [];
        
        // Find the requested attachment
        $attachmentPath = null;
        $filename = null;
        
        foreach ($attachments as $att) {
            if (basename($att) === $attachment || $att === $attachment) {
                $attachmentPath = $att;
                $filename = basename($att);
                break;
            }
        }
        
        if (!$attachmentPath) {
            abort(404, 'Attachment not found');
        }
        
        // Check if file exists
        $fullPath = storage_path('app/public/' . $attachmentPath);
        if (!file_exists($fullPath)) {
            abort(404, 'File not found');
        }
        
        // Check if it's a PDF file
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            // For non-PDF files, redirect to download
            return response()->download($fullPath, $filename);
        }
        
        // Generate PDF URL
        $pdfUrl = asset('storage/' . $attachmentPath);
        
        return view('dcr.pdf-viewer', [
            'pdfUrl' => $pdfUrl,
            'filename' => $filename,
            'dcr' => $dcr
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'request_type' => 'required|string|max:255',
            'reason_for_change' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:2048'
        ]);

        // Generate DCR ID
        $dcr_id = 'DCR-' . date('Ymd') . '-' . (Dcr::count() + 1);

        $dcr = Dcr::create([
            'dcr_id' => $dcr_id,
            'author_id' => Auth::id(),
            'recipient_id' => $validated['recipient_id'],
            'request_type' => $validated['request_type'],
            'reason_for_change' => $validated['reason_for_change'],
            'due_date' => $validated['due_date'],
            'status' => 'Pending', // Ensure status is set
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $filePaths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $filePaths[] = $path;
            }
            if (!empty($filePaths)) {
                $dcr->attachments = json_encode($filePaths);
                $dcr->save();
            }
        }

        // Send email notification to the recipient
        // Temporarily disabled to isolate submission issues
        /*
        $recipient = User::find($dcr->recipient_id);
        if ($recipient) {
            try {
                Mail::to($recipient->email)->send(new DcrSubmitted($dcr));
            } catch (\Exception $e) {
                // Log email error but don't stop the process
                \Log::error('Email notification failed: ' . $e->getMessage());
            }
        }
        */

        return redirect()->route('dcr.dashboard')
            ->with('success', "DCR {$dcr->dcr_id} submitted successfully! It has been assigned to {$dcr->recipient->name} for review.");
    }

    /**
     * Approve a DCR
     */
    public function approve(Request $request, Dcr $dcr)
    {
        // Check if user is authorized to approve this DCR
        if ($dcr->recipient_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to approve this DCR.']);
            }
            return redirect()->route('dcr.dashboard')->with('error', 'You are not authorized to approve this DCR.');
        }

        $dcr->status = 'Approved';
        $dcr->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "DCR {$dcr->dcr_id} approved successfully!"]);
        }

        return redirect()->route('dcr.dashboard')->with('success', "DCR {$dcr->dcr_id} approved successfully!");
    }

    /**
     * Reject a DCR
     */
    public function reject(Request $request, Dcr $dcr)
    {
        // Check if user is authorized to reject this DCR
        if ($dcr->recipient_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to reject this DCR.']);
            }
            return redirect()->route('dcr.dashboard')->with('error', 'You are not authorized to reject this DCR.');
        }

        $dcr->status = 'Rejected';
        $dcr->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "DCR {$dcr->dcr_id} has been successfully rejected. The request for change has been declined."]);
        }

        return redirect()->route('dcr.dashboard')->with('success', "DCR {$dcr->dcr_id} has been successfully rejected. The request for change has been declined.");
    }

    /**
     * Approve a DCR with recommendations
     */
    public function approveWithRecommendations(Request $request, Dcr $dcr)
    {
        // Check if user is authorized to approve this DCR
        if ($dcr->recipient_id !== Auth::id() && Auth::user()->role !== 'Admin') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You are not authorized to approve this DCR.']);
            }
            return redirect()->route('dcr.dashboard')->with('error', 'You are not authorized to approve this DCR.');
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
            'recommendations' => 'required|string',
        ]);

        $dcr->status = 'Approved';
        $dcr->recommendations = $validated['recommendations'];
        $dcr->decision_maker_id = Auth::id();
        $dcr->save();

        $message = "DCR {$dcr->dcr_id} approved with recommendations! Implementation guidelines have been provided.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('dcr.approval.dashboard')->with('success', $message);
    }
}
