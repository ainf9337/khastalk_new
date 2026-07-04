@extends('layouts.khas')
@section('title', 'Emergency Alert')

@section('content')
<div style="background:var(--khas-red);padding:12px 20px;text-align:center">
    <p style="color:#fff;font-weight:700;font-size:14px;margin:0">
        <i class="fa-solid fa-triangle-exclamation"></i>
        EMERGENCY ALERT SYSTEM — Only use in genuine emergencies
    </p>
</div>

<div class="khas-page" style="max-width:680px;margin:0 auto">

    {{-- Alert form --}}
    <div class="khas-card"
         style="border-left:4px solid var(--khas-red);margin-bottom:16px">
        <h5 style="font-weight:700;color:var(--khas-red);margin-bottom:16px">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Trigger Emergency Alert
        </h5>
        <form method="POST" action="{{ route('teacher.emergency.store') }}">
            @csrf
            <label class="khas-label">Student involved *</label>
            <select name="student_id" class="khas-select" required>
                <option value="">— Select student —</option>
                @foreach($students as $student)
                <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>

            <label class="khas-label">Type of emergency *</label>
            <select name="alert_type" class="khas-select" required>
                <option value="">— Select type —</option>
                @foreach($alertTypes as $at)
                <option>{{ $at }}</option>
                @endforeach
            </select>

            <label class="khas-label">Description (brief)</label>
            <textarea name="description" class="khas-textarea" rows="2"
                      placeholder="Brief description of the situation..."></textarea>

            <button type="submit" class="khas-btn khas-btn-danger"
                    style="width:100%;margin-top:14px;font-size:14px">
                <i class="fa-solid fa-triangle-exclamation"></i>
                SEND EMERGENCY ALERT NOW
            </button>
        </form>
    </div>

    {{-- Alert history --}}
    <div class="khas-card">
        <p style="font-size:13.5px;font-weight:600;margin-bottom:14px">
            <i class="fa-solid fa-clock-rotate-left"></i> Recent Alerts
        </p>
        @if($alerts->isEmpty())
            <p style="font-size:13px;color:var(--khas-muted)">No alerts sent yet.</p>
        @else
            @foreach($alerts as $alert)
            <div style="border-bottom:1px solid var(--khas-border);padding:11px 0;
                        display:flex;justify-content:space-between;align-items:flex-start">
                <div>
                    <span style="font-size:12.5px;font-weight:600">
                        {{ $alert->alert_type }}
                    </span>
                    &nbsp;&middot;&nbsp;
                    <span style="font-size:12px;color:var(--khas-muted)">
                        {{ $alert->student->name }}
                    </span>
                    @if($alert->description)
                    <p style="font-size:11.5px;color:var(--khas-muted);margin:4px 0 0">
                        {{ $alert->description }}
                    </p>
                    @endif
                </div>
                <div style="text-align:right;flex-shrink:0;padding-left:14px">
                    @php
                        $statusBg = match($alert->status) {
                            'confirmed' => '#EAFAF1',
                            'resolved'  => '#EBF1FA',
                            default     => '#FDEDEC',
                        };
                        $statusColor = match($alert->status) {
                            'confirmed' => 'var(--khas-green)',
                            'resolved'  => 'var(--khas-blue)',
                            default     => 'var(--khas-red)',
                        };
                    @endphp
                    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:10px;
                                 background:{{ $statusBg }};color:{{ $statusColor }}">
                        {{ ucfirst($alert->status) }}
                    </span>
                    <p style="font-size:10.5px;color:var(--khas-muted);margin:5px 0 0">
                        {{ $alert->created_at->format('d M, g:i a') }}
                    </p>
                </div>
            </div>
            @endforeach
        @endif
    </div>

</div>
@endsection
