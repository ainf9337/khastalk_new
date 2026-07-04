<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\BehaviourLog;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $class   = $teacher->teacherClass;
        $month   = (int) $request->get('month', now()->month);
        $year    = (int) $request->get('year',  now()->year);

        $reportData = collect();
        $totalLogs  = 0;

        if ($class) {
            $students = Student::where('class_id', $class->id)->get();

            $reportData = $students->map(function ($student) use ($month, $year) {
                $logs = BehaviourLog::where('student_id', $student->id)
                    ->whereMonth('logged_at', $month)
                    ->whereYear('logged_at', $year)
                    ->get();

                return [
                    'student'        => $student,
                    'total_logs'     => $logs->count(),
                    'resolved_count' => $logs->where('resolved', true)->count(),
                    'avg_severity'   => $logs->avg('severity'),
                    'types'          => $logs->pluck('behaviour_type')->unique()->join(', '),
                ];
            })->sortByDesc('total_logs');

            $totalLogs = $reportData->sum('total_logs');
        }

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        $months = ['','January','February','March','April','May',
                   'June','July','August','September','October','November','December'];

        return view('teacher.reports', compact(
            'reportData', 'totalLogs', 'month',
            'year', 'months', 'class', 'unreadMessages', 'teacher'
        ));
    }
}
