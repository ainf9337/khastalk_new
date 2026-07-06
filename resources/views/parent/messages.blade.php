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

            @php $isSystem = Str::startsWith($msg->content ?? $msg->message ?? '', ['📋', '🚨']); @endphp

            <div class="{{ $isSystem ? 'me-row' : ($isMe ? 'me-row' : 'them-row') }}">
                <div class="bubble {{ $isSystem ? 'system' : ($isMe ? 'me' : 'them') }}">
                    {{ $msg->content ?? $msg->message ?? '' }}
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
                  class="msg-input-row" id="realtime-chat-form">
                @csrf
                <input type="hidden" name="receiver_id" id="chat-receiver-id" value="{{ $withTeacher }}">
                <input type="hidden" name="student_id"  value="{{ $withStudent }}">
                <input type="text"
                       name="content"
                       id="chat-message-input"
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const partnerId = "{{ $withTeacher ?? '' }}";
    const studentId = "{{ $withStudent ?? '' }}";
    const msgThread = document.getElementById('msg-thread');
    const chatForm = document.getElementById('realtime-chat-form');
    const msgInput = document.getElementById('chat-message-input');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!partnerId || !msgThread) return;

    // Scroll directly to the bottom of the conversation on initial boot
    msgThread.scrollTop = msgThread.scrollHeight;

    // Track previously fetched messages count to prevent redrawing unchanged HTML logs
    let lastKnownMessageCount = 0;

    function fetchLiveMessages() {
        fetch(`/ajax/messages/fetch/${partnerId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Only re-render if database registers new message entries
                    if (data.messages.length !== lastKnownMessageCount) {
                        renderLiveMessages(data.messages, data.current_user_id);
                        lastKnownMessageCount = data.messages.length;
                    }
                }
            })
            .catch(err => console.log('Syncing status: idle'));
    }

    function renderLiveMessages(messages, currentUserId) {
        let chatHtml = '';
        let lastDateString = '';

        messages.forEach(msg => {
            const rawMsgText = msg.content || msg.message || '';
            const msgDateObj = new Date(msg.created_at);
            const dateString = msgDateObj.toDateString();

            // Render Date Group dividers dynamically
            if (dateString !== lastDateString) {
                const label = getFriendlyDateLabel(msgDateObj);
                chatHtml += `
                    <div class="msg-date-div">
                        <span class="msg-date-label">${label}</span>
                    </div>
                `;
                lastDateString = dateString;
            }

            const isMe = msg.sender_id == currentUserId;
            const isSystem = rawMsgText.startsWith('📋') || rawMsgText.startsWith('🚨');

            const rowClass = isSystem ? 'me-row' : (isMe ? 'me-row' : 'them-row');
            const bubbleClass = isSystem ? 'system' : (isMe ? 'me' : 'them');
            const formattedTime = msgDateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            chatHtml += `
                <div class="${rowClass}">
                    <div class="bubble ${bubbleClass}">
                        ${escapeHtml(rawMsgText)}
                    </div>
                    <div class="msg-ts">
                        ${formattedTime}
                    </div>
                </div>
            `;
        });

        // Smart Scrolling: Only auto-snap scroll if user is already looking at bottom entries
        const isUserAtBottom = msgThread.scrollTop + msgThread.clientHeight >= msgThread.scrollHeight - 150;
        msgThread.innerHTML = chatHtml;

        if (isUserAtBottom || lastKnownMessageCount === 0) {
            msgThread.scrollTop = msgThread.scrollHeight;
        }
    }

    if (chatForm) {
        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = msgInput.value.trim();
            if (!text) return;

            // Clear input box immediately to preserve professional fast response feel
            msgInput.value = '';

            fetch('/ajax/messages/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    receiver_id: partnerId,
                    student_id: studentId,
                    message: text
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    fetchLiveMessages(); // Refresh UI instantly upon successful transmission
                }
            });
        });
    }

    function getFriendlyDateLabel(dateObj) {
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        if (dateObj.toDateString() === today.toDateString()) {
            return 'Today';
        } else if (dateObj.toDateString() === yesterday.toDateString()) {
            return 'Yesterday';
        } else {
            return dateObj.toLocaleDateString('ms-MY', { day: 'numeric', month: 'long', year: 'numeric' });
        }
    }

    function escapeHtml(text) {
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    // Set polling clock loop to 1.5 seconds (Fast and highly interactive)
    setInterval(fetchLiveMessages, 1500);
});
</script>
@endpush
