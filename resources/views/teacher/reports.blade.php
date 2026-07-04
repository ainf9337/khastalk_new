@extends('layouts.khas')
@section('title', 'Reports')

@section('tabs')
    <a href="{{ route('teacher.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('teacher.students') }}" class="khas-tab">
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
    <a href="{{ route('teacher.reports') }}" class="khas-tab active">
        <i class="fa-solid fa-chart-bar"></i> Reports
    </a>
@endsection

@section('content')
<div class="khas-page">

    <div class="khas-page-header">
        <div>
            <h5 style="font-weight:600;margin-bottom:2px">Behaviour Report</h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                {{ $months[$month] }} {{ $year }}
            </p>
        </div>
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <select name="month" class="khas-select" style="width:130px;margin-bottom:0">
                @foreach($months as $m => $name)
                    @if($m > 0)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                    @endif
                @endforeach
            </select>
            <select name="year" class="khas-select" style="width:90px;margin-bottom:0">
                @for($y = now()->year; $y >= 2024; $y--)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="khas-btn khas-btn-primary" style="width:auto">
                <i class="fa-solid fa-eye"></i> View
            </button>
        </form>
    </div>

    {{-- Summary stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px">
        <div class="stat-card">
            <div class="stat-num" style="color:var(--khas-blue)">{{ $totalLogs }}</div>
            <div class="stat-label">Total logs this month</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--khas-green)">
                @php
                    $resolved = $reportData->sum('resolved_count');
                    $rate     = $totalLogs > 0 ? round(($resolved / $totalLogs) * 100) : 0;
                @endphp
                {{ $rate }}%
            </div>
            <div class="stat-label">Resolution rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:var(--khas-amber)">
                {{ $reportData->where('total_logs', '>', 0)->count() }}
            </div>
            <div class="stat-label">Students with incidents</div>
        </div>
    </div>

    {{-- Per-student table --}}
    <div class="khas-card">
        <p style="font-size:13.5px;font-weight:600;margin-bottom:14px">
            Per-Student Summary
        </p>
        <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:var(--khas-bg)">
                    <th style="text-align:left;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">STUDENT</th>
                    <th style="text-align:center;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">LOGS</th>
                    <th style="text-align:center;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">AVG SEVERITY</th>
                    <th style="text-align:center;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">RESOLVED</th>
                    <th style="text-align:left;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">BEHAVIOUR TYPES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                <tr style="border-top:1px solid var(--khas-border)">
                    <td style="padding:11px 14px">
                        <p style="font-weight:600;margin:0 0 2px">
                            {{ $row['student']->name }}
                        </p>
                        <span class="badge-autism">Autism</span>
                    </td>
                    <td style="text-align:center;padding:11px 14px;font-weight:700;
                               color:{{ $row['total_logs'] > 0 ? 'var(--khas-red)' : 'var(--khas-muted)' }}">
                        {{ $row['total_logs'] }}
                    </td>
                    <td style="text-align:center;padding:11px 14px">
                        {{ $row['total_logs'] > 0 ? number_format($row['avg_severity'], 1) : '—' }}
                    </td>
                    <td style="text-align:center;padding:11px 14px">
                        @if($row['total_logs'] > 0)
                            <span style="color:var(--khas-green);font-weight:600">
                                {{ $row['resolved_count'] }}/{{ $row['total_logs'] }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding:11px 14px;font-size:12px;color:var(--khas-muted)">
                        {{ $row['types'] ?: '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

</div>
@endsection
