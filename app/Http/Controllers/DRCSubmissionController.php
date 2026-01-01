<?php

namespace App\Http\Controllers;

use App\Models\DRCSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DRCSubmissionController extends Controller
{
    public function create()
    {
        return view('drc_submission.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'details' => 'required|string',
            'recipient' => 'required|string',
            'file_upload' => 'nullable|file|max:10240', // max 10MB
        ]);

        $drcId = 'DRC-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $submissionDate = now()->toDateString();
        $author = Auth::user()->name;

        $filePath = null;
        if ($request->hasFile('file_upload') && $request->file('file_upload')->isValid()) {
            $filePath = $request->file('file_upload')->store('uploads', 'public');
        }

        $submission = DRCSubmission::create([
            'drc_id' => $drcId,
            'submission_date' => $submissionDate,
            'author' => $author,
            'recipient' => $request->recipient,
            'details' => $request->details,
            'file_path' => $filePath,
        ]);

        // Send email notification
        Mail::raw("New DRC Submission: {$drcId}", function ($message) use ($submission) {
            $message->to($submission->recipient)
                    ->subject('New DRC Submission Received');
        });

        return redirect()->route('recipient.dashboard')->with('success', 'DRC Submission created successfully.');
    }
}
