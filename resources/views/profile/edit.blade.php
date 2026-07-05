@extends('layouts.khas')
@section('title', 'My Profile')

@section('content')
<div class="khas-page" style="max-width:680px;margin:0 auto">

    {{-- Back link --}}
    @php
        $dashRoute = match(auth()->user()->role) {
            'teacher'          => 'teacher.dashboard',
            'parent'           => 'parent.dashboard',
            'admin'            => 'admin.dashboard',
            'senior_assistant' => 'senior.dashboard',
            default            => 'dashboard',
        };
    @endphp
    <a href="{{ route($dashRoute) }}"
       style="font-size:12.5px;color:var(--khas-muted);text-decoration:none;
              display:inline-flex;align-items:center;gap:6px;margin-bottom:16px">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>

    {{-- Profile header card --}}
    <div class="khas-card"
         style="display:flex;align-items:center;gap:20px;
                flex-wrap:wrap;margin-bottom:16px">
        <div style="width:72px;height:72px;border-radius:50%;
                    background:var(--khas-blue);display:flex;align-items:center;
                    justify-content:center;font-size:28px;font-weight:700;
                    color:#fff;flex-shrink:0">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div>
            <h5 style="font-weight:700;font-size:18px;margin-bottom:4px">
                {{ auth()->user()->name }}
            </h5>
            <span style="background:var(--khas-blue-light);color:var(--khas-blue);
                         font-size:12px;font-weight:600;padding:3px 12px;border-radius:10px">
                <i class="fa-solid fa-id-badge"></i>
                &nbsp;{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
            </span>
            <p style="font-size:12.5px;color:var(--khas-muted);margin-top:8px">
                <i class="fa-solid fa-envelope" style="font-size:11px"></i>
                &nbsp;{{ auth()->user()->email }}
                &nbsp;&nbsp;
                <i class="fa-solid fa-phone" style="font-size:11px"></i>
                &nbsp;{{ auth()->user()->phone ?? 'Not set' }}
            </p>
        </div>
    </div>

    {{-- Edit profile info --}}
    <div class="khas-card" style="margin-bottom:14px">
        <p style="font-size:13.5px;font-weight:600;margin-bottom:16px">
            <i class="fa-solid fa-user-pen" style="color:var(--khas-blue)"></i>
            &nbsp;Edit Profile Information
        </p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label class="khas-label" for="name">Full Name *</label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="khas-input"
                           value="{{ old('name', auth()->user()->name) }}"
                           required>
                </div>
                <div>
                    <label class="khas-label" for="phone">Phone Number</label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           class="khas-input"
                           placeholder="01X-XXXXXXXX"
                           value="{{ old('phone', auth()->user()->phone) }}">
                </div>
            </div>

            <label class="khas-label" for="email">Email Address *</label>
            <input type="email"
                   id="email"
                   name="email"
                   class="khas-input"
                   value="{{ old('email', auth()->user()->email) }}"
                   required>

            @if(session('status') === 'profile-updated')
            <div class="khas-alert khas-alert-success" style="margin-bottom:12px">
                <i class="fa-solid fa-circle-check"></i>
                Profile updated successfully.
            </div>
            @endif

            <button type="submit"
                    style="background:var(--khas-blue);color:#fff;border:none;
                           border-radius:8px;padding:10px 24px;font-size:13px;
                           font-weight:600;cursor:pointer;font-family:Poppins,sans-serif;
                           display:inline-flex;align-items:center;gap:8px;
                           transition:all 0.18s"
                    onmouseover="this.style.background='var(--khas-blue-dark)'"
                    onmouseout="this.style.background='var(--khas-blue)'">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </form>
    </div>

    {{-- Change password --}}
    <div class="khas-card" style="margin-bottom:14px">
        <p style="font-size:13.5px;font-weight:600;margin-bottom:16px">
            <i class="fa-solid fa-lock" style="color:var(--khas-amber)"></i>
            &nbsp;Change Password
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <label class="khas-label" for="current_password">Current Password</label>
            <div class="pw-wrapper">
                <input type="password"
                       id="current_password"
                       name="current_password"
                       class="khas-input"
                       placeholder="Enter current password"
                       autocomplete="current-password">
                <button type="button" class="pw-toggle">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('current_password')
            <p style="font-size:12px;color:var(--khas-red);margin:-8px 0 10px">
                {{ $message }}
            </p>
            @enderror

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label class="khas-label" for="password">New Password</label>
                    <div class="pw-wrapper">
                        <input type="password"
                               id="password"
                               name="password"
                               class="khas-input"
                               placeholder="Min. 8 characters"
                               autocomplete="new-password">
                        <button type="button" class="pw-toggle">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <p style="font-size:12px;color:var(--khas-red);margin:-8px 0 10px">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                <div>
                    <label class="khas-label" for="password_confirmation">
                        Confirm New Password
                    </label>
                    <div class="pw-wrapper">
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="khas-input"
                               placeholder="Repeat new password"
                               autocomplete="new-password">
                        <button type="button" class="pw-toggle">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if(session('status') === 'password-updated')
            <div class="khas-alert khas-alert-success" style="margin-bottom:12px">
                <i class="fa-solid fa-circle-check"></i>
                Password changed successfully.
            </div>
            @endif

            <button type="submit"
                    style="background:var(--khas-amber);color:#fff;border:none;
                           border-radius:8px;padding:10px 24px;font-size:13px;
                           font-weight:600;cursor:pointer;font-family:Poppins,sans-serif;
                           display:inline-flex;align-items:center;gap:8px;
                           transition:all 0.18s"
                    onmouseover="this.style.background='#c47d10'"
                    onmouseout="this.style.background='var(--khas-amber)'">
                <i class="fa-solid fa-key"></i> Update Password
            </button>
        </form>
    </div>

    {{-- Delete account (optional — keep collapsed) --}}
    <details style="margin-bottom:16px">
        <summary style="font-size:12.5px;color:var(--khas-red);cursor:pointer;
                         font-weight:600;list-style:none;display:flex;
                         align-items:center;gap:6px">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Delete Account
        </summary>
        <div class="khas-card"
             style="margin-top:10px;border:1.5px solid var(--khas-red)">
            <p style="font-size:13px;color:var(--khas-muted);margin-bottom:14px">
                Once deleted, all your data will be permanently removed.
                This action cannot be undone.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}"
                  onsubmit="return confirm('Are you absolutely sure? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <label class="khas-label">
                    Confirm your password to proceed
                </label>
                <div class="pw-wrapper">
                    <input type="password"
                           name="password"
                           class="khas-input"
                           placeholder="Enter your password">
                    <button type="button" class="pw-toggle">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <button type="submit"
                        class="khas-btn"
                        style="background:var(--khas-red);color:#fff;
                               border:none;width:auto">
                    <i class="fa-solid fa-trash"></i> Delete My Account
                </button>
            </form>
        </div>
    </details>

</div>
@endsection
