@extends('layouts.khas')
@section('title', 'Activity Log')

@section('tabs')
    <a href="{{ route('admin.dashboard') }}" class="khas-tab">
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
    <a href="{{ route('admin.activity-log') }}" class="khas-tab active">
        <i class="fa-solid fa-clock-rotate-left"></i> Activity Log
    </a>
@endsection

@section('content')
<div class="khas-page">

    <div class="khas-page-header">
        <div>
            <h5 style="font-weight:600;margin-bottom:2px">
                <i class="fa-solid fa-clock-rotate-left"
                   style="color:var(--khas-blue)"></i>
                &nbsp;User Activity Log
            </h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                Showing {{ $logs->count() }} records
                {{ request()->hasAny(['user_id','action','date']) ? '— filtered' : '' }}
            </p>
        </div>
        @if(request()->hasAny(['user_id','action','date']))
        <a href="{{ route('admin.activity-log') }}"
           style="font-size:12.5px;color:var(--khas-red);text-decoration:none;
                  display:inline-flex;align-items:center;gap:5px">
            <i class="fa-solid fa-xmark"></i> Clear filters
        </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="khas-card" style="margin-bottom:20px">
        <form method="GET"
              style="display:grid;grid-template-columns:1fr 1fr 1fr auto;
                     gap:12px;align-items:end">
            <div>
                <label class="khas-label">Filter by user</label>
                <select name="user_id" class="khas-select" style="margin-bottom:0">
                    <option value="">All users</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}"
                            {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="khas-label">Filter by action</label>
                <input type="text" name="action" class="khas-input"
                       style="margin-bottom:0"
                       placeholder="e.g. Login, Created..."
                       value="{{ request('action') }}">
            </div>
            <div>
                <label class="khas-label">Filter by date</label>
                <input type="date" name="date" class="khas-input"
                       style="margin-bottom:0"
                       value="{{ request('date') }}">
            </div>
            <button type="submit" class="khas-btn khas-btn-primary"
                    style="width:auto;white-space:nowrap">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>
        </form>
    </div>

    {{-- Log table --}}
    @if($logs->isEmpty())
    <div class="khas-card" style="text-align:center;padding:48px">
        <i class="fa-solid fa-inbox"
           style="font-size:36px;color:#C0C7D0;margin-bottom:12px;display:block"></i>
        <p style="color:var(--khas-muted)">No activity records found.</p>
    </div>
    @else
    @php
        $roleColors = [
            'admin'            => ['bg'=>'#EDE9FE','color'=>'#5B4ECC'],
            'teacher'          => ['bg'=>'#EAFAF1','color'=>'#1A7A4A'],
            'parent'           => ['bg'=>'#FFF7EB','color'=>'#A16207'],
            'senior_assistant' => ['bg'=>'#EBF1FA','color'=>'#2558A0'],
        ];
    @endphp
    <div class="khas-card" style="padding:0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:var(--khas-bg)">
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">USER</th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">ACTION</th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">DESCRIPTION</th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">IP</th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">TIME</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                @php
                    $rc = $roleColors[$log->user->role] ?? ['bg'=>'#F5F7FA','color'=>'#6B7A8D'];
                @endphp
                <tr style="border-top:1px solid var(--khas-border)">
                    <td style="padding:11px 16px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:28px;height:28px;border-radius:50%;
                                        background:{{ $rc['bg'] }};color:{{ $rc['color'] }};
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:11px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <a href="{{ route('admin.users.show', $log->user) }}"
                                   style="font-size:12.5px;font-weight:500;
                                          color:var(--khas-text);text-decoration:none">
                                    {{ $log->user->name }}
                                </a>
                                <p style="font-size:10px;margin:0">
                                    <span style="background:{{ $rc['bg'] }};
                                                 color:{{ $rc['color'] }};font-size:9.5px;
                                                 font-weight:600;padding:1px 7px;
                                                 border-radius:8px">
                                        {{ $log->user->role }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:11px 16px;font-weight:500">
                        {{ $log->action }}
                    </td>
                    <td style="padding:11px 16px;color:var(--khas-muted);
                               font-size:12px;max-width:280px">
                        {{ $log->description ?? '—' }}
                    </td>
                    <td style="padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-family:monospace">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                    <td style="padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);white-space:nowrap">
                        {{ $log->created_at->format('d M Y') }}<br>
                        <span style="font-size:11px">
                            {{ $log->created_at->format('g:i a') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
