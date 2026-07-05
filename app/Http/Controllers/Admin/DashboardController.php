<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\BehaviourLog;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers    = User::count();
        $totalStudents = Student::count();
        $totalClasses  = ClassRoom::count();
        $totalLogs     = BehaviourLog::whereDate('logged_at', today())->count();

        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalStudents',
            'totalClasses', 'totalLogs', 'recentActivity'
        ));
    }
}
