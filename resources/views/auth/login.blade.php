<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.sign_in') }} | {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.frontend-head-assets')
    <link rel="stylesheet" href="{{ asset('assets/css/auth/login.css') }}">
</head>
<body class="auth-body">
    <main class="login-shell">
        <section class="login-card card border-0">
            <div class="card-body login-card-body">
                <div class="login-header">
                    <span class="login-badge">{{ __('app.short_name') }}</span>
                    <h1 class="login-title">Sign in</h1>
                    <p class="login-subtitle">Enter your credentials to access the system.</p>
                </div>

                @if(session('status'))
                    <div class="alert alert-success alert-modern login-alert">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-modern login-alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="login-form">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label login-label">Email Address</label>
                        <div class="input-shell">
                            <span class="input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6.75h16A1.25 1.25 0 0 1 21.25 8v8A1.25 1.25 0 0 1 20 17.25H4A1.25 1.25 0 0 1 2.75 16V8A1.25 1.25 0 0 1 4 6.75Z"/>
                                    <path d="m3.5 8 7.7 5.39a1.4 1.4 0 0 0 1.6 0L20.5 8"/>
                                </svg>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control login-input @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="name@example.com"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label login-label">Password</label>
                        <div class="input-shell">
                            <span class="input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7.75 10.25V8a4.25 4.25 0 1 1 8.5 0v2.25"/>
                                    <rect x="4.75" y="10.25" width="14.5" height="10" rx="2.25"/>
                                    <path d="M12 14.25v2"/>
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control login-input login-input-password @error('password') is-invalid @enderror"
                                placeholder="Enter your password"
                                required
                            >
                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle
                                data-password-press-hold
                                data-target="password"
                                aria-label="Press and hold to show password"
                            >
                                <i id="eyeIcon" class="bi bi-eye password-toggle-icon" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="login-options">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn login-button w-100">Sign In</button>
                </form>
            </div>
        </section>
    </main>

    @include('layouts.partials.frontend-body-assets')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-password-press-hold]').forEach(function (button) {
                var targetId = button.getAttribute('data-target');
                var input = targetId ? document.getElementById(targetId) : null;
                var icon = button.querySelector('#eyeIcon');

                if (!input || !icon) {
                    return;
                }

                function showPassword(event) {
                    if (event) {
                        event.preventDefault();
                    }

                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }

                function hidePassword() {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }

                button.addEventListener('mousedown', showPassword);
                button.addEventListener('mouseup', hidePassword);
                button.addEventListener('mouseleave', hidePassword);
                button.addEventListener('touchstart', showPassword, { passive: false });
                button.addEventListener('touchend', hidePassword);
                button.addEventListener('touchcancel', hidePassword);
            });
        });
    </script>
</body>
</html>
