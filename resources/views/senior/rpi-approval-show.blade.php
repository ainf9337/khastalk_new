@extends('layouts.khas')
@section('title', 'Review RPI')

@section('tabs')
    <a href="{{ route('senior.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('senior.rpi-approval') }}" class="khas-tab active">
        <i class="fa-solid fa-file-circle-check"></i> RPI Approval
    </a>
@endsection

@section('content')
<div class="khas-page">

    <div class="khas-page-header">
        <div>
            <a href="{{ route('senior.rpi-approval') }}"
               style="font-size:12px;color:var(--khas-muted);text-decoration:none;
                      display:inline-flex;align-items:center;gap:6px;margin-bottom:8px">
                <i class="fa-solid fa-arrow-left"></i> All RPIs
            </a>
            <h5 style="font-weight:600;margin:0 0 2px">
                RPI Review — {{ $rpi->student->name }}
            </h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                {{ $rpi->period }}
                &nbsp;&middot;&nbsp; Submitted by {{ $rpi->createdBy->name }}
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

    {{-- Goals --}}
    <div class="khas-card" style="margin-bottom:16px">
        <p style="font-size:13.5px;font-weight:600;margin-bottom:14px">
            Goals ({{ $rpi->goals->count() }})
        </p>
        @forelse($rpi->goals as $i => $goal)
        <div style="border:1px solid var(--khas-border);border-radius:9px;
                    padding:13px;margin-bottom:10px">
            <p style="font-weight:600;margin-bottom:4px">
                {{ $i + 1 }}. {{ $goal->goal_description }}
            </p>
            <p style="font-size:12px;color:var(--khas-muted);margin:0">
                Strategy: {{ $goal->strategy ?? 'Not specified' }}
                @if($goal->target_date)
                    &nbsp;&middot;&nbsp; Target: {{ $goal->target_date->format('d M Y') }}
                @endif
            </p>
        </div>
        @empty
        <p style="font-size:13px;color:var(--khas-muted)">No goals in this RPI.</p>
        @endforelse
    </div>

    {{-- Approve / Reject --}}
    @if($rpi->status === 'pending_approval')
    <div class="khas-card"
         style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
        <p style="font-size:13.5px;font-weight:600;margin:0;flex:1">
            Your decision:
        </p>
        <form method="POST"
              action="{{ route('senior.rpi-approval.update', $rpi) }}"
              style="display:flex;gap:10px">
            @csrf
            @method('PATCH')
            <button name="action" value="approve"
                    class="khas-btn khas-btn-success" style="width:auto">
                <i class="fa-solid fa-circle-check"></i> Approve
            </button>
            <button name="action" value="reject"
                    onclick="return confirm('Are you sure you want to reject this RPI?')"
                    class="khas-btn"
                    style="width:auto;background:#fff;color:var(--khas-red);
                           border:2px solid var(--khas-red)">
                <i class="fa-solid fa-circle-xmark"></i> Reject
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
