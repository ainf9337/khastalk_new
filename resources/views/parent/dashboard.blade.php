@extends('layouts.khas')
@section('title', 'Dashboard')

@section('tabs')
    <a href="{{ route('parent.dashboard') }}" class="khas-tab active">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('parent.messages') }}" class="khas-tab">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('parent.behaviour-history') }}" class="khas-tab">
        <i class="fa-solid fa-clock-rotate-left"></i> Behaviour History
    </a>
    <a href="{{ route('parent.rpi-progress') }}" class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI Progress
    </a>
@endsection

@section('content')
<div class="khas-page">

    <h5 style="font-weight:600;font-size:17px;margin-bottom:3px">
        {{ $greeting }}, {{ $parent->name }} 👋
    </h5>
    <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:18px">
        {{ now()->format('l, j F Y') }}
    </p>

    {{-- Emergency alerts --}}
    @foreach($pendingAlerts as $alert)
    <div style="background:#FDEDEC;border:2px solid var(--khas-red);border-radius:12px;
                padding:16px 18px;margin-bottom:14px">
        <p style="font-weight:700;color:var(--khas-red);font-size:14px;margin-bottom:4px">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Emergency Alert — {{ $alert->alert_type }}
        </p>
        <p style="font-size:13px;margin-bottom:4px">
            Student: <strong>{{ $alert->student->name }}</strong>
        </p>
        <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:12px">
            {{ $alert->description }}
            &nbsp;&middot;&nbsp; {{ $alert->created_at->format('g:i a') }}
        </p>
        <form method="POST"
              action="{{ route('parent.emergency.confirm', $alert) }}">
            @csrf
            <button type="submit" class="khas-btn khas-btn-danger" style="width:auto">
                <i class="fa-solid fa-circle-check"></i>
                I confirm receipt of this alert
            </button>
        </form>
    </div>
    @endforeach

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);
                gap:10px;margin-bottom:20px">
        <div class="stat-card">
            <div class="stat-num" style="color:var(--khas-blue)">
                {{ $children->count() }}
            </div>
            <div class="stat-label">
                My {{ $children->count() === 1 ? 'child' : 'children' }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--khas-green)">
                {{ collect($todayLogs)->filter(fn($l) => $l->isNotEmpty())->count() }}
            </div>
            <div class="stat-label">Logs today</div>
        </div>
        <div class="stat-card">
            <div class="stat-num"
                 style="color:{{ $unreadMessages > 0 ? 'var(--khas-amber)' : 'var(--khas-muted)' }}">
                {{ $unreadMessages }}
            </div>
            <div class="stat-label">Unread messages</div>
        </div>
    </div>

    {{-- Children cards --}}
    @foreach($children as $child)
    @php $logs = $todayLogs[$child->id] ?? collect(); @endphp
    <div class="khas-card" style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;
                    align-items:flex-start;margin-bottom:14px;flex-wrap:wrap;gap:10px">
            <div>
                <h6 style="font-weight:600;font-size:15px;margin-bottom:4px">
                    {{ $child->name }}
                </h6>
                <span class="badge-autism">{{ $child->diagnosis ?? 'Autism' }}</span>
                <span style="font-size:12px;color:var(--khas-muted);margin-left:8px">
                    {{ $child->classRoom?->class_name ?? '' }}
                    @if($child->classRoom?->teacher)
                        &nbsp;&middot;&nbsp; Teacher: {{ $child->classRoom->teacher->name }}
                    @endif
                </span>
            </div>
            <a href="{{ route('parent.child.show', $child) }}"
               style="font-size:12.5px;color:var(--khas-blue);text-decoration:none">
                View profile &rarr;
            </a>
        </div>

        @if($logs->isNotEmpty())
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:8px">
                TODAY'S LOG
            </p>
            @foreach($logs as $log)
            <div style="background:var(--khas-bg);border-radius:8px;padding:12px;margin-bottom:6px">
                <div style="display:flex;justify-content:space-between">
                    <span style="font-size:13px;font-weight:600">
                        {{ $log->behaviour_type }}
                    </span>
                    <span style="font-size:11px;font-weight:600;
                                 color:{{ $log->resolved ? 'var(--khas-green)' : 'var(--khas-amber)' }}">
                        {{ $log->resolved ? '✓ Resolved' : '⏳ Unresolved' }}
                    </span>
                </div>
                <p style="font-size:12px;color:var(--khas-muted);margin:4px 0 0">
                    Severity {{ $log->severity }}
                    @if($log->duration) &nbsp;&middot;&nbsp; {{ $log->duration }} @endif
                    &nbsp;&middot;&nbsp; {{ $log->logged_at->format('g:i a') }}
                </p>
                @if($log->notes)
                <p style="font-size:12px;color:var(--khas-text);margin:6px 0 0">
                    {{ $log->notes }}
                </p>
                @endif
            </div>
            @endforeach
        @else
            <div style="background:var(--khas-bg);border-radius:8px;
                        padding:14px;text-align:center">
                <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                    <i class="fa-solid fa-clock" style="margin-right:6px"></i>
                    No behaviour log recorded today yet.
                </p>
            </div>
        @endif
    </div>
    @endforeach

</div>
@endsection
