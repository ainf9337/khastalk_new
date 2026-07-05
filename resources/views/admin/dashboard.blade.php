@extends('layouts.khas')
@section('title', 'Admin Dashboard')

@section('tabs')
    <a href="{{ route('admin.dashboard') }}" class="khas-tab active">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('admin.users.index') }}" class="khas-tab">
        <i class="fa-solid fa-users"></i> Users
    </a>
    <a href="{{ route('admin.students.index') }}" class="khas-tab">
        <i class="fa-solid fa-graduation-cap"></i> Students
    </a>
    <a href="{{ route('admin.classes.index') }}" class="khas-tab">
        <i class="fa-solid fa-school"></i> Classes
    </a>
    <a href="{{ route('admin.activity-log') }}" class="khas-tab">
        <i class="fa-solid fa-clock-rotate-left"></i> Activity Log
    </a>
@endsection

@section('content')
<div class="khas-page">

    <h5 style="font-weight:600;margin-bottom:3px">Admin Dashboard</h5>
    <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:20px">
        System overview &nbsp;&middot;&nbsp; {{ now()->format('l, j F Y') }}
    </p>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);
                gap:10px;margin-bottom:24px">
        <div class="stat-card">
            <div style="font-size:22px;color:var(--khas-blue);margin-bottom:6px">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-num">{{ $totalUsers }}</div>
            <div class="stat-label">Total users</div>
        </div>
        <div class="stat-card">
            <div style="font-size:22px;color:var(--khas-green);margin-bottom:6px">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-green)">{{ $totalStudents }}</div>
            <div class="stat-label">Students enrolled</div>
        </div>
        <div class="stat-card">
            <div style="font-size:22px;color:var(--khas-amber);margin-bottom:6px">
                <i class="fa-solid fa-school"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-amber)">{{ $totalClasses }}</div>
            <div class="stat-label">Active classes</div>
        </div>
        <div class="stat-card">
            <div style="font-size:22px;color:var(--khas-muted);margin-bottom:6px">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="stat-num">{{ $totalLogs }}</div>
            <div class="stat-label">Logs today</div>
        </div>
    </div>

    {{-- Quick links --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);
                gap:10px;margin-bottom:24px">
        <a href="{{ route('admin.users.create') }}"
           class="khas-card" style="text-decoration:none;color:inherit;display:block">
            <p style="font-size:20px;margin-bottom:8px">
                <i class="fa-solid fa-user-plus" style="color:var(--khas-blue)"></i>
            </p>
            <p style="font-size:14px;font-weight:600;margin-bottom:4px">Add User</p>
            <p style="font-size:12px;color:var(--khas-muted);margin:0">
                Register teachers, parents, assistants
            </p>
        </a>
        <a href="{{ route('admin.students.create') }}"
           class="khas-card" style="text-decoration:none;color:inherit;display:block">
            <p style="font-size:20px;margin-bottom:8px">
                <i class="fa-solid fa-user-graduate" style="color:var(--khas-green)"></i>
            </p>
            <p style="font-size:14px;font-weight:600;margin-bottom:4px">Enrol Student</p>
            <p style="font-size:12px;color:var(--khas-muted);margin:0">
                Add students and assign to classes
            </p>
        </a>
        <a href="{{ route('admin.classes.create') }}"
           class="khas-card" style="text-decoration:none;color:inherit;display:block">
            <p style="font-size:20px;margin-bottom:8px">
                <i class="fa-solid fa-school" style="color:var(--khas-amber)"></i>
            </p>
            <p style="font-size:14px;font-weight:600;margin-bottom:4px">Create Class</p>
            <p style="font-size:12px;color:var(--khas-muted);margin:0">
                Create classes and assign teachers
            </p>
        </a>
    </div>

    {{-- Recent activity --}}
    @if($recentActivity->isNotEmpty())
    <div style="display:flex;justify-content:space-between;
                align-items:center;margin-bottom:12px">
        <h6 style="font-weight:600;margin:0">
            <i class="fa-solid fa-clock-rotate-left"
               style="color:var(--khas-blue)"></i>
            &nbsp;Recent Activity
        </h6>
        <a href="{{ route('admin.activity-log') }}"
           style="font-size:12.5px;color:var(--khas-blue);text-decoration:none">
            View all &rarr;
        </a>
    </div>
    <div class="khas-card" style="padding:0;overflow:hidden">
        @foreach($recentActivity as $log)
        <div style="padding:11px 16px;border-bottom:1px solid var(--khas-border);
                    display:flex;align-items:center;gap:12px">
            <div style="width:30px;height:30px;border-radius:50%;
                        background:var(--khas-blue-light);color:var(--khas-blue);
                        display:flex;align-items:center;justify-content:center;
                        font-size:12px;font-weight:700;flex-shrink:0">
                {{ strtoupper(substr($log->user->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0">
                <p style="font-size:12.5px;font-weight:500;margin:0 0 2px">
                    {{ $log->user->name }}
                    <span style="font-weight:400;color:var(--khas-muted)">
                        — {{ $log->action }}
                    </span>
                </p>
                <p style="font-size:11px;color:var(--khas-muted);margin:0;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $log->description }}
                </p>
            </div>
            <div style="font-size:11px;color:var(--khas-muted);
                        flex-shrink:0;text-align:right">
                {{ $log->created_at->format('d M') }}<br>
                {{ $log->created_at->format('g:i a') }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
