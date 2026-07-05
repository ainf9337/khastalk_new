@extends('layouts.khas')
@section('title', 'Add User')

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
<div class="khas-page" style="max-width:560px;margin:0 auto">

    <a href="{{ route('admin.users.index') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Back to Users
    </a>
    <h5 style="font-weight:600;margin-bottom:20px">Add New User</h5>

    <div class="khas-card">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <label class="khas-label">Full name *</label>
            <input type="text" name="name" class="khas-input"
                   placeholder="Cikgu Ahmad"
                   value="{{ old('name') }}" required>

            <label class="khas-label">Email *</label>
            <input type="email" name="email" class="khas-input"
                   placeholder="teacher@school.com"
                   value="{{ old('email') }}" required>

            <label class="khas-label">Role *</label>
            <select name="role" class="khas-select" required>
                <option value="">— Select role —</option>
                <option value="teacher"
                        {{ old('role') === 'teacher' ? 'selected' : '' }}>
                    Teacher
                </option>
                <option value="parent"
                        {{ old('role') === 'parent' ? 'selected' : '' }}>
                    Parent
                </option>
                <option value="senior_assistant"
                        {{ old('role') === 'senior_assistant' ? 'selected' : '' }}>
                    Senior Assistant
                </option>
            </select>
            <p style="font-size:11px;color:var(--khas-muted);margin:-8px 0 14px;
                       display:flex;align-items:center;gap:5px">
                <i class="fa-solid fa-circle-info"></i>
                Admin accounts are managed by system only
            </p>

            <label class="khas-label">Phone number</label>
            <input type="text" name="phone" class="khas-input"
                   placeholder="01X-XXXXXXXX"
                   value="{{ old('phone') }}">

            <label class="khas-label">Temporary Password *</label>
            <div class="pw-wrapper">
                <input type="password" name="password" class="khas-input"
                       placeholder="Min. 6 characters" required>
                <button type="button" class="pw-toggle">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <p style="font-size:11px;color:var(--khas-muted);margin:-8px 0 16px;
                       display:flex;align-items:center;gap:5px">
                <i class="fa-solid fa-circle-info"></i>
                User can change this via My Profile after first login
            </p>

            <button type="submit" class="khas-btn khas-btn-primary">
                <i class="fa-solid fa-user-plus"></i> Add User
            </button>
        </form>
    </div>
</div>
@endsection
