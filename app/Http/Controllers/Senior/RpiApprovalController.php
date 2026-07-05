<?php
namespace App\Http\Controllers\Senior;

use App\Http\Controllers\Controller;
use App\Models\RpiDocument;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RpiApprovalController extends Controller
{
    public function index()
    {
        $allRpis = RpiDocument::with(['student', 'createdBy', 'goals'])
            ->orderByRaw("FIELD(status, 'pending_approval', 'draft', 'approved', 'rejected')")
            ->latest()
            ->get();

        return view('senior.rpi-approval', compact('allRpis'));
    }

    public function show(RpiDocument $rpi)
    {
        $rpi->load(['student', 'createdBy', 'goals']);

        return view('senior.rpi-approval-show', compact('rpi'));
    }

    public function update(Request $request, RpiDocument $rpi)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';

        $rpi->update([
            'status'      => $status,
            'approved_by' => Auth::id(),
        ]);

        ActivityLog::record(
            Auth::id(),
            'RPI ' . ucfirst($status),
            "RPI for {$rpi->student->name} (Period: {$rpi->period}) was {$status}"
        );

        $message = $request->action === 'approve'
            ? 'RPI document approved successfully.'
            : 'RPI document rejected. The teacher will be notified.';

        return redirect()->route('senior.rpi-approval')
            ->with('success', $message);
    }
}
