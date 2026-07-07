<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
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

    public function updateProfile(Request $request, Student $student)
{
    $parent = Auth::user();

    abort_unless(
        $parent->children->pluck('id')->contains($student->id),
        403
    );

    $request->validate([
        'sensory_triggers'    => 'nullable|string|max:500',
        'calming_strategies'  => 'nullable|string|max:500',
        'medical_info'        => 'nullable|string|max:500',
        'communication_level' => 'nullable|string|max:100',
    ]);

    // Create profile if it doesn't exist yet
    $student->profile()->updateOrCreate(
        ['student_id' => $student->id],
        [
            'sensory_triggers'    => $request->sensory_triggers,
            'calming_strategies'  => $request->calming_strategies,
            'medical_info'        => $request->medical_info,
            'communication_level' => $request->communication_level,
        ]
    );

    \App\Models\ActivityLog::record(
        $parent->id,
        'Student Profile Updated',
        "Parent updated profile info for {$student->name}"
    );

    return redirect()->route('parent.child.show', $student)
        ->with('success', "Profile for {$student->name} updated successfully.");
}
}
