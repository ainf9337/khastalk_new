<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\BehaviourLog;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BehaviourHistoryController extends Controller
{
    public function index(Request $request)
    {
        $parent   = Auth::user();
        $children = $parent->children;

        $selectedId = (int) $request->get('id', $children->first()?->id);

        // Verify ownership
        abort_unless(
            !$selectedId || $children->pluck('id')->contains($selectedId),
            403
        );

        $selectedChild = $children->find($selectedId);

        $logs = $selectedChild
            ? BehaviourLog::where('student_id', $selectedChild->id)
                ->with('teacher')
                ->latest('logged_at')
                ->get()
            : collect();

        $unreadMessages = Message::where('receiver_id', $parent->id)
            ->where('is_read', false)->count();

        return view('parent.behaviour-history', compact(
            'children', 'selectedChild', 'logs', 'unreadMessages'
        ));
    }
}
