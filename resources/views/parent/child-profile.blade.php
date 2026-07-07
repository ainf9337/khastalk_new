@extends('layouts.khas')
@section('title', $student->name)

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
    <a href="{{ route('parent.behaviour-history', ['id' => $student->id]) }}"
       class="khas-tab">
        <i class="fa-solid fa-clock-rotate-left"></i> Behaviour History
    </a>
    <a href="{{ route('parent.rpi-progress', ['id' => $student->id]) }}"
       class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI Progress
    </a>
@endsection

@section('content')
<div class="khas-page">

    <a href="{{ route('parent.dashboard') }}"
       style="font-size:12px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:14px">
        <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>

    {{-- Student header --}}
    <div class="khas-card"
         style="display:flex;align-items:center;gap:18px;
                flex-wrap:wrap;margin-bottom:16px">
        <div style="width:56px;height:56px;border-radius:50%;
                    background:#EDE9FE;display:flex;align-items:center;
                    justify-content:center;font-size:22px;font-weight:700;
                    color:#5B4ECC;flex-shrink:0">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div>
            <h5 style="font-weight:700;font-size:17px;margin-bottom:4px">
                {{ $student->name }}
            </h5>
            <span class="badge-autism">
                {{ $student->diagnosis ?? 'Autism' }}
            </span>
            <span style="font-size:12px;color:var(--khas-muted);margin-left:10px">
                {{ $student->classRoom?->class_name ?? '' }}
                @if($student->classRoom?->teacher)
                    &nbsp;&middot;&nbsp;
                    Teacher: {{ $student->classRoom->teacher->name }}
                @endif
            </span>
        </div>
        <div style="margin-left:auto;font-size:12px;color:var(--khas-muted)">
            <i class="fa-solid fa-circle-info" style="color:var(--khas-blue)"></i>
            &nbsp;You can update your child's profile below
        </div>
    </div>

    {{-- View: current profile info --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;
                gap:14px;margin-bottom:20px">

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);
                       margin-bottom:10px">
                <i class="fa-solid fa-bolt" style="color:var(--khas-red)"></i>
                SENSORY TRIGGERS
            </p>
            @if($student->profile?->sensory_triggers)
                @foreach(explode(',', $student->profile->sensory_triggers) as $t)
                    <span style="display:inline-block;background:#FDEDEC;
                                 color:var(--khas-red);font-size:11px;
                                 padding:3px 10px;border-radius:12px;
                                 margin:3px 3px 0 0">
                        {{ trim($t) }}
                    </span>
                @endforeach
            @else
                <p style="font-size:12.5px;color:var(--khas-muted)">Not specified yet</p>
            @endif
        </div>

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);
                       margin-bottom:10px">
                <i class="fa-solid fa-heart" style="color:var(--khas-green)"></i>
                CALMING STRATEGIES
            </p>
            @if($student->profile?->calming_strategies)
                @foreach(explode(',', $student->profile->calming_strategies) as $s)
                    <span style="display:inline-block;background:#EAFAF1;
                                 color:var(--khas-green);font-size:11px;
                                 padding:3px 10px;border-radius:12px;
                                 margin:3px 3px 0 0">
                        {{ trim($s) }}
                    </span>
                @endforeach
            @else
                <p style="font-size:12.5px;color:var(--khas-muted)">Not specified yet</p>
            @endif
        </div>

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);
                       margin-bottom:8px">
                <i class="fa-solid fa-notes-medical" style="color:var(--khas-blue)"></i>
                MEDICAL INFO
            </p>
            <p style="font-size:13px;margin:0">
                {{ $student->profile?->medical_info ?? 'Not specified yet' }}
            </p>
        </div>

        <div class="khas-card">
            <p style="font-size:12px;font-weight:600;color:var(--khas-muted);
                       margin-bottom:8px">
                <i class="fa-solid fa-comments" style="color:var(--khas-blue)"></i>
                COMMUNICATION LEVEL
            </p>
            <p style="font-size:13px;margin:0">
                {{ $student->profile?->communication_level ?? 'Not specified yet' }}
            </p>
        </div>

    </div>

    {{-- Edit form --}}
    <div class="khas-card"
         style="border-top:3px solid var(--khas-blue)">

        <p style="font-size:14px;font-weight:600;margin-bottom:4px">
            <i class="fa-solid fa-pen-to-square"
               style="color:var(--khas-blue)"></i>
            &nbsp;Update {{ $student->name }}'s Profile
        </p>
        <p style="font-size:12.5px;color:var(--khas-muted);margin-bottom:20px">
            This information helps the teacher understand and support your child better.
            Only you and the teacher can see this.
        </p>

        <form method="POST"
              action="{{ route('parent.child.profile.update', $student) }}">
            @csrf
            @method('PATCH')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                    <label class="khas-label">
                        <i class="fa-solid fa-bolt"
                           style="color:var(--khas-red);font-size:11px"></i>
                        &nbsp;Sensory Triggers
                    </label>
                    <textarea name="sensory_triggers"
                              class="khas-textarea" rows="3"
                              placeholder="e.g. Loud noises, Bright lights, Transitions">{{ old('sensory_triggers', $student->profile?->sensory_triggers) }}</textarea>
                    <p style="font-size:11px;color:var(--khas-muted);margin-top:4px">
                        Separate multiple triggers with commas
                    </p>
                </div>

                <div>
                    <label class="khas-label">
                        <i class="fa-solid fa-heart"
                           style="color:var(--khas-green);font-size:11px"></i>
                        &nbsp;Calming Strategies
                    </label>
                    <textarea name="calming_strategies"
                              class="khas-textarea" rows="3"
                              placeholder="e.g. Hug, Favourite toy, Quiet corner">{{ old('calming_strategies', $student->profile?->calming_strategies) }}</textarea>
                    <p style="font-size:11px;color:var(--khas-muted);margin-top:4px">
                        What works best to calm your child?
                    </p>
                </div>
            </div>

            <div style="margin-top:14px">
                <label class="khas-label">
                    <i class="fa-solid fa-notes-medical"
                       style="color:var(--khas-blue);font-size:11px"></i>
                    &nbsp;Medical Info & Allergies
                </label>
                <textarea name="medical_info"
                          class="khas-textarea" rows="2"
                          placeholder="e.g. Epilepsy — has medication. Allergic to peanuts.">{{ old('medical_info', $student->profile?->medical_info) }}</textarea>
            </div>

            <div style="margin-top:14px">
                <label class="khas-label">
                    <i class="fa-solid fa-comments"
                       style="color:var(--khas-blue);font-size:11px"></i>
                    &nbsp;Communication Level
                </label>
                <select name="communication_level" class="khas-select"
                        style="max-width:300px;margin-bottom:0">
                    <option value="">— Select —</option>
                    @foreach([
                        'Verbal',
                        'Limited verbal',
                        'Non-verbal',
                        'AAC device',
                        'Selective mutism',
                    ] as $level)
                    <option value="{{ $level }}"
                        {{ old('communication_level',
                               $student->profile?->communication_level) === $level
                           ? 'selected' : '' }}>
                        {{ $level }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-top:20px">
                <button type="submit" class="khas-btn khas-btn-primary"
                        style="width:auto">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Profile
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
