@extends('layouts.khas')
@section('title', 'Create RPI')

@section('content')
<div class="khas-page" style="max-width:500px;margin:0 auto">
    <a href="{{ route('teacher.rpi') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> All RPIs
    </a>
    <h5 style="font-weight:600;margin-bottom:20px">Create New RPI Document</h5>

    <div class="khas-card">
        <form method="POST" action="{{ route('teacher.rpi.store') }}">
            @csrf
            <label class="khas-label">Student *</label>
            <select name="student_id" class="khas-select" required>
                <option value="">— Select student —</option>
                @foreach($students as $student)
                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                    {{ $student->name }}
                </option>
                @endforeach
            </select>

            <label class="khas-label">Period *</label>
            <input type="text" name="period" class="khas-input"
                   placeholder="e.g. January – June 2026"
                   value="{{ old('period') }}" required>

            <button type="submit" class="khas-btn khas-btn-primary">
                <i class="fa-solid fa-plus"></i> Create RPI
            </button>
        </form>
    </div>
</div>
@endsection
