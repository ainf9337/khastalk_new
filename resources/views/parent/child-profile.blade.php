@extends('layouts.khas')
@section('title', $student->name)

@section('tabs')
    <a href="{{ route('parent.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('parent.messages') }}" class="khas-tab">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('parent.behaviour-history', ['id' => $student->id]) }}" class="khas-tab">
        <i class="fa-solid fa-clock-rotate-left"></i> Behaviour History
    </a>
    <a href="{{ route('parent.rpi-progress', ['id' => $student->id]) }}" class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI Progress
    </a>
@endsection

@section('content')
<div class="khas-page">
    <a href="{{ route('parent.dashboard') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>

    <div style="margin-bottom:20px">
        <h5 style="font-weight:600;margin-bottom:4px">{{ $student->name }}</h5>
        <span class="badge-autism">{{ $student->diagnosis ?? 'Autism' }}</span>
        <span style="font-size:12px;color:var(--khas-muted);margin-left:10px">
            {{ $student->classRoom?->class_name ?? '' }}
            @if($student->classRoom?->teacher)
                &nbsp;&middot;&nbsp; Teacher: {{ $student->classRoom->teacher->name }}
            @endif
        </span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:10px">
                <i class="fa-solid fa-bolt" style="color:var(--khas-red)"></i>
                SENSORY TRIGGERS
            </p>
            @if($student->profile?->sensory_triggers)
                @foreach($student->profile->triggersArray() as $t)
                    <span style="display:inline-block;background:#FDEDEC;color:var(--khas-red);
                                 font-size:11px;padding:3px 10px;border-radius:12px;margin:3px 3px 0 0">
                        {{ $t }}
                    </span>
                @endforeach
            @else
                <p style="font-size:12.5px;color:var(--khas-muted)">Not specified</p>
            @endif
        </div>

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:10px">
                <i class="fa-solid fa-heart" style="color:var(--khas-green)"></i>
                CALMING STRATEGIES
            </p>
            @if($student->profile?->calming_strategies)
                @foreach($student->profile->strategiesArray() as $s)
                    <span style="display:inline-block;background:#EAFAF1;color:var(--khas-green);
                                 font-size:11px;padding:3px 10px;border-radius:12px;margin:3px 3px 0 0">
                        {{ $s }}
                    </span>
                @endforeach
            @else
                <p style="font-size:12.5px;color:var(--khas-muted)">Not specified</p>
            @endif
        </div>

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:8px">
                <i class="fa-solid fa-notes-medical" style="color:var(--khas-blue)"></i>
                MEDICAL INFO
            </p>
            <p style="font-size:13px;margin:0">
                {{ $student->profile?->medical_info ?? 'No known allergies' }}
            </p>
        </div>

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:8px">
                <i class="fa-solid fa-comments" style="color:var(--khas-blue)"></i>
                COMMUNICATION
            </p>
            <p style="font-size:13px;margin:0">
                {{ $student->profile?->communication_level ?? 'Not specified' }}
            </p>
        </div>

    </div>
</div>
@endsection
