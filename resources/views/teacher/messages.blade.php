@extends('layouts.khas')
@section('title', 'Messages')

@section('tabs')
    <a href="{{ route('teacher.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('teacher.students') }}" class="khas-tab">
        <i class="fa-solid fa-users"></i> Students
    </a>
    <a href="{{ route('teacher.rpi') }}" class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI / IEP
    </a>
    <a href="{{ route('teacher.messages') }}" class="khas-tab active">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadTotal > 0)
            <span class="notif-dot">{{ $unreadTotal }}</span>
        @endif
    </a>
    <a href="{{ route('teacher.reports') }}" class="khas-tab">
        <i class="fa-solid fa-chart-bar"></i> Reports
    </a>
@endsection

@section('content')
<div class="khas-page" style="padding-bottom:0">
<div class="msg-wrap">

    {{-- Left: conversation list --}}
    <div class="msg-left">
        <div class="msg-left-hd">
            <p style="font-size:13.5px;font-weight:600;margin:0">Messages</p>
        </div>
        <div class="msg-list">
            @forelse($conversations as $conv)
            @php
                $isActive = ($conv['other_id'] == $withParent &&
                             $conv['student_id'] == $withStudent);
            @endphp
            <a href="{{ route('teacher.messages', [
                    'parent_id'  => $conv['other_id'],
                    'student_id' => $conv['student_id'],
                ]) }}"
               class="conv-item {{ $isActive ? 'active' : '' }}">
                <div class="conv-av"
                     style="background:{{ $isActive ? 'var(--khas-blue)' : 'var(--khas-green)' }}">
                    {{ strtoupper(substr($conv['other_name'], 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="font-size:12px;font-weight:600;color:var(--khas-text)">
                            {{ $conv['other_name'] }}
                        </span>
                        @if($conv['unread'] > 0)
                        <span style="background:var(--khas-blue);color:#fff;font-size:9px;
                                     font-weight:700;border-radius:10px;padding:1px 6px">
                            {{ $conv['unread'] }}
                        </span>
                        @endif
                    </div>
                    <p style="font-size:10.5px;color:var(--khas-muted);margin:2px 0 0">
                        {{ $conv['student_name'] }}
                    </p>
                    <p style="font-size:10.5px;color:var(--khas-muted);margin:2px 0 0;
                               white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px">
                        {{ Str::limit($conv['last_message'], 40) }}
                    </p>
                </div>
            </a>
            @empty
            <p style="font-size:12px;color:var(--khas-muted);padding:16px">
                No conversations yet.
            </p>
            @endforelse
        </div>
    </div>

    {{-- Right: thread --}}
    <div class="msg-right">
        @if($withParent && $activeParent)

        <div class="msg-right-hd">
            <div class="conv-av" style="background:var(--khas-green)">
                {{ strtoupper(substr($activeParent->name, 0, 1)) }}
            </div>
            <div>
                <p style="font-size:13px;font-weight:600;margin:0">
                    {{ $activeParent->name }}
                </p>
                <p style="font-size:10.5px;color:var(--khas-muted);margin:0">
                    Parent &nbsp;&middot;&nbsp; {{ $activeStudent?->name }}
                </p>
            </div>
            <div style="flex:1"></div>
            <span style="font-size:10.5px;background:var(--khas-blue-light);
                         color:var(--khas-blue);padding:3px 10px;border-radius:10px;font-weight:500">
                {{ $activeStudent?->name }}
            </span>
        </div>

        {{-- Messages thread --}}
        <div class="msg-thread" id="msg-thread">
            @forelse($thread as $msg)
            @php
                $isMe     = $msg->sender_id === auth()->id();
                $msgDate  = $msg->created_at->toDateString();
                $today    = now()->toDateString();
                $yesterday= now()->subDay()->toDateString();
                $label    = match(true) {
                    $msgDate === $today     => 'Today',
                    $msgDate === $yesterday => 'Yesterday',
                    default                 => $msg->created_at->format('d F Y'),
                };
            @endphp

            @if($loop->first || $msg->created_at->toDateString() !== $thread[$loop->index - 1]->created_at->toDateString())
            <div class="msg-date-div">
                <span class="msg-date-label">{{ $label }}</span>
            </div>
            @endif

            @php
                $isSystem = Str::startsWith($msg->content, ['📋', '🚨']);
            @endphp

            <div class="{{ $isSystem ? 'me-row' : ($isMe ? 'me-row' : 'them-row') }}">
                <div class="bubble {{ $isSystem ? 'system' : ($isMe ? 'me' : 'them') }}">
                    {{ $msg->content }}
                </div>
                <div class="msg-ts">
                    {{ $msg->created_at->format('g:i a') }}
                    @if($isMe)
                        @if($msg->is_read)
                            <i class="fa-solid fa-check-double"
                               style="font-size:10px;color:var(--khas-blue)"></i>
                        @else
                            <i class="fa-solid fa-check"
                               style="font-size:10px;color:#999"></i>
                        @endif
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align:center;margin:auto;padding:40px 0">
                <i class="fa-solid fa-comments"
                   style="font-size:40px;color:rgba(0,0,0,0.15)"></i>
                <p style="font-size:13px;color:#888;margin-top:10px">No messages yet. Say hello!</p>
            </div>
            @endforelse
        </div>

        {{-- Input area --}}
        <div class="msg-input-area">
            <div class="tpl-chips">
                <span style="font-size:10.5px;color:#888;flex-shrink:0">Quick:</span>
                <button class="tpl-chip"
                        onclick="document.getElementById('msgInput').value='Good progress today 👍'">
                    Good progress today 👍
                </button>
                <button class="tpl-chip"
                        onclick="document.getElementById('msgInput').value='Please check the behaviour log'">
                    Check the log
                </button>
                <button class="tpl-chip"
                        onclick="document.getElementById('msgInput').value='Can we schedule a meeting?'">
                    Schedule a meeting?
                </button>
            </div>
            <form method="POST" action="{{ route('teacher.messages.store') }}"
                  class="msg-input-row">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $withParent }}">
                <input type="hidden" name="student_id"  value="{{ $withStudent }}">
                <input type="text"
                       name="content"
                       id="msgInput"
                       class="msg-input-field"
                       placeholder="Type a message..."
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
                Select a conversation on the left<br>to start messaging
            </p>
        </div>
        @endif
    </div>

</div>
</div>
@endsection
