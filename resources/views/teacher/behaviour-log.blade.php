@extends('layouts.khas')
@section('title', 'Log Behaviour')

@section('tabs')
    <a href="{{ route('teacher.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('teacher.students') }}" class="khas-tab active">
        <i class="fa-solid fa-users"></i> Students
    </a>
    <a href="{{ route('teacher.rpi') }}" class="khas-tab">
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
<div class="khas-page" style="max-width:700px;margin:0 auto">

    <a href="{{ route('teacher.dashboard') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:12px">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
    <h5 style="font-weight:600;margin-bottom:2px">Log Behaviour</h5>
    <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:20px">
        Fields marked <span style="color:var(--khas-red)">*</span> are required
    </p>

    <form method="POST" action="{{ route('teacher.behaviour-log.store') }}" id="logForm">
        @csrf

        {{-- Student & Time --}}
        <div class="khas-card" style="margin-bottom:12px">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:12px">
                <i class="fa-solid fa-user"></i> STUDENT & TIME
            </p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label class="khas-label">Student <span style="color:var(--khas-red)">*</span></label>
                    <select name="student_id" class="khas-select" required>
                        <option value="">— Select student —</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}"
                            {{ (old('student_id', $preStudentId) == $student->id) ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="khas-label">Time of incident</label>
                    <input type="time" name="incident_time" class="khas-input"
                           style="margin-bottom:0" value="{{ now()->format('H:i') }}">
                </div>
            </div>
        </div>

        {{-- Behaviour Type --}}
        <div class="khas-card" style="margin-bottom:12px">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:12px">
                <i class="fa-solid fa-tag"></i>
                BEHAVIOUR TYPE <span style="color:var(--khas-red)">*</span>
            </p>
            <input type="hidden" name="behaviour_type" id="behaviourTypeInput"
                   value="{{ old('behaviour_type') }}">
            <div class="pill-group" style="display:flex;flex-wrap:wrap;gap:7px">
                @foreach($behaviourTypes as $bt)
                <button type="button"
                        class="pill-opt {{ old('behaviour_type') === $bt ? 'selected' : '' }}"
                        onclick="selectPill(this, '{{ $bt }}')">
                    {{ $bt }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Severity & Duration --}}
        <div class="khas-card" style="margin-bottom:12px">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:12px">
                <i class="fa-solid fa-chart-simple"></i> DETAILS
            </p>
            <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:16px">
                <div>
                    <label class="khas-label">
                        Severity level <span style="color:var(--khas-red)">*</span>
                    </label>
                    <input type="hidden" name="severity" id="severityInput"
                           value="{{ old('severity', 0) }}">
                    <div style="display:flex;gap:8px;margin-top:4px">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button"
                                class="sev-btn {{ old('severity') == $i ? 'selected' : '' }}"
                                onclick="selectSev(this, {{ $i }})">
                            {{ $i }}
                        </button>
                        @endfor
                    </div>
                    <div style="font-size:10px;color:var(--khas-muted);margin-top:5px;
                                display:flex;justify-content:space-between">
                        <span>Mild</span><span>Severe</span>
                    </div>
                </div>
                <div>
                    <label class="khas-label">Duration</label>
                    <select name="duration" class="khas-select" style="margin-bottom:0">
                        @foreach($durationOptions as $d)
                        <option {{ old('duration') === $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Triggers & Response --}}
        <div class="khas-card" style="margin-bottom:12px">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:4px">
                <i class="fa-solid fa-magnifying-glass"></i> CONTEXT
            </p>
            <p style="font-size:10.5px;font-weight:700;letter-spacing:0.6px;
                      color:var(--khas-muted);text-transform:uppercase;margin:12px 0 8px">
                What triggered it?
            </p>
            <div class="chk-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:7px">
                @foreach($triggerOptions as $t)
                <label class="chk-card {{ in_array($t, old('triggers', [])) ? 'checked' : '' }}">
                    <input type="checkbox" name="triggers[]" value="{{ $t }}"
                           {{ in_array($t, old('triggers', [])) ? 'checked' : '' }}
                           style="display:none" onchange="toggleCard(this)">
                    <div class="chk-box {{ in_array($t, old('triggers', [])) ? 'on' : '' }}">
                        <i class="fa-solid fa-check" style="font-size:9px;color:#fff"></i>
                    </div>
                    <span style="font-size:11px;font-weight:500">{{ $t }}</span>
                </label>
                @endforeach
            </div>

            <p style="font-size:10.5px;font-weight:700;letter-spacing:0.6px;
                      color:var(--khas-muted);text-transform:uppercase;margin:14px 0 8px">
                Teacher's response
            </p>
            <div class="chk-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:7px">
                @foreach($responseOptions as $r)
                <label class="chk-card {{ in_array($r, old('responses', [])) ? 'checked' : '' }}">
                    <input type="checkbox" name="responses[]" value="{{ $r }}"
                           {{ in_array($r, old('responses', [])) ? 'checked' : '' }}
                           style="display:none" onchange="toggleCard(this)">
                    <div class="chk-box {{ in_array($r, old('responses', [])) ? 'on' : '' }}">
                        <i class="fa-solid fa-check" style="font-size:9px;color:#fff"></i>
                    </div>
                    <span style="font-size:11px;font-weight:500">{{ $r }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Outcome --}}
        <div class="khas-card" style="margin-bottom:12px">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);margin-bottom:12px">
                <i class="fa-solid fa-circle-check"></i> OUTCOME
            </p>
            <label class="khas-label">Behaviour resolved?</label>
            <div style="display:flex;gap:8px;margin-bottom:14px">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="radio" name="resolved" value="1"
                           {{ old('resolved', '1') === '1' ? 'checked' : '' }}>
                    <span style="font-size:13px">Yes</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <input type="radio" name="resolved" value="0"
                           {{ old('resolved') === '0' ? 'checked' : '' }}>
                    <span style="font-size:13px">No</span>
                </label>
            </div>
            <label class="khas-label">Notes (optional)</label>
            <textarea name="notes" class="khas-textarea" rows="2"
                      placeholder="Additional observations...">{{ old('notes') }}</textarea>
        </div>

        {{-- Notify parent --}}
        <div class="khas-card" style="margin-bottom:16px;display:flex;
                                       align-items:center;justify-content:space-between">
            <div>
                <p style="font-size:13px;font-weight:500;margin-bottom:2px">
                    Notify parent about this log
                </p>
                <p style="font-size:11.5px;color:var(--khas-muted);margin:0">
                    A summary message will be sent automatically
                </p>
            </div>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                <span style="font-size:12px;color:var(--khas-muted)">Notify</span>
                <input type="checkbox" name="notify_parent" id="notifyToggle" checked>
            </label>
        </div>

        {{-- Submit --}}
        <div style="display:flex;gap:10px">
            <a href="{{ route('teacher.dashboard') }}"
               class="khas-btn khas-btn-secondary" style="flex:1;text-align:center">
                Cancel
            </a>
            <button type="submit" class="khas-btn khas-btn-primary" style="flex:2">
                <i class="fa-solid fa-floppy-disk"></i> Save Log
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function selectPill(el, val) {
    document.querySelectorAll('.pill-opt').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('behaviourTypeInput').value = val;
}
function selectSev(el, val) {
    document.querySelectorAll('.sev-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('severityInput').value = val;
}
function toggleCard(cb) {
    const card = cb.closest('.chk-card');
    const box  = card.querySelector('.chk-box');
    if (cb.checked) {
        card.classList.add('checked');
        box.classList.add('on');
    } else {
        card.classList.remove('checked');
        box.classList.remove('on');
    }
}
</script>
@endpush
