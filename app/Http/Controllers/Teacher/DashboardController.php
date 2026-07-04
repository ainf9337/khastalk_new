<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Message;
use App\Models\EmergencyAlert;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher  = Auth::user();
        $class    = $teacher->teacherClass;
        $students = collect();

        if ($class) {
            $students = Student::where('class_id', $class->id)
                ->with(['profile', 'behaviourLogs' => function ($q) {
                    $q->whereDate('logged_at', today());
                }])
                ->get()
                ->map(function ($student) use ($teacher) {
                    $student->logged_today = $student->behaviourLogs->isNotEmpty();
                    $student->today_log    = $student->behaviourLogs->first();
                    return $student;
                })
                ->sortBy('logged_today');
        }

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)
            ->count();

        $activeAlerts = EmergencyAlert::where('teacher_id', $teacher->id)
            ->where('status', 'pending')
            ->count();

        $loggedToday  = $students->where('logged_today', true)->count();
        $pendingCount = $students->where('logged_today', false)->count();

        $hour     = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

        return view('teacher.dashboard', compact(
            'teacher', 'class', 'students',
            'unreadMessages', 'activeAlerts',
            'loggedToday', 'pendingCount', 'greeting'
        ));
    }
}
