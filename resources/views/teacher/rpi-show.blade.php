@extends('layouts.khas')
@section('title', 'RPI — ' . $rpi->student->name)

@section('tabs')
    <a href="{{ route('teacher.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('teacher.students') }}" class="khas-tab">
        <i class="fa-solid fa-users"></i> Students
    </a>
    <a href="{{ route('teacher.rpi') }}" class="khas-tab active">
        <i class="fa-solid fa-file-lines"></i> RPI / IEP
    </a>
    <a href="{{ route('teacher.messages') }}" class="khas-tab">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('teacher.reports') }}" class="khas-tab">
        <i class="fa-solid fa-chart-bar"></i> Reports
    </a>
@endsection

@section('content')
<div class="khas-page">

    <div class="khas-page-header">
        <div>
            <a href="{{ route('teacher.rpi') }}"
               style="font-size:12px;color:var(--khas-muted);text-decoration:none;
                      display:inline-flex;align-items:center;gap:6px;margin-bottom:8px">
                <i class="fa-solid fa-arrow-left"></i> All RPIs
            </a>
            <h5 style="font-weight:600;margin:0 0 2px">
                RPI — {{ $rpi->student->name }}
            </h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                Period: {{ $rpi->period }}
            </p>
        </div>
        @php
            $statusData = match($rpi->status) {
                'pending_approval' => ['bg'=>'#FFF7EB','color'=>'#A16207','label'=>'Pending Approval'],
                'approved'         => ['bg'=>'#EAFAF1','color'=>'var(--khas-green)','label'=>'Approved'],
                'rejected'         => ['bg'=>'#FDEDEC','color'=>'var(--khas-red)','label'=>'Rejected'],
                default            => ['bg'=>'#F5F7FA','color'=>'var(--khas-muted)','label'=>'Draft'],
            };
        @endphp
        <span style="padding:5px 14px;border-radius:10px;font-size:12px;font-weight:600;
                     background:{{ $statusData['bg'] }};color:{{ $statusData['color'] }}">
            {{ $statusData['label'] }}
        </span>
    </div>

    {{-- Goals list --}}
    <div class="khas-card" style="margin-bottom:14px">
        <p style="font-size:13.5px;font-weight:600;margin-bottom:14px">
            Goals ({{ $rpi->goals->count() }})
        </p>
        @forelse($rpi->goals as $goal)
        <div style="border:1px solid var(--khas-border);border-radius:10px;
                    padding:14px;margin-bottom:10px">
            <p style="font-size:13px;font-weight:600;margin-bottom:4px">
                {{ $goal->goal_description }}
            </p>
            <p style="font-size:12px;color:var(--khas-muted);margin-bottom:10px">
                Strategy: {{ $goal->strategy ?? 'Not specified' }}
                @if($goal->target_date)
                    &nbsp;&middot;&nbsp; Target: {{ $goal->target_date->format('d M Y') }}
                @endif
            </p>
            <div class="khas-progress-bar" style="margin-bottom:8px">
                <div class="khas-progress-fill"
                     style="width:{{ $goal->progress_percentage }}%"></div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center">
                <span style="font-size:11px;color:var(--khas-muted)">
                    {{ $goal->progress_percentage }}% progress
                </span>
                @if($rpi->status === 'draft')
                <form method="POST"
                      action="{{ route('teacher.rpi.goals.update', $goal) }}"
                      style="display:flex;gap:8px;align-items:center">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="rpi_id" value="{{ $rpi->id }}">
                    <input type="number" name="progress" min="0" max="100"
                           value="{{ $goal->progress_percentage }}"
                           class="khas-input"
                           style="width:70px;margin-bottom:0;padding:5px 8px;font-size:12px">
                    <select name="status" class="khas-select"
                            style="margin-bottom:0;padding:5px 8px;font-size:12px;width:auto">
                        <option value="not_started"  {{ $goal->status==='not_started' ?'selected':'' }}>Not started</option>
                        <option value="in_progress"  {{ $goal->status==='in_progress' ?'selected':'' }}>In progress</option>
                        <option value="achieved"     {{ $goal->status==='achieved'    ?'selected':'' }}>Achieved</option>
                    </select>
                    <button type="submit"
                            style="padding:5px 12px;background:var(--khas-blue);color:#fff;
                                   border:none;border-radius:6px;font-size:11px;
                                   cursor:pointer;font-family:Poppins,sans-serif">
                        Save
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <p style="font-size:13px;color:var(--khas-muted)">
            No goals yet. Add your first goal below.
        </p>
        @endforelse
    </div>

    @if($rpi->status === 'draft')
    {{-- Add goal --}}
    <div class="khas-card" style="margin-bottom:14px">
        <p style="font-size:13px;font-weight:600;margin-bottom:14px">
            <i class="fa-solid fa-plus" style="color:var(--khas-blue)"></i>
            Add New Goal
        </p>
        <form method="POST" action="{{ route('teacher.rpi.goals.store', $rpi) }}">
            @csrf
            <label class="khas-label">Goal description *</label>
            <input type="text" name="goal_description" class="khas-input"
                   placeholder="e.g. Improve verbal communication in group settings" required>
            <label class="khas-label">Strategy / method</label>
            <input type="text" name="strategy" class="khas-input"
                   placeholder="e.g. Daily 10-min peer conversation practice">
            <label class="khas-label">Target date</label>
            <input type="date" name="target_date" class="khas-input">
            <button type="submit"
                    style="padding:9px 20px;background:var(--khas-blue);color:#fff;
                           border:none;border-radius:8px;font-size:13px;font-weight:600;
                           cursor:pointer;font-family:Poppins,sans-serif;
                           display:inline-flex;align-items:center;gap:8px">
                <i class="fa-solid fa-plus"></i> Add Goal
            </button>
        </form>
    </div>

    {{-- Submit for approval --}}
    @if($rpi->goals->count() > 0)
    <div class="khas-card">
        <p style="font-size:13px;font-weight:600;margin-bottom:4px">Ready for approval?</p>
        <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:14px">
            Once submitted, you cannot edit until the Senior Assistant reviews it.
        </p>
        <form method="POST" action="{{ route('teacher.rpi.submit', $rpi) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="khas-btn khas-btn-success" style="width:auto">
                <i class="fa-solid fa-paper-plane"></i> Submit for Approval
            </button>
        </form>
    </div>
    @endif
    @endif

</div>
@endsection
