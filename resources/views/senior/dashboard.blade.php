@extends('layouts.khas')
@section('title', 'Dashboard')

@section('tabs')
    <a href="{{ route('senior.dashboard') }}" class="khas-tab active">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('senior.rpi-approval') }}" class="khas-tab">
        <i class="fa-solid fa-file-circle-check"></i> RPI Approval
        @if($pending->count() > 0)
            <span class="notif-dot">{{ $pending->count() }}</span>
        @endif
    </a>
@endsection

@section('content')
<div class="khas-page">

    <h5 style="font-weight:600;margin-bottom:3px">Senior Assistant Dashboard</h5>
    <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:20px">
        Welcome, {{ auth()->user()->name }}
        &nbsp;&middot;&nbsp; {{ now()->format('l, j F Y') }}
    </p>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);
                gap:10px;margin-bottom:24px">
        <div class="stat-card">
            <div style="font-size:22px;color:var(--khas-amber);margin-bottom:6px">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="stat-num"
                 style="color:{{ $pending->count() > 0 ? 'var(--khas-amber)' : 'var(--khas-muted)' }}">
                {{ $pending->count() }}
            </div>
            <div class="stat-label">Pending approval</div>
        </div>
        <div class="stat-card">
            <div style="font-size:22px;color:var(--khas-green);margin-bottom:6px">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-green)">
                {{ $totalApproved }}
            </div>
            <div class="stat-label">Approved RPIs</div>
        </div>
        <div class="stat-card">
            <div style="font-size:22px;color:var(--khas-red);margin-bottom:6px">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-red)">
                {{ $totalRejected }}
            </div>
            <div class="stat-label">Rejected RPIs</div>
        </div>
    </div>

    @if($pending->count() > 0)
    <div class="khas-alert khas-alert-warning" style="margin-bottom:16px">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>
            You have {{ $pending->count() }} RPI
            {{ $pending->count() === 1 ? 'document' : 'documents' }}
            waiting for review.
            <a href="{{ route('senior.rpi-approval') }}"
               style="color:var(--khas-blue);font-weight:600;margin-left:8px;text-decoration:none">
                Review now &rarr;
            </a>
        </span>
    </div>

    {{-- Pending list preview --}}
    @foreach($pending as $rpi)
    <div class="khas-card"
         style="display:flex;justify-content:space-between;
                align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:10px">
        <div>
            <p style="font-size:13.5px;font-weight:600;margin-bottom:3px">
                {{ $rpi->student->name }}
            </p>
            <p style="font-size:12px;color:var(--khas-muted);margin:0">
                {{ $rpi->period }}
                &nbsp;&middot;&nbsp; By {{ $rpi->createdBy->name }}
                &nbsp;&middot;&nbsp; {{ $rpi->goals->count() }} goals
                &nbsp;&middot;&nbsp; Submitted {{ $rpi->updated_at->diffForHumans() }}
            </p>
        </div>
        <a href="{{ route('senior.rpi-approval.show', $rpi) }}"
           class="khas-btn khas-btn-primary" style="width:auto">
            <i class="fa-solid fa-eye"></i> Review
        </a>
    </div>
    @endforeach

    @else
    <div class="khas-card" style="text-align:center;padding:48px">
        <i class="fa-solid fa-party-horn"
           style="font-size:36px;color:var(--khas-green);margin-bottom:12px;display:block"></i>
        <p style="font-weight:600;margin-bottom:4px">All caught up!</p>
        <p style="font-size:13px;color:var(--khas-muted)">
            No RPI documents pending approval right now.
        </p>
    </div>
    @endif

</div>
@endsection
