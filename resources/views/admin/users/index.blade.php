@extends('layouts.khas')
@section('title', 'Users')

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
    <div class="khas-page-header">
        <h5 style="font-weight:600;margin:0">
            All Users
            <span style="font-size:13px;font-weight:400;color:var(--khas-muted)">
                ({{ $users->count() }})
            </span>
        </h5>
        <a href="{{ route('admin.users.create') }}"
           class="khas-btn khas-btn-primary" style="width:auto">
            <i class="fa-solid fa-user-plus"></i> Add User
        </a>
    </div>

    @php
        $roleColors = [
            'admin'            => ['bg'=>'#EDE9FE','color'=>'#5B4ECC'],
            'teacher'          => ['bg'=>'#EAFAF1','color'=>'#1A7A4A'],
            'parent'           => ['bg'=>'#FFF7EB','color'=>'#A16207'],
            'senior_assistant' => ['bg'=>'#EBF1FA','color'=>'#2558A0'],
        ];
        $roleLabels = [
            'admin'            => 'Admin',
            'teacher'          => 'Teacher',
            'parent'           => 'Parent',
            'senior_assistant' => 'Senior Asst.',
        ];
    @endphp

    <div class="khas-card" style="padding:0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:var(--khas-bg)">
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">NAME</th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">EMAIL</th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">ROLE</th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">PHONE</th>
                    <th style="padding:11px 16px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                @php $rc = $roleColors[$u->role] ?? ['bg'=>'#F5F7FA','color'=>'#6B7A8D']; @endphp
                <tr style="border-top:1px solid var(--khas-border);cursor:pointer"
                    onclick="window.location='{{ route('admin.users.show', $u) }}'">
                    <td style="padding:11px 16px">
                        <div style="display:flex;align-items:center;gap:9px">
                            <div style="width:30px;height:30px;border-radius:50%;
                                        background:var(--khas-blue-light);color:var(--khas-blue);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <span style="font-weight:500">{{ $u->name }}</span>
                            @if($u->id === auth()->id())
                            <span style="font-size:10px;color:var(--khas-muted)">(you)</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding:11px 16px;color:var(--khas-muted);font-size:12.5px">
                        {{ $u->email }}
                    </td>
                    <td style="padding:11px 16px">
                        <span style="background:{{ $rc['bg'] }};color:{{ $rc['color'] }};
                                     font-size:11px;font-weight:600;padding:3px 10px;
                                     border-radius:10px;white-space:nowrap">
                            {{ $roleLabels[$u->role] ?? $u->role }}
                        </span>
                    </td>
                    <td style="padding:11px 16px;font-size:12.5px;color:var(--khas-muted)">
                        {{ $u->phone ?? '—' }}
                    </td>
                    <td style="padding:11px 16px;text-align:right"
                        onclick="event.stopPropagation()">
                        @if($u->id !== auth()->id())
                        <form method="POST"
                              action="{{ route('admin.users.destroy', $u) }}"
                              onsubmit="return confirm('Delete {{ addslashes($u->name) }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="background:none;border:none;color:var(--khas-red);
                                           font-size:12px;cursor:pointer;font-weight:500;
                                           padding:4px 8px;border-radius:6px;
                                           transition:background 0.15s"
                                    onmouseover="this.style.background='#FDEDEC'"
                                    onmouseout="this.style.background='none'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
