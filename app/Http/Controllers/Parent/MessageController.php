<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $parent      = Auth::user();
        $withTeacher = (int) $request->teacher_id;
        $withStudent = (int) $request->student_id;

        // Get parent's children with their teachers
        $children = $parent->children()->with('classRoom.teacher')->get();

        // Mark messages as read
        if ($withTeacher && $withStudent) {
            Message::where('sender_id', $withTeacher)
                ->where('receiver_id', $parent->id)
                ->where('student_id', $withStudent)
                ->update(['is_read' => true]);
        }

        // Build thread
        $thread        = collect();
        $activeTeacher = null;
        $activeStudent = null;

        if ($withTeacher && $withStudent) {
            $thread = Message::where('student_id', $withStudent)
                ->where(function ($q) use ($parent, $withTeacher) {
                    $q->where(fn($q) =>
                        $q->where('sender_id', $parent->id)
                          ->where('receiver_id', $withTeacher))
                      ->orWhere(fn($q) =>
                        $q->where('sender_id', $withTeacher)
                          ->where('receiver_id', $parent->id));
                })
                ->oldest()
                ->get();

            $activeTeacher = User::find($withTeacher);
            $activeStudent = Student::find($withStudent);
        }

        $unreadMessages = Message::where('receiver_id', $parent->id)
            ->where('is_read', false)->count();

        return view('parent.messages', compact(
            'children', 'thread', 'activeTeacher', 'activeStudent',
            'withTeacher', 'withStudent', 'unreadMessages'
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

        return redirect()->route('parent.messages', [
            'teacher_id' => $request->receiver_id,
            'student_id' => $request->student_id,
        ]);
    }
}
