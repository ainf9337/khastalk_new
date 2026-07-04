<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        // Route the user to their specific folder structure based on their role
        switch ($role) {
            case 'admin':
                return view('admin.dashboard');
            case 'teacher':
                $students = Auth::user()->students; 
                return view('teacher.dashboard', compact('students'));
            case 'senior-assistant':
                return view('senior-assistant.dashboard');
            case 'parent':
                return view('parent.dashboard');
            default:
                Auth::logout();
                return redirect('/login')->with('error', 'Unauthorized access.');
        }
    }
}
