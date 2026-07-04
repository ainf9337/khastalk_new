<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\EmergencyAlert;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyController extends Controller
{
    public function index()
    {
        $teacher  = Auth::user();
        $class    = $teacher->teacherClass;
        $students = $class
            ? Student::where('class_id', $class->id)->orderBy('name')->get()
            : collect();

        $alerts = EmergencyAlert::where('teacher_id', $teacher->id)
            ->with(['student', 'student.parent'])
            ->latest()
            ->take(10)
            ->get();

        $alertTypes = [
            'Seizure', 'Allergic reaction', 'Injury',
            'Aggression — self-harm risk',
            'Wandering / elopement', 'Other emergency',
        ];

        return view('teacher.emergency', compact(
            'students', 'alerts', 'alertTypes', 'teacher'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'alert_type' => 'required|string',
        ]);

        $teacher = Auth::user();

        $alert = EmergencyAlert::create([
            'student_id'  => $request->student_id,
            'teacher_id'  => $teacher->id,
            'alert_type'  => $request->alert_type,
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        // Notify parent via message
        $student = Student::find($request->student_id);
        if ($student && $student->parent_id) {
            Message::create([
                'sender_id'   => $teacher->id,
                'receiver_id' => $student->parent_id,
                'student_id'  => $student->id,
                'content'     => "🚨 EMERGENCY ALERT: {$request->alert_type} involving "
                               . "{$student->name}. Please confirm receipt immediately. "
                               . "Details: {$request->description}",
            ]);
        }

        return redirect()->route('teacher.emergency')
            ->with('success', 'Emergency alert sent! Parent has been notified.');
    }
}
