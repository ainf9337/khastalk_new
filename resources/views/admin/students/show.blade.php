@extends('layouts.khas')
@section('title', $student->name)

@section('tabs')
    <a href="{{ route('admin.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('admin.users.index') }}" class="khas-tab">
        <i class="fa-solid fa-users"></i> Users
    </a>
    <a href="{{ route('admin.students.index') }}" class="khas-tab active">
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

    <a href="{{ route('admin.students.index') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Back to Students
    </a>

    {{-- Student header --}}
    <div class="khas-card"
         style="display:flex;align-items:center;gap:20px;
                flex-wrap:wrap;margin-bottom:16px">
        <div style="width:64px;height:64px;border-radius:50%;
                    background:#EDE9FE;display:flex;align-items:center;
                    justify-content:center;font-size:26px;font-weight:700;
                    color:#5B4ECC;flex-shrink:0">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div>
            <h5 style="font-weight:700;font-size:18px;margin-bottom:4px">
                {{ $student->name }}
            </h5>
            <span class="badge-autism">Autism</span>
            <p style="font-size:12.5px;color:var(--khas-muted);margin-top:6px">
                <i class="fa-solid fa-school" style="font-size:11px"></i>
                &nbsp;{{ $student->classRoom?->class_name ?? 'No class' }}
                &nbsp;&nbsp;
                <i class="fa-solid fa-chalkboard" style="font-size:11px"></i>
                &nbsp;{{ $student->classRoom?->teacher?->name ?? 'No teacher' }}
                &nbsp;&nbsp;
                @if($student->parent)
                <i class="fa-solid fa-user" style="font-size:11px"></i>
                &nbsp;{{ $student->parent->name }}
                @endif
                @if($student->date_of_birth)
                &nbsp;&nbsp;
                <i class="fa-solid fa-calendar" style="font-size:11px"></i>
                &nbsp;DOB: {{ $student->date_of_birth->format('d M Y') }}
                @endif
            </p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;
                gap:16px;align-items:start">

        {{-- Left: profile info + logs --}}
        <div>

            {{-- Profile info --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;
                        gap:12px;margin-bottom:14px">

                <div class="khas-card">
                    <p style="font-size:12px;font-weight:600;
                               color:var(--khas-muted);margin-bottom:10px">
                        <i class="fa-solid fa-bolt"
                           style="color:var(--khas-red)"></i>
                        SENSORY TRIGGERS
                    </p>
                    @if($student->profile?->sensory_triggers)
                        @foreach(explode(',', $student->profile->sensory_triggers) as $t)
                            <span style="display:inline-block;background:#FDEDEC;
                                         color:var(--khas-red);font-size:11px;
                                         padding:3px 10px;border-radius:12px;
                                         margin:3px 3px 0 0">
                                {{ trim($t) }}
                            </span>
                        @endforeach
                    @else
                        <p style="font-size:12.5px;color:var(--khas-muted)">
                            Not specified
                        </p>
                    @endif
                </div>

                <div class="khas-card">
                    <p style="font-size:12px;font-weight:600;
                               color:var(--khas-muted);margin-bottom:10px">
                        <i class="fa-solid fa-heart"
                           style="color:var(--khas-green)"></i>
                        CALMING STRATEGIES
                    </p>
                    @if($student->profile?->calming_strategies)
                        @foreach(explode(',', $student->profile->calming_strategies) as $s)
                            <span style="display:inline-block;background:#EAFAF1;
                                         color:var(--khas-green);font-size:11px;
                                         padding:3px 10px;border-radius:12px;
                                         margin:3px 3px 0 0">
                                {{ trim($s) }}
                            </span>
                        @endforeach
                    @else
                        <p style="font-size:12.5px;color:var(--khas-muted)">
                            Not specified
                        </p>
                    @endif
                </div>

                <div class="khas-card">
                    <p style="font-size:12px;font-weight:600;
                               color:var(--khas-muted);margin-bottom:8px">
                        <i class="fa-solid fa-notes-medical"
                           style="color:var(--khas-blue)"></i>
                        MEDICAL INFO
                    </p>
                    <p style="font-size:13px;margin:0">
                        {{ $student->profile?->medical_info
                            ?? 'No known medical issues' }}
                    </p>
                </div>

                <div class="khas-card">
                    <p style="font-size:12px;font-weight:600;
                               color:var(--khas-muted);margin-bottom:8px">
                        <i class="fa-solid fa-comments"
                           style="color:var(--khas-blue)"></i>
                        COMMUNICATION
                    </p>
                    <p style="font-size:13px;margin:0">
                        {{ $student->profile?->communication_level
                            ?? 'Not specified' }}
                    </p>
                </div>

            </div>

            {{-- Recent behaviour logs --}}
            <div class="khas-card">
                <p style="font-size:13.5px;font-weight:600;margin-bottom:14px">
                    <i class="fa-solid fa-clock-rotate-left"
                       style="color:var(--khas-muted)"></i>
                    &nbsp;Recent Behaviour Logs
                    <span style="font-size:12px;font-weight:400;
                                 color:var(--khas-muted)">
                        ({{ $recentLogs->count() }} shown)
                    </span>
                </p>

                @forelse($recentLogs as $log)
                <div style="border-bottom:1px solid var(--khas-border);
                            padding:10px 0;display:flex;
                            justify-content:space-between;align-items:flex-start">
                    <div>
                        <span style="font-size:12.5px;font-weight:600">
                            {{ $log->behaviour_type }}
                        </span>
                        <span style="font-size:11.5px;color:var(--khas-muted);
                                     margin-left:8px">
                            Severity {{ $log->severity }}
                            @if($log->duration)
                                &middot; {{ $log->duration }}
                            @endif
                        </span>
                        @if($log->triggers)
                        <p style="font-size:12px;color:var(--khas-muted);margin:4px 0 0">
                            Triggers: {{ $log->triggers }}
                        </p>
                        @endif
                        <p style="font-size:11px;color:var(--khas-muted);margin:3px 0 0">
                            By {{ $log->teacher->name }}
                        </p>
                    </div>
                    <div style="text-align:right;flex-shrink:0;padding-left:14px">
                        <div style="font-size:11px;color:var(--khas-muted)">
                            {{ $log->logged_at->format('d M Y') }}
                        </div>
                        <div style="font-size:11px;
                                    color:{{ $log->resolved
                                           ? 'var(--khas-green)'
                                           : 'var(--khas-amber)' }}">
                            {{ $log->resolved ? '✓ Resolved' : '⏳ Unresolved' }}
                        </div>
                    </div>
                </div>
                @empty
                <p style="font-size:13px;color:var(--khas-muted)">
                    No behaviour logs recorded yet.
                </p>
                @endforelse
            </div>

        </div>

        {{-- Right: change class + danger zone --}}
        <div>

            {{-- Change class --}}
            <div class="khas-card" style="margin-bottom:14px">
                <p style="font-size:13px;font-weight:600;margin-bottom:14px">
                    <i class="fa-solid fa-arrow-right-arrow-left"
                       style="color:var(--khas-blue)"></i>
                    &nbsp;Change Class
                </p>
                <p style="font-size:12px;color:var(--khas-muted);margin-bottom:14px">
                    Currently in
                    <strong>{{ $student->classRoom?->class_name ?? 'No class' }}</strong>
                </p>
                <form method="POST"
                      action="{{ route('admin.students.change-class', $student) }}">
                    @csrf
                    @method('PATCH')
                    <label class="khas-label">Move to class</label>
                    <select name="class_id" class="khas-select" required>
                        <option value="">— Select class —</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}"
                            {{ $student->class_id === $class->id
                               ? 'selected' : '' }}>
                            {{ $class->class_name }}
                            ({{ $class->teacher?->name ?? 'No teacher' }})
                        </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="khas-btn khas-btn-primary"
                            style="width:100%">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                        Change Class
                    </button>
                </form>
            </div>

            {{-- Delete student --}}
            <div class="khas-card"
                 style="border:1.5px solid var(--khas-red)">
                <p style="font-size:12px;font-weight:600;
                           color:var(--khas-red);margin-bottom:10px">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Danger Zone
                </p>
                <p style="font-size:12px;color:var(--khas-muted);margin-bottom:12px">
                    Deleting this student will permanently remove all their
                    behaviour logs, messages, and RPI documents.
                </p>
                <form method="POST"
                      action="{{ route('admin.students.destroy', $student) }}"
                      onsubmit="return confirm('Permanently delete {{ addslashes($student->name) }}? All data will be lost.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="khas-btn"
                            style="width:100%;background:#fff;
                                   color:var(--khas-red);
                                   border:1.5px solid var(--khas-red)">
                        <i class="fa-solid fa-trash"></i>
                        Delete Student
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
