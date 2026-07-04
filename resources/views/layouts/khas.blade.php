<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KHAS-Talk')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/khas.css') }}">
    @stack('styles')
</head>
<body>

<nav class="khas-navbar">
    @php
        $dashRoute = match(auth()->user()->role) {
            'teacher'          => 'teacher.dashboard',
            'parent'           => 'parent.dashboard',
            'admin'            => 'admin.dashboard',
            'senior_assistant' => 'senior.dashboard',
            default            => 'dashboard',
        };
    @endphp

    <a href="{{ route($dashRoute) }}" class="khas-nav-logo-wrap">
        <img src="{{ asset('img/logo.png') }}" alt="KHAS-Talk" class="khas-nav-logo-img">
    </a>

    <span class="khas-nav-school">SK Hicom &nbsp;&middot;&nbsp; PPKI</span>

    <div class="khas-nav-spacer"></div>

    @if(auth()->user()->isTeacher())
    <a href="{{ route('teacher.emergency') }}" class="khas-emg-btn">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span class="d-none-mobile">Emergency</span>
    </a>
    @endif

    <div class="khas-profile-wrapper" x-data="{ open: false }">
        <button class="khas-profile-btn" @click="open = !open">
            <div class="khas-nav-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <span class="khas-profile-name">
                {{ explode(' ', auth()->user()->name)[0] }}
            </span>
            <i class="fa-solid fa-chevron-down khas-profile-chevron"></i>
        </button>

        <div class="khas-dropdown" x-show="open" @click.outside="open = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             style="display:none">
            <div class="khas-dropdown-header">
                <p>{{ auth()->user()->name }}</p>
                <p>{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
            </div>
            <a href="#">
                <i class="fa-solid fa-user-pen"></i> My Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dd-logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

@hasSection('tabs')
<div class="khas-tabs">
    @yield('tabs')
</div>
@endif

@if(session('success'))
<div class="khas-flash khas-flash-success" x-data="{ show: true }" x-show="show">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
    <button @click="show = false"><i class="fa-solid fa-xmark"></i></button>
</div>
@endif

@if(session('error'))
<div class="khas-flash khas-flash-danger" x-data="{ show: true }" x-show="show">
    <i class="fa-solid fa-circle-exclamation"></i>
    {{ session('error') }}
    <button @click="show = false"><i class="fa-solid fa-xmark"></i></button>
</div>
@endif

@if($errors->any())
<div class="khas-flash khas-flash-danger">
    <i class="fa-solid fa-circle-exclamation"></i>
    <ul style="margin:0;padding-left:18px">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<main class="khas-main">
    @yield('content')
</main>

<footer class="khas-footer">
    <div class="khas-footer-logo-wrap">
        <img src="{{ asset('img/logo.png') }}" alt="KHAS-Talk" class="khas-footer-logo-img">
    </div>
    <br>
    <strong>KHAS-Talk</strong> &mdash; Parent-Teacher Communication System for Autism
    <br>&copy; {{ date('Y') }} All rights reserved.
    <br><br>
    Developed by <strong>Nur Ain Farhana Binti Ahmad Saifful</strong>
</footer>

@stack('scripts')
<script>
document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const icon  = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye','fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash','fa-eye');
        }
    });
});
const thread = document.getElementById('msg-thread');
if (thread) thread.scrollTop = thread.scrollHeight;
</script>
</body>
</html>
