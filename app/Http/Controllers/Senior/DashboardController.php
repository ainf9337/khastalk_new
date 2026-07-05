<?php
namespace App\Http\Controllers\Senior;

use App\Http\Controllers\Controller;
use App\Models\RpiDocument;

class DashboardController extends Controller
{
    public function index()
    {
        $pending       = RpiDocument::where('status', 'pending_approval')
                            ->with(['student', 'createdBy', 'goals'])
                            ->latest()
                            ->get();

        $totalApproved = RpiDocument::where('status', 'approved')->count();
        $totalRejected = RpiDocument::where('status', 'rejected')->count();

        return view('senior.dashboard', compact(
            'pending', 'totalApproved', 'totalRejected'
        ));
    }
}
