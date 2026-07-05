<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\RpiDocument;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RpiProgressController extends Controller
{
    public function index(Request $request)
    {
        $parent   = Auth::user();
        $children = $parent->children;

        $selectedId = (int) $request->get('id', $children->first()?->id);

        abort_unless(
            !$selectedId || $children->pluck('id')->contains($selectedId),
            403
        );

        $selectedChild = $children->find($selectedId);

        // Show all RPI docs (approved AND pending) so parent can see progress
        $rpis = $selectedChild
            ? RpiDocument::where('student_id', $selectedChild->id)
                ->whereIn('status', ['approved', 'pending_approval'])
                ->with(['goals', 'createdBy'])
                ->latest()
                ->get()
            : collect();

        $unreadMessages = Message::where('receiver_id', $parent->id)
            ->where('is_read', false)->count();

        return view('parent.rpi-progress', compact(
            'children', 'selectedChild', 'rpis', 'unreadMessages'
        ));
    }
}
