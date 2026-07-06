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

    <!-- 🎨 Modern CSS overrides to fix tiny logos and footer contrast -->
    <style>
        /* Force perfect proportions for the header logo */
        .khas-nav-logo-img {
            height: 42px !important;
            width: auto !important;
            display: block !important;
        }

        /* Force perfect proportions for the footer logo */
        .khas-footer-logo-img {
            height: 48px !important;
            width: auto !important;
            display: inline-block !important;
        }

        /* Modern High-Contrast Slate Footer */
        .khas-footer {
            background-color: #0f172a !important; /* Deep Slate Blue */
            color: #94a3b8 !important;            /* Clean light grey text */
            padding: 40px 20px !important;
            text-align: center !important;
            border-top: 1px solid #1e293b !important;
            font-size: 13px !important;
            line-height: 1.6 !important;
        }

        .khas-footer strong {
            color: #f8fafc !important; /* Highlight key branding elements in off-white */
        }

        /* Vibrant high-contrast developer name */
        .khas-dev-name {
            color: #38bdf8 !important; /* Bright sky-blue to pop out legibly */
            font-weight: 700 !important;
        }

        /* Keyframes for emergency banner animations */
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>

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

            <a href="{{ route('profile.edit') }}">
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
    Developed by <span class="khas-dev-name">Nur Ain Farhana Binti Ahmad Saifful</span>
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

    // 🚨 --- Global Real-Time Emergency Polling System ---
    function checkActiveEmergency() {
        fetch('/ajax/emergency/check')
            .then(response => response.json())
            .then(data => {
                if (data.has_emergency) {
                    showGlobalEmergencyAlert(data.alert, data.student_name);
                } else {
                    hideGlobalEmergencyAlert();
                }
            })
            .catch(err => console.log('Emergency sync idle'));
    }

    function showGlobalEmergencyAlert(alert, studentName) {
        let alertBox = document.getElementById('global-emergency-banner');
        if (!alertBox) {
            alertBox = document.createElement('div');
            alertBox.id = 'global-emergency-banner';
            alertBox.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                background: #ef4444;
                color: white;
                padding: 18px 24px;
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 15px rgba(0,0,0,0.25);
                font-family: 'Poppins', sans-serif;
                box-sizing: border-box;
                animation: slideDown 0.4s ease;
            `;
            document.body.appendChild(alertBox);
        }

        alertBox.innerHTML = `
            <div style="display:flex; align-items:center; gap:16px;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size:24px; animation: pulse 1s infinite; color: #fff;"></i>
                <div>
                    <strong style="font-size:16px; letter-spacing: 0.5px;">AMARAN KECEMASAN: ${studentName}</strong>
                    <p style="margin:4px 0 0 0; font-size:13.5px; opacity:0.95;">${alert.message}</p>
                </div>
            </div>
            <div>
                <button onclick="acknowledgeEmergency(${alert.id})" style="background: white; color: #ef4444; border: none; padding: 10px 20px; font-weight: 700; border-radius: 6px; cursor: pointer; font-size:13px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.1s;">
                    SAHKAN TERIMA BANCIAN
                </button>
            </div>
        `;
    }

    function hideGlobalEmergencyAlert() {
        const alertBox = document.getElementById('global-emergency-banner');
        if (alertBox) alertBox.remove();
    }

    // Globally declare acknowledge action without library reliance
    window.acknowledgeEmergency = function(alertId) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(`/ajax/emergency/${alertId}/acknowledge`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                hideGlobalEmergencyAlert();
            }
        });
    }

    // Poll the server for active emergencies every 2.5 seconds
    setInterval(checkActiveEmergency, 2500);
    checkActiveEmergency(); // Check immediately on layout load
});
</script>
</body>
</html>
