@extends('layouts.khas')
@section('title', 'RPI / IEP')

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
            <h5 style="font-weight:600;margin-bottom:2px">RPI / IEP Documents</h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                Rancangan Pendidikan Individu
            </p>
        </div>
        <a href="{{ route('teacher.rpi.create') }}"
           class="khas-btn khas-btn-primary" style="width:auto">
            <i class="fa-solid fa-plus"></i> New RPI
        </a>
    </div>

    @forelse($rpis as $rpi)
    @php
        $statusData = match($rpi->status) {
            'pending_approval' => ['bg'=>'#FFF7EB','color'=>'#A16207','label'=>'Pending Approval'],
            'approved'         => ['bg'=>'#EAFAF1','color'=>'var(--khas-green)','label'=>'Approved'],
            'rejected'         => ['bg'=>'#FDEDEC','color'=>'var(--khas-red)','label'=>'Rejected'],
            default            => ['bg'=>'#F5F7FA','color'=>'var(--khas-muted)','label'=>'Draft'],
        };
    @endphp
    <div class="khas-card" style="display:flex;justify-content:space-between;
                                   align-items:center;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:13.5px;font-weight:600;margin-bottom:3px">
                {{ $rpi->student->name }}
            </p>
            <p style="font-size:12px;color:var(--khas-muted);margin:0">
                {{ $rpi->period }}
                &nbsp;&middot;&nbsp; {{ $rpi->goals->count() }} goals
                @if($rpi->approvedBy)
                    &nbsp;&middot;&nbsp; Approved by {{ $rpi->approvedBy->name }}
                @endif
            </p>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span style="padding:4px 12px;border-radius:10px;font-size:11.5px;font-weight:600;
                         background:{{ $statusData['bg'] }};color:{{ $statusData['color'] }}">
                {{ $statusData['label'] }}
            </span>
            <a href="{{ route('teacher.rpi.show', $rpi) }}"
               style="font-size:12.5px;color:var(--khas-blue);text-decoration:none;font-weight:500">
                View &rarr;
            </a>
        </div>
    </div>
    @empty
    <div class="khas-card" style="text-align:center;padding:40px">
        <i class="fa-solid fa-file-circle-plus"
           style="font-size:36px;color:#C0C7D0;margin-bottom:12px;display:block"></i>
        <p style="color:var(--khas-muted)">No RPI documents yet. Click "+ New RPI" to create one.</p>
    </div>
    @endforelse
</div>
@endsection
