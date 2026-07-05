@extends('layouts.khas')
@section('title', 'RPI Progress')

@section('tabs')
    <a href="{{ route('parent.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('parent.messages') }}" class="khas-tab">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('parent.behaviour-history') }}" class="khas-tab">
        <i class="fa-solid fa-clock-rotate-left"></i> Behaviour History
    </a>
    <a href="{{ route('parent.rpi-progress') }}" class="khas-tab active">
        <i class="fa-solid fa-file-lines"></i> RPI Progress
    </a>
@endsection

@section('content')
<div class="khas-page">
    <div class="khas-page-header">
        <h5 style="font-weight:600;margin:0">RPI / IEP Progress</h5>
        @if($children->count() > 1)
        <form method="GET" style="display:flex;gap:8px;align-items:center">
            <select name="id" class="khas-select"
                    style="margin-bottom:0;width:auto"
                    onchange="this.form.submit()">
                @foreach($children as $child)
                <option value="{{ $child->id }}"
                        {{ $child->id == $selectedChild?->id ? 'selected' : '' }}>
                    {{ $child->name }}
                </option>
                @endforeach
            </select>
        </form>
        @endif
    </div>

    @forelse($rpis as $rpi)
    @php
        $isApproved = $rpi->status === 'approved';
        $avgProgress= $rpi->overallProgress();
    @endphp
    <div class="khas-card" style="margin-bottom:14px">

        {{-- Header --}}
        <div style="display:flex;justify-content:space-between;
                    align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
            <div>
                <p style="font-size:14px;font-weight:600;margin:0 0 3px">
                    {{ $rpi->period }}
                </p>
                <p style="font-size:12px;color:var(--khas-muted);margin:0">
                    By {{ $rpi->createdBy->name }}
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:11.5px;font-weight:600;padding:3px 12px;border-radius:10px;
                             background:{{ $isApproved ? '#EAFAF1' : '#FFF7EB' }};
                             color:{{ $isApproved ? 'var(--khas-green)' : '#A16207' }}">
                    <i class="fa-solid {{ $isApproved ? 'fa-circle-check' : 'fa-clock' }}"></i>
                    {{ $isApproved ? 'Approved' : 'Pending Approval' }}
                </span>
                <div style="text-align:right">
                    <div style="font-size:20px;font-weight:700;color:var(--khas-blue)">
                        {{ $avgProgress }}%
                    </div>
                    <div style="font-size:11px;color:var(--khas-muted)">overall</div>
                </div>
            </div>
        </div>

        {{-- Overall progress bar --}}
        <div class="khas-progress-bar" style="margin-bottom:18px">
            <div class="khas-progress-fill" style="width:{{ $avgProgress }}%"></div>
        </div>

        {{-- Goals --}}
        @foreach($rpi->goals as $goal)
        <div style="margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;
                        align-items:center;margin-bottom:5px;flex-wrap:wrap;gap:6px">
                <p style="font-size:12.5px;font-weight:500;margin:0">
                    {{ $goal->goal_description }}
                </p>
                @php
                    $gStatusData = match($goal->status) {
                        'achieved'   => ['bg'=>'#EAFAF1','color'=>'var(--khas-green)','label'=>'Achieved'],
                        'in_progress'=> ['bg'=>'#EBF1FA','color'=>'var(--khas-blue)','label'=>'In progress'],
                        default      => ['bg'=>'#F5F7FA','color'=>'var(--khas-muted)','label'=>'Not started'],
                    };
                @endphp
                <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;
                             flex-shrink:0;background:{{ $gStatusData['bg'] }};
                             color:{{ $gStatusData['color'] }}">
                    {{ $gStatusData['label'] }}
                </span>
            </div>
            @if($goal->strategy)
            <p style="font-size:11.5px;color:var(--khas-muted);margin:0 0 6px">
                Strategy: {{ $goal->strategy }}
            </p>
            @endif
            <div class="khas-progress-bar">
                <div class="khas-progress-fill"
                     style="width:{{ $goal->progress_percentage }}%;
                            background:{{ $goal->status === 'achieved' ? 'var(--khas-green)' : 'var(--khas-blue)' }}">
                </div>
            </div>
            <p style="font-size:11px;color:var(--khas-muted);margin:4px 0 0">
                {{ $goal->progress_percentage }}% complete
            </p>
        </div>
        @endforeach

        {{-- Consult teacher link --}}
        @if($selectedChild?->classRoom?->teacher)
        <div style="border-top:1px solid var(--khas-border);padding-top:14px;margin-top:6px">
            <a href="{{ route('parent.messages', [
                    'teacher_id' => $selectedChild->classRoom->teacher->id,
                    'student_id' => $selectedChild->id,
                ]) }}"
               style="font-size:12.5px;color:var(--khas-blue);text-decoration:none;
                      display:inline-flex;align-items:center;gap:6px">
                <i class="fa-solid fa-comments"></i>
                Discuss this RPI with {{ $selectedChild->classRoom->teacher->name }} &rarr;
            </a>
        </div>
        @endif

    </div>
    @empty
    <div class="khas-card" style="text-align:center;padding:40px">
        <i class="fa-solid fa-file-circle-question"
           style="font-size:36px;color:#C0C7D0;margin-bottom:12px;display:block"></i>
        <p style="color:var(--khas-muted)">No RPI documents available yet.</p>
    </div>
    @endforelse
</div>
@endsection
