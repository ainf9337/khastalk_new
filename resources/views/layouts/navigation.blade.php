@php
$user = Auth::user();
$initial = strtoupper(substr($user->name ?? 'U', 0, 1));
$firstName = explode(' ', $user->name ?? '')[0];

$roleLabels = [
    'admin'            => 'Admin',
    'teacher'          => 'Teacher',
    'parent'           => 'Parent',
    'senior-assistant' => 'Senior Assistant',
];


@endphp

<!-- Dynamic Logo pointing to dashboard -->
<a href="{{ route('dashboard') }}" class="khas-nav-logo-wrap">
    <img src="{{ asset('assets/img/logo.png') }}"
         alt="KHAS-Talk"
         class="khas-nav-logo-img"
         style="height: 40px; display: block;"> <!-- Scaled to fit nav bar proportionally -->
</a>

<div class="khas-nav-spacer"></div>

<!-- Emergency button — visible to teachers only -->
@if ($user->role === 'teacher')
<a href="{{ url('/teacher/emergency') }}" class="khas-emg-btn">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span>Emergency</span>
</a>
@endif

<!-- Profile dropdown containing user identity and role mapping -->
<div class="khas-profile-wrapper">
    <button class="khas-profile-btn"
            onclick="this.setAttribute('aria-expanded', this.nextElementSibling.classList.toggle('show'))">
        <div class="khas-nav-avatar">{{ $initial }}</div>
        <span class="khas-profile-name">{{ htmlspecialchars($firstName) }}</span>
        <i class="fa-solid fa-chevron-down khas-profile-chevron"></i>
    </button>

    <div class="khas-dropdown">
        <div class="khas-dropdown-header">
            <p>{{ htmlspecialchars($user->name) }}</p>
            <p>{{ $roleLabels[$user->role] ?? 'User' }}</p>
        </div>

        <a href="{{ url('/profile') }}">
            <i class="fa-solid fa-user-pen"></i> My Profile
        </a>

        <!-- Form for secure Laravel CSRF POST-based logout -->
        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
            @csrf
        </form>
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="dd-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</div>
