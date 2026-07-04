@extends('layouts.khas')
@section('title', 'Dashboard')

@section('tabs')
    <a href="{{ route('teacher.dashboard') }}"
       class="khas-tab {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('teacher.students') }}"
       class="khas-tab {{ request()->routeIs('teacher.students*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i> Students
    </a>
    <a href="{{ route('teacher.rpi') }}"
       class="khas-tab {{ request()->routeIs('teacher.rpi*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-lines"></i> RPI / IEP
    </a>
    <a href="{{ route('teacher.messages') }}"
       class="khas-tab {{ request()->routeIs('teacher.messages*') ? 'active' : '' }}">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('teacher.reports') }}"
       class="khas-tab {{ request()->routeIs('teacher.reports') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-bar"></i> Reports
    </a>
@endsection

@section('content')
<div class="khas-page">

    {{-- Greeting --}}
    <h5 style="font-weight:600;font-size:17px;margin-bottom:3px">
        {{ $greeting }}, {{ $teacher->name }}
        <span style="font-size:20px">👋</span>
    </h5>
    <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:18px">
        {{ now()->format('l, j F Y') }}
        @if($class)
            &nbsp;&middot;&nbsp; {{ $class->class_name }}
        @endif
        &nbsp;&middot;&nbsp; {{ $students->count() }} students enrolled
    </p>

    {{-- Stats --}}
    <div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px">
        <div class="stat-card">
            <div style="font-size:20px;color:var(--khas-blue);margin-bottom:6px">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-num">{{ $students->count() }}</div>
            <div class="stat-label">Total students</div>
        </div>
        <div class="stat-card">
            <div style="font-size:20px;color:var(--khas-green);margin-bottom:6px">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-green)">{{ $loggedToday }}</div>
            <div class="stat-label">Logged today</div>
        </div>
        <div class="stat-card">
            <div style="font-size:20px;color:var(--khas-amber);margin-bottom:6px">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <div class="stat-num"
                 style="color:{{ $unreadMessages > 0 ? 'var(--khas-amber)' : 'var(--khas-muted)' }}">
                {{ $unreadMessages }}
            </div>
            <div class="stat-label">Unread messages</div>
        </div>
        <div class="stat-card">
            <div style="font-size:20px;color:var(--khas-muted);margin-bottom:6px">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="stat-num"
                 style="color:{{ $activeAlerts > 0 ? 'var(--khas-red)' : 'var(--khas-muted)' }}">
                {{ $activeAlerts }}
            </div>
            <div class="stat-label">Active alerts</div>
        </div>
    </div>

    {{-- Quick actions --}}
    <p style="font-size:13.5px;font-weight:600;margin-bottom:10px">Quick actions</p>
    <div class="qa-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:22px">
        <a href="{{ route('teacher.behaviour-log.create') }}" class="qa-btn qa-primary">
            <i class="fa-solid fa-plus qa-icon"></i>
            <span class="qa-label">Log Behaviour</span>
        </a>
        <a href="{{ route('teacher.emergency') }}" class="qa-btn qa-danger">
            <i class="fa-solid fa-triangle-exclamation qa-icon"></i>
            <span class="qa-label">Emergency Alert</span>
        </a>
        <a href="{{ route('teacher.rpi') }}" class="qa-btn">
            <i class="fa-solid fa-file-lines qa-icon" style="color:var(--khas-muted)"></i>
            <span class="qa-label">Update RPI</span>
        </a>
        <a href="{{ route('teacher.reports') }}" class="qa-btn">
            <i class="fa-solid fa-chart-bar qa-icon" style="color:var(--khas-muted)"></i>
            <span class="qa-label">View Reports</span>
        </a>
    </div>

    {{-- Students --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <p style="font-size:13.5px;font-weight:600;margin:0">Today's students</p>
        <span style="font-size:11.5px;color:var(--khas-muted)">
            @if($pendingCount > 0)
                {{ $pendingCount }} pending &nbsp;&middot;&nbsp;
            @endif
            <a href="{{ route('teacher.students') }}"
               style="color:var(--khas-blue);text-decoration:none">View all &rarr;</a>
        </span>
    </div>

    @if($students->isEmpty())
        <div class="khas-card" style="text-align:center;padding:40px">
            <p style="color:var(--khas-muted)">No students found in your class.</p>
        </div>
    @else
        <div class="student-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
            @foreach($students as $student)
            <div class="sc {{ $student->logged_today ? 'logged' : 'pending' }}">
                <div class="sc-name">{{ $student->name }}</div>
                <span class="{{ strtolower($student->diagnosis) === 'adhd' ? 'badge-adhd' : 'badge-autism' }}">
                    {{ $student->diagnosis ?? 'Autism' }}
                </span>
                <div class="sc-status">
                    <span class="dot {{ $student->logged_today ? 'dot-green' : 'dot-amber' }}"></span>
                    @if($student->logged_today)
                        Logged &middot; {{ $student->today_log?->logged_at?->format('g:i a') }}
                    @else
                        Not logged yet
                    @endif
                </div>
                @if($student->logged_today)
                    <a href="{{ route('teacher.students.show', $student) }}" class="sc-btn">
                        <i class="fa-solid fa-eye"></i> View Profile
                    </a>
                @else
                    <a href="{{ route('teacher.behaviour-log.create', ['student_id' => $student->id]) }}"
                       class="sc-btn log-btn">
                        <i class="fa-solid fa-pen"></i> Log Now
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
