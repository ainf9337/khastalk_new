@extends('layouts.khas')
@section('title', 'Students')

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
    <div class="khas-page-header">
        <h5 style="font-weight:600;margin:0">
            Students
            <span style="font-size:13px;font-weight:400;color:var(--khas-muted)">
                ({{ $students->count() }})
            </span>
        </h5>
        <a href="{{ route('admin.students.create') }}"
           class="khas-btn khas-btn-primary" style="width:auto">
            <i class="fa-solid fa-user-plus"></i> Enrol Student
        </a>
    </div>

    <div class="khas-card" style="padding:0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:var(--khas-bg)">
                    <th style="text-align:left;padding:11px 14px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">STUDENT</th>
                    <th style="text-align:left;padding:11px 14px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">CLASS</th>
                    <th style="text-align:left;padding:11px 14px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">PARENT</th>
                    <th style="padding:11px 14px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr style="border-top:1px solid var(--khas-border)">
                    <td style="padding:11px 14px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:28px;height:28px;border-radius:50%;
                                        background:#EDE9FE;color:#5B4ECC;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:11px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <div>
                                <p style="font-weight:600;margin:0 0 2px">
                                    {{ $student->name }}
                                </p>
                                <span class="badge-autism">Autism</span>
                            </div>
                        </div>
                    </td>
                    <td style="padding:11px 14px;color:var(--khas-muted)">
                        {{ $student->classRoom?->class_name ?? '—' }}
                    </td>
                    <td style="padding:11px 14px;color:var(--khas-muted)">
                        {{ $student->parent?->name ?? '—' }}
                    </td>
                    <td style="padding:11px 14px;text-align:right">
                        <form method="POST"
                              action="{{ route('admin.students.destroy', $student) }}"
                              onsubmit="return confirm('Remove this student? All data will be deleted.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="background:none;border:none;
                                           color:var(--khas-red);font-size:12px;
                                           cursor:pointer;font-weight:500;
                                           padding:4px 8px;border-radius:6px;
                                           transition:background 0.15s"
                                    onmouseover="this.style.background='#FDEDEC'"
                                    onmouseout="this.style.background='none'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
