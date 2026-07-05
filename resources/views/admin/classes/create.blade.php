@extends('layouts.khas')
@section('title', 'Add Class')

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
<div class="khas-page" style="max-width:480px;margin:0 auto">

    <a href="{{ route('admin.classes.index') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Back to Classes
    </a>
    <h5 style="font-weight:600;margin-bottom:20px">Add New Class</h5>

    <div class="khas-card">
        <form method="POST" action="{{ route('admin.classes.store') }}">
            @csrf

            <label class="khas-label">Class name *</label>
            <input type="text" name="class_name" class="khas-input"
                   placeholder="e.g. PPKI 6A"
                   value="{{ old('class_name') }}" required>

            <label class="khas-label">Assign teacher *</label>
            <select name="teacher_id" class="khas-select" required>
                <option value="">— Select teacher —</option>
                @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}"
                        {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                    {{ $teacher->name }}
                </option>
                @endforeach
            </select>

            <label class="khas-label">Academic year *</label>
            <input type="number" name="academic_year" class="khas-input"
                   value="{{ old('academic_year', date('Y')) }}"
                   min="2020" max="2030" required>

            <button type="submit" class="khas-btn khas-btn-primary">
                <i class="fa-solid fa-plus"></i> Create Class
            </button>
        </form>
    </div>
</div>
@endsection
