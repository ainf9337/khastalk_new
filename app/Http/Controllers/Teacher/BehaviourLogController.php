<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\BehaviourLog;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BehaviourLogController extends Controller
{
    public function create(Request $request)
    {
        $teacher  = Auth::user();
        $class    = $teacher->teacherClass;
        $students = $class
            ? Student::where('class_id', $class->id)->orderBy('name')->get()
            : collect();

        $preStudentId = $request->student_id;

        $behaviourTypes  = ['Tantrum','Meltdown','Stimming','Aggression','Self-injury','Refusal','Other'];
        $triggerOptions  = ['Loud noise / sensory','Transition','Routine change','Demand placed','Peer interaction','Hunger / tired','Unknown'];
        $responseOptions = ['Sensory break','Calming corner','Verbal redirect','Fidget tool','Deep breathing','Removed from area','Physical support'];
        $durationOptions = ['< 5 minutes','5 – 10 minutes','10 – 20 minutes','20 – 30 minutes','> 30 minutes'];

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        return view('teacher.behaviour-log', compact(
            'students', 'preStudentId', 'behaviourTypes',
            'triggerOptions', 'responseOptions',
            'durationOptions', 'unreadMessages'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'behaviour_type'=> 'required|string',
            'severity'      => 'required|integer|min:1|max:5',
            'duration'      => 'nullable|string',
        ]);

        $teacher = Auth::user();

        $log = BehaviourLog::create([
            'student_id'       => $request->student_id,
            'teacher_id'       => $teacher->id,
            'behaviour_type'   => $request->behaviour_type,
            'severity'         => $request->severity,
            'duration'         => $request->duration,
            'triggers'         => $request->triggers
                                    ? implode(', ', $request->triggers)
                                    : null,
            'teacher_response' => $request->responses
                                    ? implode(', ', $request->responses)
                                    : null,
            'resolved'         => $request->boolean('resolved'),
            'notes'            => $request->notes,
            'logged_at'        => now(),
        ]);

        // Notify parent if toggled
        if ($request->boolean('notify_parent')) {
            $student = Student::find($request->student_id);
            if ($student && $student->parent_id) {
                $msg = "📋 Behaviour Log: {$log->behaviour_type} · "
                     . "Severity {$log->severity} · {$log->duration}. "
                     . "Triggers: {$log->triggers}. "
                     . "Response: {$log->teacher_response}. "
                     . ($log->resolved ? 'Resolved ✓' : 'Unresolved.');

                Message::create([
                    'sender_id'   => $teacher->id,
                    'receiver_id' => $student->parent_id,
                    'student_id'  => $student->id,
                    'content'     => $msg,
                ]);
            }
        }

        return redirect()->route('teacher.dashboard', ['logged' => 1])
            ->with('success', 'Behaviour log saved successfully.');
    }
}
