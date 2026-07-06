<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\BehaviourLog;
use App\Models\Message;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private function buildReportData(int $classId, int $month, int $year): array
    {
        $students   = Student::where('class_id', $classId)->get();
        $reportData = $students->map(function ($student) use ($month, $year) {
            $logs = BehaviourLog::where('student_id', $student->id)
                ->whereMonth('logged_at', $month)
                ->whereYear('logged_at',  $year)
                ->get();

            return [
                'student'        => $student,
                'total_logs'     => $logs->count(),
                'resolved_count' => $logs->where('resolved', true)->count(),
                'avg_severity'   => round($logs->avg('severity') ?? 0, 1),
                'types'          => $logs->pluck('behaviour_type')
                                         ->unique()->join(', '),
                'daily'          => $logs->groupBy(fn($l) =>
                                         $l->logged_at->format('Y-m-d'))
                                         ->map->count(),
            ];
        })->sortByDesc('total_logs');

        return [
            'reportData' => $reportData,
            'totalLogs'  => $reportData->sum('total_logs'),
        ];
    }

    public function index(Request $request)
    {
        $teacher = Auth::user();
        $class   = $teacher->teacherClass;
        $month   = (int) $request->get('month', now()->month);
        $year    = (int) $request->get('year',  now()->year);

        $reportData    = collect();
        $totalLogs     = 0;
        $chartLabels   = [];
        $chartData     = [];
        $typeLabels    = [];
        $typeCounts    = [];

        if ($class) {
            $built      = $this->buildReportData($class->id, $month, $year);
            $reportData = $built['reportData'];
            $totalLogs  = $built['totalLogs'];

            // ── Bar chart: logs per student ───────────────────────
            $chartLabels = $reportData->map(fn($r) =>
                explode(' ', $r['student']->name)[0]
            )->values()->toArray();

            $chartData = $reportData->pluck('total_logs')->values()->toArray();

            // ── Doughnut chart: behaviour type breakdown ──────────
            $allLogs = BehaviourLog::whereHas('student', fn($q) =>
                    $q->where('class_id', $class->id))
                ->whereMonth('logged_at', $month)
                ->whereYear('logged_at',  $year)
                ->get();

            $typeCounts = $allLogs->groupBy('behaviour_type')
                ->map->count()
                ->sortDesc();

            $typeLabels = $typeCounts->keys()->toArray();
            $typeCounts = $typeCounts->values()->toArray();
        }

        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->where('is_read', false)->count();

        $months = ['','January','February','March','April','May','June',
                   'July','August','September','October','November','December'];

        return view('teacher.reports', compact(
            'reportData', 'totalLogs', 'month', 'year', 'months',
            'class', 'unreadMessages', 'teacher',
            'chartLabels', 'chartData', 'typeLabels', 'typeCounts'
        ));
    }

    public function export(Request $request)
    {
        $teacher = Auth::user();
        $class   = $teacher->teacherClass;
        $month   = (int) $request->get('month', now()->month);
        $year    = (int) $request->get('year',  now()->year);

        $months = ['','January','February','March','April','May','June',
                   'July','August','September','October','November','December'];

        $reportData = collect();
        $totalLogs  = 0;

        if ($class) {
            $built      = $this->buildReportData($class->id, $month, $year);
            $reportData = $built['reportData'];
            $totalLogs  = $built['totalLogs'];
        }

        $resolved     = $reportData->sum('resolved_count');
        $resolutionRate = $totalLogs > 0
            ? round(($resolved / $totalLogs) * 100)
            : 0;

        $pdf = Pdf::loadView('teacher.reports-pdf', compact(
            'reportData', 'totalLogs', 'month', 'year',
            'months', 'class', 'teacher', 'resolutionRate'
        ))->setPaper('a4', 'portrait');

        $filename = 'KHAS-Talk-Report-'
                  . $months[$month] . '-' . $year . '.pdf';

        return $pdf->download($filename);
    }
}
