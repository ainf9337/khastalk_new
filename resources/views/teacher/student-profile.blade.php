@extends('layouts.khas')
@section('title', $student->name)

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
            <a href="{{ route('teacher.students') }}"
               style="font-size:12px;color:var(--khas-muted);text-decoration:none;
                      display:inline-flex;align-items:center;gap:6px;margin-bottom:8px">
                <i class="fa-solid fa-arrow-left"></i> Back to Students
            </a>
            <h5 style="font-weight:600;margin-bottom:4px">{{ $student->name }}</h5>
            <span class="{{ strtolower($student->diagnosis) === 'adhd' ? 'badge-adhd' : 'badge-autism' }}">
                {{ $student->diagnosis ?? 'Autism' }}
            </span>
            @if($student->date_of_birth)
            <span style="font-size:12px;color:var(--khas-muted);margin-left:10px">
                DOB: {{ $student->date_of_birth->format('d M Y') }}
            </span>
            @endif
        </div>
        <a href="{{ route('teacher.behaviour-log.create', ['student_id' => $student->id]) }}"
           class="khas-btn khas-btn-primary" style="width:auto">
            <i class="fa-solid fa-plus"></i> Log Behaviour
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">

        {{-- Sensory Triggers --}}
        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:10px">
                <i class="fa-solid fa-bolt" style="color:var(--khas-red)"></i>
                SENSORY TRIGGERS
            </p>
            @if($student->profile?->sensory_triggers)
                @foreach($student->profile->triggersArray() as $trigger)
                    <span style="display:inline-block;background:#FDEDEC;color:var(--khas-red);
                                 font-size:11px;padding:3px 10px;border-radius:12px;margin:3px 3px 0 0">
                        {{ $trigger }}
                    </span>
                @endforeach
            @else
                <p style="font-size:12.5px;color:var(--khas-muted)">Not specified</p>
            @endif
        </div>

        {{-- Calming Strategies --}}
        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:10px">
                <i class="fa-solid fa-heart" style="color:var(--khas-green)"></i>
                CALMING STRATEGIES
            </p>
            @if($student->profile?->calming_strategies)
                @foreach($student->profile->strategiesArray() as $strategy)
                    <span style="display:inline-block;background:#EAFAF1;color:var(--khas-green);
                                 font-size:11px;padding:3px 10px;border-radius:12px;margin:3px 3px 0 0">
                        {{ $strategy }}
                    </span>
                @endforeach
            @else
                <p style="font-size:12.5px;color:var(--khas-muted)">Not specified</p>
            @endif
        </div>

        {{-- Medical Info --}}
        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:8px">
                <i class="fa-solid fa-notes-medical" style="color:var(--khas-blue)"></i>
                MEDICAL INFO
            </p>
            <p style="font-size:13px;margin:0">
                {{ $student->profile?->medical_info ?? 'No known allergies or conditions' }}
            </p>
        </div>

        {{-- Communication + Parent --}}
        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:8px">
                <i class="fa-solid fa-comments" style="color:var(--khas-blue)"></i>
                COMMUNICATION LEVEL
            </p>
            <p style="font-size:13px;margin:0 0 10px">
                {{ $student->profile?->communication_level ?? 'Not specified' }}
            </p>
            @if($student->parent)
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:6px">
                <i class="fa-solid fa-user"></i> PARENT / GUARDIAN
            </p>
            <p style="font-size:12.5px;margin:0">
                {{ $student->parent->name }}
                &nbsp;&middot;&nbsp; {{ $student->parent->phone ?? 'No phone' }}
            </p>
            @endif
        </div>

    </div>

    {{-- Recent Logs --}}
    <div class="khas-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <p style="font-size:13.5px;font-weight:600;margin:0">Recent Behaviour Logs</p>
            <a href="{{ route('teacher.behaviour-log.create', ['student_id' => $student->id]) }}"
               style="font-size:12px;color:var(--khas-blue);text-decoration:none">
                <i class="fa-solid fa-plus"></i> New log
            </a>
        </div>
        @if($recentLogs->isEmpty())
            <p style="font-size:13px;color:var(--khas-muted);margin:0">No logs yet.</p>
        @else
            @foreach($recentLogs as $log)
            <div style="border-bottom:1px solid var(--khas-border);
                        padding:10px 0;display:flex;gap:14px;align-items:flex-start">
                <div style="flex:1">
                    <span style="font-size:12.5px;font-weight:600">{{ $log->behaviour_type }}</span>
                    &nbsp;
                    <span style="font-size:11px;color:var(--khas-muted)">
                        Severity {{ $log->severity }}
                        @if($log->duration) &middot; {{ $log->duration }} @endif
                    </span>
                    @if($log->triggers)
                    <p style="font-size:12px;color:var(--khas-muted);margin:4px 0 0">
                        Triggers: {{ $log->triggers }}
                    </p>
                    @endif
                </div>
                <div style="text-align:right;flex-shrink:0">
                    <div style="font-size:11px;color:var(--khas-muted)">
                        {{ $log->logged_at->format('d M Y') }}
                    </div>
                    <div style="font-size:11px;
                                color:{{ $log->resolved ? 'var(--khas-green)' : 'var(--khas-amber)' }}">
                        {{ $log->resolved ? '✓ Resolved' : '⏳ Unresolved' }}
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

</div>
@endsection
