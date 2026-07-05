<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChildProfileController extends Controller
{
    public function show(Student $student)
    {
        $parent = Auth::user();

        // Make sure this child belongs to this parent
        abort_unless(
            $parent->children->pluck('id')->contains($student->id),
            403
        );

        $student->load(['profile', 'classRoom.teacher']);

        $unreadMessages = Message::where('receiver_id', $parent->id)
            ->where('is_read', false)->count();

        return view('parent.child-profile', compact(
            'student', 'unreadMessages', 'parent'
        ));
    }
}
