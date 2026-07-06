@extends('layouts.khas')
@section('title', 'Enrol Student')

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
<div class="khas-page" style="max-width:560px;margin:0 auto">

    <a href="{{ route('admin.students.index') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Back to Students
    </a>
    <h5 style="font-weight:600;margin-bottom:20px">Enrol New Student</h5>

    <div class="khas-card">
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf

            <label class="khas-label">Full name *</label>
            <input type="text" name="name" class="khas-input"
                   placeholder="Ahmad Zikri Bin Hafiz"
                   value="{{ old('name') }}" required>

            <label class="khas-label">MyKid Number</label>
            <input type="text" name="mykid_number" class="khas-input"
                   placeholder="e.g. 130315-XX-XXXX"
                   value="{{ old('mykid_number') }}">

            <label class="khas-label">Class *</label>
            <select name="class_id" class="khas-select" required>
                <option value="">— Select class —</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}"
                        {{ old('class_id') == $class->id ? 'selected' : '' }}>
                    {{ $class->class_name }}
                    ({{ $class->teacher?->name ?? 'No teacher' }})
                </option>
                @endforeach
            </select>

            <label class="khas-label">Date of birth</label>
            <input type="date" name="date_of_birth" class="khas-input"
                   value="{{ old('date_of_birth') }}">

            <label class="khas-label">Link parent account</label>
            <select name="parent_id" class="khas-select">
                <option value="">— Optional —</option>
                @foreach($parents as $parent)
                <option value="{{ $parent->id }}"
                        {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
                @endforeach
            </select>

            <label class="khas-label">Relationship to student</label>
            <select name="relationship" class="khas-select">
                @foreach($relationshipOptions as $rel)
                <option value="{{ $rel }}"
                        {{ old('relationship') === $rel ? 'selected' : '' }}>
                    {{ $rel }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="khas-btn khas-btn-primary">
                <i class="fa-solid fa-user-plus"></i> Enrol Student
            </button>
        </form>
    </div>
</div>
@endsection
