<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $teacher  = Auth::user();
        $class    = $teacher->teacherClass;
        $students = collect();

        if ($class) {
            $query = Student::where('class_id', $class->id)
                ->with(['profile', 'behaviourLogs' => function ($q) {
                    $q->whereDate('logged_at', today());
                }]);

            if ($request->filled('q')) {
                $query->where('name', 'like', '%' . $request->q . '%');
            }

            $students = $query->get()->map(function ($student) {
                $student->logged_today = $student->behaviourLogs->isNotEmpty();
                $student->today_log    = $student->behaviourLogs->first();
                return $student;
            })->sortBy('logged_today');
        }

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        return view('teacher.students', compact(
            'teacher', 'class', 'students', 'unreadMessages'
        ));
    }

    public function show(Student $student)
    {
        $teacher = Auth::user();
        $student->load(['profile', 'parent', 'classRoom']);

        $recentLogs = $student->behaviourLogs()
            ->with('teacher')
            ->latest('logged_at')
            ->take(5)
            ->get();

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        return view('teacher.student-profile', compact(
            'student', 'recentLogs', 'unreadMessages', 'teacher'
        ));
    }
}
