<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\EmergencyAlert;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $parent   = Auth::user();
        $children = $parent->children()->with(['profile', 'classRoom.teacher'])->get();

        // Today's logs for each child
        $todayLogs = [];
        foreach ($children as $child) {
            $todayLogs[$child->id] = $child->behaviourLogs()
                ->whereDate('logged_at', today())
                ->latest('logged_at')
                ->get();
        }

        // Pending emergency alerts
        $pendingAlerts = EmergencyAlert::whereIn('student_id', $children->pluck('id'))
            ->where('status', 'pending')
            ->with(['student', 'teacher'])
            ->latest()
            ->get();

        $unreadMessages = Message::where('receiver_id', $parent->id)
            ->where('is_read', false)
            ->count();

        $hour     = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

        return view('parent.dashboard', compact(
            'parent', 'children', 'todayLogs',
            'pendingAlerts', 'unreadMessages', 'greeting'
        ));
    }

    public function confirmAlert(EmergencyAlert $alert)
    {
        // Make sure this alert belongs to this parent's child
        $parent   = Auth::user();
        $childIds = $parent->children->pluck('id');
        abort_unless($childIds->contains($alert->student_id), 403);

        $alert->update([
            'status'               => 'confirmed',
            'parent_confirmed_at'  => now(),
        ]);

        return redirect()->route('parent.dashboard')
            ->with('success', 'Emergency alert confirmed. Thank you.');
    }
}
