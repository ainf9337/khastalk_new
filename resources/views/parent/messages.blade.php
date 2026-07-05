@extends('layouts.khas')
@section('title', 'Messages')

@section('tabs')
    <a href="{{ route('parent.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('parent.messages') }}" class="khas-tab active">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('parent.behaviour-history') }}" class="khas-tab">
        <i class="fa-solid fa-clock-rotate-left"></i> Behaviour History
    </a>
    <a href="{{ route('parent.rpi-progress') }}" class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI Progress
    </a>
@endsection

@section('content')
<div class="khas-page" style="padding-bottom:0">
<div class="msg-wrap">

    {{-- Left: teacher list --}}
    <div class="msg-left">
        <div class="msg-left-hd">
            <p style="font-size:13.5px;font-weight:600;margin:0">Messages</p>
        </div>
        <div class="msg-list">
            @forelse($children as $child)
                @if($child->classRoom?->teacher)
                @php
                    $teacher  = $child->classRoom->teacher;
                    $isActive = ($teacher->id == $withTeacher &&
                                 $child->id   == $withStudent);
                @endphp
                <a href="{{ route('parent.messages', [
                        'teacher_id' => $teacher->id,
                        'student_id' => $child->id,
                    ]) }}"
                   class="conv-item {{ $isActive ? 'active' : '' }}">
                    <div class="conv-av"
                         style="background:{{ $isActive ? 'var(--khas-blue)' : 'var(--khas-green)' }}">
                        {{ strtoupper(substr($teacher->name, 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <p style="font-size:12px;font-weight:600;margin:0 0 2px;
                                   color:var(--khas-text)">
                            {{ $teacher->name }}
                        </p>
                        <p style="font-size:10.5px;color:var(--khas-muted);margin:0">
                            Re: {{ $child->name }}
                        </p>
                    </div>
                </a>
                @endif
            @empty
            <p style="font-size:12px;color:var(--khas-muted);padding:16px">
                No teachers linked yet.
            </p>
            @endforelse
        </div>
    </div>

    {{-- Right: thread --}}
    <div class="msg-right">
        @if($withTeacher && $activeTeacher)

        <div class="msg-right-hd">
            <div class="conv-av" style="background:var(--khas-blue)">
                {{ strtoupper(substr($activeTeacher->name, 0, 1)) }}
            </div>
            <div>
                <p style="font-size:13px;font-weight:600;margin:0">
                    {{ $activeTeacher->name }}
                </p>
                <p style="font-size:10.5px;color:var(--khas-muted);margin:0">
                    Teacher &nbsp;&middot;&nbsp; {{ $activeStudent?->name }}
                </p>
            </div>
        </div>

        <div class="msg-thread" id="msg-thread">
            @forelse($thread as $msg)
            @php
                $isMe     = $msg->sender_id === auth()->id();
                $msgDate  = $msg->created_at->toDateString();
                $today    = now()->toDateString();
                $yesterday= now()->subDay()->toDateString();
                $label    = match(true) {
                    $msgDate === $today      => 'Today',
                    $msgDate === $yesterday  => 'Yesterday',
                    default                  => $msg->created_at->format('d F Y'),
                };
            @endphp

            @if($loop->first || $msg->created_at->toDateString() !== $thread[$loop->index - 1]->created_at->toDateString())
            <div class="msg-date-div">
                <span class="msg-date-label">{{ $label }}</span>
            </div>
            @endif

            @php $isSystem = Str::startsWith($msg->content, ['📋', '🚨']); @endphp

            <div class="{{ $isSystem ? 'me-row' : ($isMe ? 'me-row' : 'them-row') }}">
                <div class="bubble {{ $isSystem ? 'system' : ($isMe ? 'me' : 'them') }}">
                    {{ $msg->content }}
                </div>
                <div class="msg-ts">
                    {{ $msg->created_at->format('g:i a') }}
                </div>
            </div>
            @empty
            <div style="flex:1;display:flex;align-items:center;justify-content:center;
                        flex-direction:column;gap:10px;padding:40px 0">
                <i class="fa-solid fa-comments"
                   style="font-size:40px;color:rgba(0,0,0,0.15)"></i>
                <p style="font-size:13px;color:#888">No messages yet. Say hello!</p>
            </div>
            @endforelse
        </div>

        <div class="msg-input-area">
            <form method="POST" action="{{ route('parent.messages.store') }}"
                  class="msg-input-row">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $withTeacher }}">
                <input type="hidden" name="student_id"  value="{{ $withStudent }}">
                <input type="text"
                       name="content"
                       class="msg-input-field"
                       placeholder="Type a message to the teacher..."
                       required autocomplete="off">
                <button type="submit" class="msg-send-btn">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

        @else
        <div style="flex:1;display:flex;align-items:center;justify-content:center;
                    flex-direction:column;gap:10px">
            <i class="fa-solid fa-comments"
               style="font-size:48px;color:rgba(0,0,0,0.15)"></i>
            <p style="font-size:14px;color:var(--khas-muted);text-align:center">
                Select a teacher to start messaging
            </p>
        </div>
        @endif
    </div>

</div>
</div>
@endsection
