@extends('layouts.khas')
@section('title', 'Classes')

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
    <a href="{{ route('admin.classes.index') }}" class="khas-tab active">
        <i class="fa-solid fa-school"></i> Classes
    </a>
    <a href="{{ route('admin.activity-log') }}" class="khas-tab">
        <i class="fa-solid fa-clock-rotate-left"></i> Activity Log
    </a>
@endsection

@section('content')
<div class="khas-page">
    <div class="khas-page-header">
        <h5 style="font-weight:600;margin:0">Classes</h5>
        <a href="{{ route('admin.classes.create') }}"
           class="khas-btn khas-btn-primary" style="width:auto">
            <i class="fa-solid fa-plus"></i> Add Class
        </a>
    </div>

    @forelse($classes as $class)
    <div class="khas-card"
         style="display:flex;justify-content:space-between;
                align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:10px">
        <div>
            <p style="font-size:14px;font-weight:600;margin:0 0 3px">
                {{ $class->class_name }}
            </p>
            <p style="font-size:12px;color:var(--khas-muted);margin:0">
                Teacher: {{ $class->teacher?->name ?? 'Unassigned' }}
                &nbsp;&middot;&nbsp; {{ $class->students->count() }} students
                &nbsp;&middot;&nbsp; {{ $class->academic_year }}
            </p>
        </div>
        <form method="POST"
              action="{{ route('admin.classes.destroy', $class) }}"
              onsubmit="return confirm('Delete this class?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="khas-btn"
                    style="background:none;border:1.5px solid var(--khas-border);
                           color:var(--khas-red);font-size:12px;width:auto;padding:6px 14px">
                <i class="fa-solid fa-trash"></i> Delete
            </button>
        </form>
    </div>
    @empty
    <div class="khas-card" style="text-align:center;padding:40px">
        <i class="fa-solid fa-school"
           style="font-size:36px;color:#C0C7D0;margin-bottom:12px;display:block"></i>
        <p style="color:var(--khas-muted)">No classes yet. Create one to get started.</p>
    </div>
    @endforelse
</div>
@endsection
