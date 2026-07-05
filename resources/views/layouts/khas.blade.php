<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KHAS-Talk')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons & Styles -->
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
            'senior-assistant' => 'senior.dashboard',
            default            => 'dashboard',
        };
    @endphp

    <a href="{{ route($dashRoute) }}" class="khas-nav-logo-wrap">
        <img src="{{ asset('img/logo.png') }}" alt="KHAS-Talk" class="khas-nav-logo-img">
    </a>

    <span class="khas-nav-school">SK Hicom &nbsp;&middot;&nbsp; PPKI</span>

    <div class="khas-nav-spacer"></div>

    @if(auth()->user()->role === 'teacher')
    <a href="{{ route('teacher.emergency') }}" class="khas-emg-btn">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span class="d-none-mobile">Emergency</span>
    </a>
    @endif

    <!-- Profile Dropdown Component (Refactored to robust Native JS) -->
    <div class="khas-profile-wrapper">
        <button class="khas-profile-btn" id="khasProfileBtn" type="button">
            <div class="khas-nav-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <span class="khas-profile-name">
                {{ explode(' ', auth()->user()->name)[0] }}
            </span>
            <i class="fa-solid fa-chevron-down khas-profile-chevron"></i>
        </button>

        <div class="khas-dropdown" id="khasDropdown" style="display: none;">
            <div class="khas-dropdown-header">
                <p>{{ auth()->user()->name }}</p>
                <p>{{ ucfirst(str_replace('-', ' ', auth()->user()->role)) }}</p>
            </div>

            <a href="#">
                <i class="fa-solid fa-user-pen"></i> My Profile
            </a>

            <!-- Secure POST Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dd-logout-btn" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 16px; cursor: pointer; display: flex; align-items: center; gap: 8px; color: #ef4444;">
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

<!-- Flash Notifications (Refactored to native JS dismiss buttons) -->
@if(session('success'))
<div class="khas-flash khas-flash-success">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
    <button type="button" class="khas-flash-close-btn"><i class="fa-solid fa-xmark"></i></button>
</div>
@endif

@if(session('error'))
<div class="khas-flash khas-flash-danger">
    <i class="fa-solid fa-circle-exclamation"></i>
    {{ session('error') }}
    <button type="button" class="khas-flash-close-btn"><i class="fa-solid fa-xmark"></i></button>
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
// --- Native Javascript Controller (Zero Conflicts) ---
document.addEventListener('DOMContentLoaded', function () {
    const profileBtn = document.getElementById('khasProfileBtn');
    const dropdownMenu = document.getElementById('khasDropdown');

    // Toggle Profile Dropdown
    if (profileBtn && dropdownMenu) {
        profileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            const isOpen = dropdownMenu.style.display === 'block';
            dropdownMenu.style.display = isOpen ? 'none' : 'block';
            profileBtn.classList.toggle('active', !isOpen);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            if (!profileBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = 'none';
                profileBtn.classList.remove('active');
            }
        });
    }

    // Dismiss Flash Notifications
    document.querySelectorAll('.khas-flash-close-btn').forEach(button => {
        button.addEventListener('click', function() {
            const flashCard = this.closest('.khas-flash');
            if (flashCard) {
                flashCard.style.transition = 'opacity 0.2s ease';
                flashCard.style.opacity = '0';
                setTimeout(() => flashCard.remove(), 200);
            }
        });
    });

    // Toggle Input Passwords
    document.querySelectorAll('.pw-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon  = this.querySelector('i');
            if (input && input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye','fa-eye-slash');
            } else if (input) {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash','fa-eye');
            }
        });
    });

    // Scroll chat thread to bottom
    const thread = document.getElementById('msg-thread');
    if (thread) thread.scrollTop = thread.scrollHeight;
});
</script>
</body>
</html>
