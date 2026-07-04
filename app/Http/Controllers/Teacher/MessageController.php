<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $teacher     = Auth::user();
        $withParent  = (int) $request->parent_id;
        $withStudent = (int) $request->student_id;

        // All unique conversations
        $rawConvs = Message::where('sender_id', $teacher->id)
            ->orWhere('receiver_id', $teacher->id)
            ->with(['sender', 'receiver', 'student'])
            ->latest()
            ->get();

        $conversations = collect();
        $seen          = [];

        foreach ($rawConvs as $msg) {
            $otherId   = $msg->sender_id === $teacher->id
                       ? $msg->receiver_id
                       : $msg->sender_id;
            $key       = $otherId . '_' . $msg->student_id;

            if (!in_array($key, $seen)) {
                $seen[] = $key;
                $unread = Message::where('sender_id', $otherId)
                    ->where('receiver_id', $teacher->id)
                    ->where('student_id', $msg->student_id)
                    ->where('is_read', false)
                    ->count();

                $conversations->push([
                    'other_id'      => $otherId,
                    'other_name'    => $otherId === $msg->sender_id
                                     ? $msg->sender->name
                                     : $msg->receiver->name,
                    'student_id'    => $msg->student_id,
                    'student_name'  => $msg->student->name,
                    'last_message'  => $msg->content,
                    'unread'        => $unread,
                    'sent_at'       => $msg->created_at,
                ]);
            }
        }

        // Mark as read
        $thread       = collect();
        $activeParent = null;
        $activeStudent= null;

        if ($withParent && $withStudent) {
            Message::where('sender_id', $withParent)
                ->where('receiver_id', $teacher->id)
                ->where('student_id', $withStudent)
                ->update(['is_read' => true]);

            $thread = Message::where('student_id', $withStudent)
                ->where(function ($q) use ($teacher, $withParent) {
                    $q->where(fn($q) =>
                        $q->where('sender_id', $teacher->id)
                          ->where('receiver_id', $withParent))
                      ->orWhere(fn($q) =>
                        $q->where('sender_id', $withParent)
                          ->where('receiver_id', $teacher->id));
                })
                ->with('sender')
                ->oldest()
                ->get();

            $activeParent  = \App\Models\User::find($withParent);
            $activeStudent = Student::find($withStudent);
        }

        $unreadTotal = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        return view('teacher.messages', compact(
            'conversations', 'thread', 'activeParent',
            'activeStudent', 'withParent', 'withStudent', 'unreadTotal'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content'     => 'required|string',
            'receiver_id' => 'required|exists:users,id',
            'student_id'  => 'required|exists:students,id',
        ]);

        Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'student_id'  => $request->student_id,
            'content'     => $request->content,
        ]);

        return redirect()->route('teacher.messages', [
            'parent_id'  => $request->receiver_id,
            'student_id' => $request->student_id,
        ]);
    }
}
