<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\RpiDocument;
use App\Models\RpiGoal;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RpiController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $rpis    = RpiDocument::where('created_by', $teacher->id)
            ->with(['student', 'goals', 'approvedBy'])
            ->latest()
            ->get();

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        return view('teacher.rpi', compact('rpis', 'unreadMessages', 'teacher'));
    }

    public function create()
    {
        $teacher  = Auth::user();
        $class    = $teacher->teacherClass;
        $students = $class
            ? Student::where('class_id', $class->id)->orderBy('name')->get()
            : collect();

        return view('teacher.rpi-create', compact('students', 'teacher'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'period'     => 'required|string',
        ]);

        $rpi = RpiDocument::create([
            'student_id' => $request->student_id,
            'created_by' => Auth::id(),
            'period'     => $request->period,
            'status'     => 'draft',
        ]);

        return redirect()->route('teacher.rpi.show', $rpi)
            ->with('success', 'RPI document created. Now add goals.');
    }

    public function show(RpiDocument $rpi)
    {
        $teacher = Auth::user();
        abort_if($rpi->created_by !== $teacher->id, 403);

        $rpi->load(['student', 'goals', 'approvedBy']);

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        return view('teacher.rpi-show', compact('rpi', 'unreadMessages', 'teacher'));
    }

    public function addGoal(Request $request, RpiDocument $rpi)
    {
        abort_if($rpi->created_by !== Auth::id(), 403);

        $request->validate([
            'goal_description' => 'required|string',
        ]);

        RpiGoal::create([
            'rpi_id'           => $rpi->id,
            'goal_description' => $request->goal_description,
            'strategy'         => $request->strategy,
            'target_date'      => $request->target_date,
        ]);

        return redirect()->route('teacher.rpi.show', $rpi)
            ->with('success', 'Goal added.');
    }

    public function updateGoal(Request $request, RpiGoal $goal)
    {
        abort_if($goal->rpiDocument->created_by !== Auth::id(), 403);

        $goal->update([
            'progress_percentage' => $request->progress,
            'status'              => $request->status,
        ]);

        return redirect()->route('teacher.rpi.show', $goal->rpi_id)
            ->with('success', 'Progress updated.');
    }

    public function submit(RpiDocument $rpi)
    {
        abort_if($rpi->created_by !== Auth::id(), 403);
        $rpi->update(['status' => 'pending_approval']);

        return redirect()->route('teacher.rpi')
            ->with('success', 'RPI submitted for approval.');
    }
}
