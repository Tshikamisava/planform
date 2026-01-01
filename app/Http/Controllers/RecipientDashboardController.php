<?php

namespace App\Http\Controllers;

use App\Models\DRCSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipientDashboardController extends Controller
{
    public function index()
    {
        $recipientName = Auth::user()->name;
        $submissions = DRCSubmission::where('recipient', $recipientName)->get();
        return view('recipient.dashboard', compact('submissions'));
    }
}
