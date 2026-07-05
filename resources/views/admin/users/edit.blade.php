@extends('layouts.khas')
@section('title', 'Edit ' . $user->name)

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

    <a href="{{ route('admin.users.show', $user) }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Back to Profile
    </a>
    <h5 style="font-weight:600;margin-bottom:20px">
        Edit — {{ $user->name }}
    </h5>

    <div class="khas-card">
        <form method="POST"
              action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label class="khas-label">Full Name *</label>
                    <input type="text" name="name" class="khas-input"
                           value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label class="khas-label">Phone</label>
                    <input type="text" name="phone" class="khas-input"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="01X-XXXXXXXX">
                </div>
            </div>

            <label class="khas-label">Email *</label>
            <input type="email" name="email" class="khas-input"
                   value="{{ old('email', $user->email) }}" required>

            <label class="khas-label">Role</label>
            <select name="role" class="khas-select">
                @foreach(['teacher','parent','senior_assistant','admin'] as $r)
                <option value="{{ $r }}"
                        {{ old('role', $user->role) === $r ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $r)) }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="khas-btn khas-btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
