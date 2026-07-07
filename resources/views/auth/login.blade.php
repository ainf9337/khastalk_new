<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk — KHAS-Talk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/khas.css') }}">
</head>
<body class="khas-login-body">

<div class="khas-login-wrap">

    {{-- Left branding panel --}}
    <div class="khas-left">
        <div class="khas-brand">

            <div class="khas-login-logo-wrap">
                <img src="{{ asset('img/logo.png') }}"
                     alt="KHAS-Talk"
                     class="khas-login-logo-img">
            </div>

            <div class="khas-tagline">
                Sistem Komunikasi Ibu Bapa &ndash; Guru<br>
                Parent-Teacher Communication System
            </div>

            <div class="khas-badge">
                <span class="khas-dot"></span>
                Pendidikan Khas &nbsp;&middot;&nbsp; Autisme
            </div>

        </div>
    </div>

    {{-- Right form panel --}}
    <div class="khas-right">
        <div class="khas-form-card">

            <h2 class="khas-form-title">
                <i class="fa-solid fa-right-to-bracket"
                   style="color:var(--khas-blue);font-size:20px"></i>
                &nbsp;Log Masuk
            </h2>
            <p class="khas-form-sub">Please fill in your details below</p>

            {{-- Errors --}}
            @if($errors->any())
            <div class="khas-alert khas-alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Session status (e.g. after password reset) --}}
            @if(session('status'))
            <div class="khas-alert khas-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <label class="khas-label" for="email">
                    <i class="fa-solid fa-envelope"
                       style="color:var(--khas-muted);font-size:12px"></i>
                    &nbsp;Emel
                </label>
                <input class="khas-input"
                       type="email"
                       id="email"
                       name="email"
                       placeholder="cikgu@khastalk.com"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="username">

                {{-- Password --}}
                <label class="khas-label" for="password">
                    <i class="fa-solid fa-lock"
                       style="color:var(--khas-muted);font-size:12px"></i>
                    &nbsp;Kata Laluan
                </label>
                <div class="pw-wrapper">
                    <input class="khas-input"
                           type="password"
                           id="password"
                           name="password"
                           placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                           required
                           autocomplete="current-password">
                    <button type="button" class="pw-toggle" title="Show/hide password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                {{-- Remember me --}}
                <div style="display:flex;align-items:center;
                            justify-content:space-between;margin-bottom:18px">
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       style="font-size:12.5px;color:var(--khas-muted);
                              text-decoration:none;transition:color 0.15s"
                       onmouseover="this.style.color='var(--khas-blue)'"
                       onmouseout="this.style.color='var(--khas-muted)'">
                        <i class="fa-solid fa-key" style="font-size:11px"></i>
                        &nbsp;Lupa kata laluan?
                    </a>
                    @endif
                </div>

                <button type="submit" class="khas-btn khas-btn-primary">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Log Masuk
                </button>

            </form>

        </div>
    </div>

</div>

<script>
// Password show/hide toggle
document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', function () {
        const input = this.previousElementSibling;
        const icon  = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});
</script>
</body>
</html>
