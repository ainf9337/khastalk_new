@extends('layouts.khas')

@section('title', 'Kemas Kini Profil - KHAS-Talk')

@section('content')
<style>
    :root {
        --khas-blue: #1e468a;
        --khas-blue-dark: #1e3a8a;
        --khas-border: #e5e7eb;
        --khas-muted: #6b7280;
        --khas-text: #1f2937;
        --khas-bg: #f9fafb;
    }

    .profile-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .profile-header-block {
        margin-bottom: 28px;
    }

    .profile-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--khas-text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-subtitle {
        font-size: 13.5px;
        color: var(--khas-muted);
        margin-top: 4px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .profile-card {
        background: #fff;
        border: 1px solid var(--khas-border);
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .profile-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--khas-border);
        background: var(--khas-bg);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--khas-text);
        margin: 0;
    }

    .profile-card-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--khas-text);
        margin-bottom: 6px;
    }

    .form-control-wrap {
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        font-size: 14px;
        border: 1.5px solid var(--khas-border);
        border-radius: 6px;
        color: var(--khas-text);
        background: #fff;
        transition: border-color 0.15s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--khas-blue);
    }

    .form-control-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--khas-muted);
        cursor: pointer;
    }

    .form-btn-submit {
        background: var(--khas-blue);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .form-btn-submit:hover {
        background: var(--khas-blue-dark);
    }
</style>

<div class="profile-container">

    <div class="profile-header-block">
        <h2 class="profile-title">
            <i class="fa-solid fa-user-gear" style="color: var(--khas-blue);"></i> My Profile
        </h2>
        <p class="profile-subtitle">Update your personal information and change your system account password.</p>
    </div>

    <div class="profile-grid">
        <!-- Card 1: Account Information -->
        <div class="profile-card">
            <div class="profile-card-header">
                <i class="fa-regular fa-address-card" style="color: var(--khas-blue);"></i>
                <h3 class="profile-card-title">Maklumat Peribadi</h3>
            </div>
            <div class="profile-card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label class="form-label" for="name">Nama Penuh</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required autocomplete="name">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Alamat Emel</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">No. Telefon</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $userPhone) }}" placeholder="e.g. 0123456789">
                    </div>

                    <div style="margin-top: 24px;">
                        <button type="submit" class="form-btn-submit">
                            <i class="fa-regular fa-floppy-disk"></i> Simpan Maklumat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 2: Update Password -->
        <div class="profile-card">
            <div class="profile-card-header">
                <i class="fa-solid fa-key" style="color: var(--khas-blue);"></i>
                <h3 class="profile-card-title">Kemas Kini Kata Laluan</h3>
            </div>
            <div class="profile-card-body">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label class="form-label" for="update_password_current_password">Kata Laluan Semasa</label>
                        <div class="form-control-wrap">
                            <input type="password" name="current_password" id="update_password_current_password" class="form-control" required autocomplete="current-password">
                            <span class="form-control-icon pw-toggle"><i class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="update_password_password">Kata Laluan Baharu</label>
                        <div class="form-control-wrap">
                            <input type="password" name="password" id="update_password_password" class="form-control" required autocomplete="new-password">
                            <span class="form-control-icon pw-toggle"><i class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="update_password_password_confirmation">Sahkan Kata Laluan Baharu</label>
                        <div class="form-control-wrap">
                            <input type="password" name="password_confirmation" id="update_password_password_confirmation" class="form-control" required autocomplete="new-password">
                            <span class="form-control-icon pw-toggle"><i class="fa-regular fa-eye"></i></span>
                        </div>
                    </div>

                    <div style="margin-top: 24px;">
                        <button type="submit" class="form-btn-submit">
                            <i class="fa-solid fa-shield-halved"></i> Tukar Kata Laluan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
