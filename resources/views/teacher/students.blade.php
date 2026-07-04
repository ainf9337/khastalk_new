@extends('layouts.khas')
@section('title', 'Students')

@section('tabs')
    <a href="{{ route('teacher.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('teacher.students') }}" class="khas-tab active">
        <i class="fa-solid fa-users"></i> Students
    </a>
    <a href="{{ route('teacher.rpi') }}" class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI / IEP
    </a>
    <a href="{{ route('teacher.messages') }}" class="khas-tab">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('teacher.reports') }}" class="khas-tab">
        <i class="fa-solid fa-chart-bar"></i> Reports
    </a>
@endsection

@section('content')
<div class="khas-page">
    <div class="khas-page-header">
        <div>
            <h5 style="font-weight:600;margin-bottom:2px">Students</h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                {{ $class?->class_name ?? 'No class assigned' }}
                &nbsp;&middot;&nbsp; {{ $students->count() }} students
            </p>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" style="margin-bottom:18px">
        <div style="position:relative;max-width:320px">
            <input name="q" type="text" class="khas-input"
                   placeholder="Search student name..."
                   value="{{ request('q') }}"
                   style="margin-bottom:0;padding-right:40px">
            <button type="submit"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                           background:none;border:none;cursor:pointer;color:var(--khas-muted)">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>

    @if($students->isEmpty())
        <div class="khas-card" style="text-align:center;padding:40px">
            <p style="color:var(--khas-muted)">No students found.</p>
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
                <div style="display:flex;gap:6px">
                    <a href="{{ route('teacher.students.show', $student) }}"
                       class="sc-btn" style="flex:1">
                        <i class="fa-solid fa-eye"></i> Profile
                    </a>
                    @if(!$student->logged_today)
                    <a href="{{ route('teacher.behaviour-log.create', ['student_id' => $student->id]) }}"
                       class="sc-btn log-btn" style="flex:1">
                        <i class="fa-solid fa-pen"></i> Log
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
