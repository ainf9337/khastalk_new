@extends('layouts.khas')
@section('title', $user->name)

@section('tabs')
    <a href="{{ route('admin.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('admin.users.index') }}" class="khas-tab active">
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

    <a href="{{ route('admin.users.index') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Back to Users
    </a>

    {{-- Profile header --}}
    <div class="khas-card"
         style="display:flex;align-items:center;gap:20px;
                flex-wrap:wrap;margin-bottom:16px">
        <div style="width:64px;height:64px;border-radius:50%;
                    background:var(--khas-blue);display:flex;align-items:center;
                    justify-content:center;font-size:26px;font-weight:700;
                    color:#fff;flex-shrink:0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h5 style="font-weight:700;font-size:18px;margin-bottom:4px">
                {{ $user->name }}
            </h5>
            <span style="background:var(--khas-blue-light);color:var(--khas-blue);
                         font-size:12px;font-weight:600;padding:3px 12px;border-radius:10px">
                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
            </span>
            <p style="font-size:12.5px;color:var(--khas-muted);margin-top:6px">
                <i class="fa-solid fa-envelope"></i>
                &nbsp;{{ $user->email }}
                &nbsp;&nbsp;
                <i class="fa-solid fa-phone"></i>
                &nbsp;{{ $user->phone ?? 'Not set' }}
                &nbsp;&nbsp;
                <i class="fa-solid fa-calendar"></i>
                &nbsp;Joined {{ $user->created_at->format('d M Y') }}
            </p>
        </div>
        <div style="margin-left:auto">
            <a href="{{ route('admin.users.edit', $user) }}"
               class="khas-btn khas-btn-primary" style="width:auto">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;
                gap:16px;align-items:start">

        {{-- Activity log --}}
        <div class="khas-card">
            <p style="font-size:13.5px;font-weight:600;margin-bottom:14px">
                <i class="fa-solid fa-clock-rotate-left"
                   style="color:var(--khas-muted)"></i>
                &nbsp;Activity History
            </p>
            @forelse($activityLogs as $log)
            <div style="border-bottom:1px solid var(--khas-border);
                        padding:10px 0;display:flex;gap:12px;align-items:flex-start">
                <div style="flex:1">
                    <p style="font-size:12.5px;font-weight:500;margin:0 0 2px">
                        {{ $log->action }}
                    </p>
                    <p style="font-size:11.5px;color:var(--khas-muted);margin:0">
                        {{ $log->description }}
                    </p>
                </div>
                <div style="font-size:11px;color:var(--khas-muted);
                            text-align:right;flex-shrink:0">
                    {{ $log->created_at->format('d M Y') }}<br>
                    {{ $log->created_at->format('g:i a') }}<br>
                    <span style="font-size:10px;color:#C0C7D0">
                        {{ $log->ip_address }}
                    </span>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:var(--khas-muted)">
                No activity recorded yet.
            </p>
            @endforelse
        </div>

        {{-- Right sidebar --}}
        <div>
            {{-- Reset password --}}
            <div class="khas-card" style="margin-bottom:14px">
                <p style="font-size:13px;font-weight:600;margin-bottom:14px">
                    <i class="fa-solid fa-key"
                       style="color:var(--khas-amber)"></i>
                    &nbsp;Reset Password
                </p>
                <form method="POST"
                      action="{{ route('admin.users.reset-password', $user) }}">
                    @csrf
                    <label class="khas-label">New Password *</label>
                    <div class="pw-wrapper">
                        <input type="password" name="new_password"
                               class="khas-input"
                               placeholder="Min. 6 characters" required>
                        <button type="button" class="pw-toggle">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit"
                            class="khas-btn khas-btn-primary"
                            style="background:var(--khas-amber)">
                        <i class="fa-solid fa-rotate"></i> Reset
                    </button>
                </form>
            </div>

            {{-- Danger zone --}}
            @if($user->id !== auth()->id())
            <div class="khas-card"
                 style="border:1.5px solid var(--khas-red)">
                <p style="font-size:12px;font-weight:600;
                           color:var(--khas-red);margin-bottom:10px">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Danger Zone
                </p>
                <form method="POST"
                      action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="khas-btn"
                            style="width:100%;background:#fff;color:var(--khas-red);
                                   border:1.5px solid var(--khas-red)">
                        <i class="fa-solid fa-trash"></i> Delete User
                    </button>
                </form>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
