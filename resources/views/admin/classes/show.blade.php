@extends('layouts.khas')
@section('title', $class->class_name)

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

    <a href="{{ route('admin.classes.index') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Back to Classes
    </a>

    {{-- Class header --}}
    <div class="khas-card"
         style="display:flex;align-items:center;gap:18px;
                flex-wrap:wrap;margin-bottom:20px">
        <div style="width:56px;height:56px;border-radius:12px;
                    background:var(--khas-blue-light);display:flex;
                    align-items:center;justify-content:center;flex-shrink:0">
            <i class="fa-solid fa-school"
               style="font-size:24px;color:var(--khas-blue)"></i>
        </div>
        <div>
            <h5 style="font-weight:700;font-size:18px;margin-bottom:4px">
                {{ $class->class_name }}
            </h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                <i class="fa-solid fa-chalkboard" style="font-size:11px"></i>
                &nbsp;{{ $class->teacher?->name ?? 'No teacher assigned' }}
                &nbsp;&nbsp;
                <i class="fa-solid fa-users" style="font-size:11px"></i>
                &nbsp;{{ $class->students->count() }} students
                &nbsp;&nbsp;
                <i class="fa-solid fa-calendar" style="font-size:11px"></i>
                &nbsp;{{ $class->academic_year }}
            </p>
        </div>
        <a href="{{ route('admin.students.create') }}"
           class="khas-btn khas-btn-primary"
           style="width:auto;margin-left:auto">
            <i class="fa-solid fa-user-plus"></i> Enrol Student
        </a>
    </div>

    {{-- Student list --}}
    @if($class->students->isEmpty())
    <div class="khas-card" style="text-align:center;padding:40px">
        <i class="fa-solid fa-user-slash"
           style="font-size:36px;color:#C0C7D0;margin-bottom:12px;display:block"></i>
        <p style="color:var(--khas-muted)">
            No students enrolled in this class yet.
        </p>
        <a href="{{ route('admin.students.create') }}"
           style="font-size:13px;color:var(--khas-blue);text-decoration:none">
            Enrol the first student &rarr;
        </a>
    </div>
    @else
    <div class="khas-card" style="padding:0;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:var(--khas-bg)">
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">
                        STUDENT
                    </th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">
                        PARENT
                    </th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">
                        COMMUNICATION
                    </th>
                    <th style="text-align:left;padding:11px 16px;font-size:11.5px;
                               color:var(--khas-muted);font-weight:600">
                        MOVE TO CLASS
                    </th>
                    <th style="padding:11px 16px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($class->students->sortBy('name') as $student)
                <tr style="border-top:1px solid var(--khas-border)">

                    {{-- Student name --}}
                    <td style="padding:11px 16px">
                        <a href="{{ route('admin.students.show', $student) }}"
                           style="font-weight:600;color:var(--khas-text);
                                  text-decoration:none;display:block;margin-bottom:3px">
                            {{ $student->name }}
                        </a>
                        <span class="badge-autism" style="font-size:10px">
                            Autism
                        </span>
                    </td>

                    {{-- Parent --}}
                    <td style="padding:11px 16px;color:var(--khas-muted);
                               font-size:12.5px">
                        @if($student->parent)
                            <span>{{ $student->parent->name }}</span>
                            <br>
                            <span style="font-size:11px">
                                {{ $student->parent->phone ?? '' }}
                            </span>
                        @else
                            <span style="color:#C0C7D0">No parent linked</span>
                        @endif
                    </td>

                    {{-- Communication level --}}
                    <td style="padding:11px 16px;font-size:12.5px;
                               color:var(--khas-muted)">
                        {{ $student->profile?->communication_level ?? '—' }}
                    </td>

                    {{-- Change class inline --}}
                    <td style="padding:11px 16px">
                        <form method="POST"
                              action="{{ route('admin.students.change-class', $student) }}"
                              style="display:flex;gap:7px;align-items:center">
                            @csrf
                            @method('PATCH')
                            <select name="class_id" class="khas-select"
                                    style="margin-bottom:0;font-size:12px;
                                           padding:6px 10px;width:auto;min-width:140px">
                                @foreach($allClasses as $otherClass)
                                <option value="{{ $otherClass->id }}">
                                    {{ $otherClass->class_name }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    style="padding:6px 12px;background:var(--khas-blue);
                                           color:#fff;border:none;border-radius:7px;
                                           font-size:11.5px;font-weight:600;cursor:pointer;
                                           font-family:Poppins,sans-serif;
                                           white-space:nowrap;transition:all 0.18s"
                                    onmouseover="this.style.background='var(--khas-blue-dark)'"
                                    onmouseout="this.style.background='var(--khas-blue)'">
                                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                                Move
                            </button>
                        </form>
                    </td>

                    {{-- View button --}}
                    <td style="padding:11px 16px;text-align:right">
                        <a href="{{ route('admin.students.show', $student) }}"
                           style="font-size:12px;color:var(--khas-blue);
                                  text-decoration:none;font-weight:500">
                            View &rarr;
                        </a>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection
