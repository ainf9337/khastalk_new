@extends('layouts.khas')
@section('title', 'Behaviour History')

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
    <a href="{{ route('parent.behaviour-history') }}" class="khas-tab active">
        <i class="fa-solid fa-clock-rotate-left"></i> Behaviour History
    </a>
    <a href="{{ route('parent.rpi-progress') }}" class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI Progress
    </a>
@endsection

@section('content')
<div class="khas-page">
    <div class="khas-page-header">
        <h5 style="font-weight:600;margin:0">Behaviour History</h5>
        @if($children->count() > 1)
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <select name="id" class="khas-select"
                    style="margin-bottom:0;width:auto"
                    onchange="this.form.submit()">
                @foreach($children as $child)
                <option value="{{ $child->id }}"
                        {{ $child->id == $selectedChild?->id ? 'selected' : '' }}>
                    {{ $child->name }}
                </option>
                @endforeach
            </select>
        </form>
        @endif
    </div>

    @if($logs->isEmpty())
        <div class="khas-card" style="text-align:center;padding:40px">
            <i class="fa-solid fa-inbox"
               style="font-size:36px;color:#C0C7D0;margin-bottom:12px;display:block"></i>
            <p style="color:var(--khas-muted)">No behaviour logs found.</p>
        </div>
    @else
        @php $prevDate = ''; @endphp
        @foreach($logs as $log)
            @php
                $logDate = $log->logged_at->format('d F Y');
            @endphp
            @if($logDate !== $prevDate)
                @php $prevDate = $logDate; @endphp
                <p style="font-size:11.5px;font-weight:600;color:var(--khas-muted);
                           margin:16px 0 8px;letter-spacing:0.5px;text-transform:uppercase">
                    {{ strtoupper($logDate) }}
                </p>
            @endif
            <div class="khas-card" style="margin-bottom:10px">
                <div style="display:flex;justify-content:space-between;
                            align-items:flex-start;flex-wrap:wrap;gap:10px">
                    <div>
                        <span style="font-size:13.5px;font-weight:600">
                            {{ $log->behaviour_type }}
                        </span>
                        <span style="font-size:12px;color:var(--khas-muted);margin-left:8px">
                            Severity {{ $log->severity }}
                            @if($log->duration) &nbsp;&middot;&nbsp; {{ $log->duration }} @endif
                        </span>
                        @if($log->triggers)
                        <p style="font-size:12px;color:var(--khas-muted);margin:5px 0 0">
                            Triggers: {{ $log->triggers }}
                        </p>
                        @endif
                        @if($log->notes)
                        <p style="font-size:12.5px;color:var(--khas-text);margin:6px 0 0">
                            {{ $log->notes }}
                        </p>
                        @endif
                        <p style="font-size:11.5px;color:var(--khas-muted);margin:6px 0 0">
                            <i class="fa-solid fa-user" style="font-size:10px"></i>
                            {{ $log->teacher->name }}
                            &nbsp;&middot;&nbsp; {{ $log->logged_at->format('g:i a') }}
                        </p>
                    </div>
                    <span style="font-size:12px;font-weight:600;padding:4px 10px;
                                 border-radius:10px;flex-shrink:0;
                                 background:{{ $log->resolved ? '#EAFAF1' : '#FFF7EB' }};
                                 color:{{ $log->resolved ? 'var(--khas-green)' : 'var(--khas-amber)' }}">
                        {{ $log->resolved ? '✓ Resolved' : '⏳ Unresolved' }}
                    </span>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
