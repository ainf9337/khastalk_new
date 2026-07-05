@extends('layouts.khas')
@section('title', 'RPI Approval')

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
    <h5 style="font-weight:600;margin-bottom:18px">All RPI Documents</h5>

    @forelse($allRpis as $rpi)
    @php
        $statusData = match($rpi->status) {
            'pending_approval' => ['bg'=>'#FFF7EB','color'=>'#A16207','label'=>'Pending Approval'],
            'approved'         => ['bg'=>'#EAFAF1','color'=>'var(--khas-green)','label'=>'Approved'],
            'rejected'         => ['bg'=>'#FDEDEC','color'=>'var(--khas-red)','label'=>'Rejected'],
            default            => ['bg'=>'#F5F7FA','color'=>'var(--khas-muted)','label'=>'Draft'],
        };
    @endphp
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
            </p>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span style="padding:4px 12px;border-radius:10px;font-size:11.5px;
                         font-weight:600;background:{{ $statusData['bg'] }};
                         color:{{ $statusData['color'] }}">
                {{ $statusData['label'] }}
            </span>
            <a href="{{ route('senior.rpi-approval.show', $rpi) }}"
               style="font-size:12.5px;text-decoration:none;font-weight:500;
                      color:{{ $rpi->status === 'pending_approval' ? 'var(--khas-blue)' : 'var(--khas-muted)' }}">
                {{ $rpi->status === 'pending_approval' ? 'Review →' : 'View' }}
            </a>
        </div>
    </div>
    @empty
    <div class="khas-card" style="text-align:center;padding:48px">
        <i class="fa-solid fa-inbox"
           style="font-size:36px;color:#C0C7D0;margin-bottom:12px;display:block"></i>
        <p style="color:var(--khas-muted)">No RPI documents in the system yet.</p>
    </div>
    @endforelse
</div>
@endsection
