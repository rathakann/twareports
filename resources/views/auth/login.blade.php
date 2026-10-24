<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TWA Reports - Secure Enterprise Authentication">
    <title>Sign In · TWA Reports</title>
    <script>
        (function () {
            try {
                const storedMode = localStorage.getItem('tc_mode') || 'light';
                document.documentElement.setAttribute('data-theme', storedMode);
                document.documentElement.setAttribute('data-bs-theme', storedMode);
                const storedLang = localStorage.getItem('twa_lang') || '{{ app()->getLocale() }}';
                document.documentElement.setAttribute('data-lang', storedLang);
                document.documentElement.setAttribute('lang', storedLang);
                if (storedLang === 'km') {
                    document.documentElement.style.setProperty('--font', "'Nokora', 'Noto Sans Khmer', sans-serif");
                    document.documentElement.style.setProperty('--font-kh', "'Nokora', 'Noto Sans Khmer', sans-serif");
                    document.documentElement.style.setProperty('--bs-body-font-family', "'Nokora', 'Noto Sans Khmer', sans-serif");
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-viewport">
    <!-- Ambient Backdrop Glows -->
    <div class="auth-ambient-glow auth-glow-primary"></div>
    <div class="auth-ambient-glow auth-glow-secondary"></div>

    <!-- Auth Top Bar -->
    <header class="auth-topbar d-flex align-items-center justify-content-between">
        <a href="{{ route('login') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <span class="auth-logo-badge" style="width: 36px; height: 36px; border-radius: 10px;">
                <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
            </span>
            <span class="fw-bold text-dark fs-6" style="color: var(--tx1) !important;">TWA Reports</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <!-- Language Switcher Pill -->
            <div class="btn-group btn-group-sm rounded-pill p-1 border" style="background-color: var(--bg-card); border-color: var(--bdr) !important;">
                <a href="{{ route('locale.switch', 'en') }}" class="btn btn-sm rounded-pill px-2.5 py-0.5 fw-semibold {{ app()->getLocale() === 'en' ? 'btn-primary' : 'text-secondary' }}" style="font-size: 11px;">
                    EN
                </a>
                <a href="{{ route('locale.switch', 'km') }}" class="btn btn-sm rounded-pill px-2.5 py-0.5 fw-semibold {{ app()->getLocale() === 'km' ? 'btn-primary' : 'text-secondary' }}" style="font-size: 11px;">
                    KM
                </a>
            </div>

            <!-- Theme Toggle Button -->
            <button type="button" id="authThemeToggleBtn" class="btn btn-sm rounded-circle p-2 border d-flex align-items-center justify-content-center"
                style="background-color: var(--bg-card); border-color: var(--bdr) !important; color: var(--tx2); width: 34px; height: 34px;"
                title="Toggle Light / Dark mode" aria-label="Toggle theme">
                <i data-lucide="moon" id="authThemeMoonIcon" style="width: 16px; height: 16px;"></i>
                <i data-lucide="sun" id="authThemeSunIcon" class="d-none" style="width: 16px; height: 16px;"></i>
            </button>
        </div>
    </header>

    <!-- Main Auth Content -->
    <main class="auth-card-container my-auto py-4 px-3">
        <div class="auth-card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex mb-3">
                    <span class="auth-logo-badge">
                        <i data-lucide="shield-check" style="width: 28px; height: 28px;"></i>
                    </span>
                </div>
                <h1 class="h4 fw-bold mb-1" style="color: var(--tx1);">Welcome back</h1>
                <p class="text-secondary small mb-0">Sign in with your credentials to access your dashboard</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2.5 px-3 small rounded-3 mb-3 d-flex align-items-start gap-2 border-0 bg-danger bg-opacity-10 text-danger">
                    <i data-lucide="alert-circle" style="width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px;"></i>
                    <div class="flex-grow-1">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success py-2.5 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2 border-0 bg-success bg-opacity-10 text-success">
                    <i data-lucide="check-circle" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" class="d-flex flex-column gap-3" novalidate>
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="form-label small fw-semibold text-secondary mb-1.5">Email Address</label>
                    <div class="auth-input-group">
                        <span class="auth-input-icon">
                            <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="auth-input @error('email') is-invalid @enderror" placeholder="name@twareports.test">
                    </div>
                </div>

                <!-- Password Input with Toggle -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <label for="password" class="form-label small fw-semibold text-secondary mb-0">Password</label>
                    </div>
                    <div class="auth-input-group">
                        <span class="auth-input-icon">
                            <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="auth-input @error('password') is-invalid @enderror" placeholder="••••••••">
                        <button type="button" class="auth-password-toggle" id="togglePasswordBtn" title="Toggle password visibility" aria-label="Toggle password visibility">
                            <i data-lucide="eye" id="togglePasswordIcon" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Switch -->
                <div class="switch-field pt-1">
                    <input class="switch-field-input" type="checkbox" name="remember" id="remember" role="switch" {{ old('remember') ? 'checked' : '' }}>
                    <label class="switch-field-control" for="remember">
                        <span class="switch-field-thumb"></span>
                    </label>
                    <label class="switch-field-label small text-secondary" for="remember">
                        Remember me on this device
                    </label>
                </div>

                <!-- Submit Button with Loading State -->
                <button type="submit" id="loginSubmitBtn" class="btn btn-primary btn-auth-submit w-100 d-flex align-items-center justify-content-center gap-2 mt-2 shadow-sm">
                    <span id="loginBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span id="loginBtnText">Sign In</span>
                    <i data-lucide="arrow-right" id="loginBtnIcon" style="width: 18px; height: 18px;"></i>
                </button>
            </form>

            <!-- Quick-fill Demo Accounts -->
            <div class="mt-4 pt-3 border-top" style="border-color: var(--bdr) !important;">
                <p class="text-secondary small fw-semibold mb-2 text-center" style="font-size: 11.5px; letter-spacing: 0.3px;">
                    QUICK DEMO CREDENTIALS
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                    <button type="button" class="auth-demo-chip" onclick="fillCredentials('superadmin@twareports.test', 'password')">
                        <i data-lucide="crown" style="width: 14px; height: 14px; color: #f59e0b;"></i>
                        <span>Super Admin</span>
                    </button>
                    <button type="button" class="auth-demo-chip" onclick="fillCredentials('admin@twareports.test', 'password')">
                        <i data-lucide="user-check" style="width: 14px; height: 14px; color: #10b981;"></i>
                        <span>Admin</span>
                    </button>
                </div>
                <p class="text-secondary text-center small mb-0" style="font-size: 11.5px; color: var(--tx3) !important;">
                    &copy; {{ date('Y') }} TWA Reports. All rights reserved.
                </p>
            </div>
        </div>
    </main>

    <!-- Interactive Client Scripts -->
    <script>
        // Password Visibility Toggle
        document.getElementById('togglePasswordBtn')?.addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (!passwordInput || !icon) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });

        // Quick Credentials Filler
        window.fillCredentials = function (email, password) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.getElementById('loginSubmitBtn');

            if (emailInput) {
                emailInput.value = email;
                emailInput.classList.add('bg-white');
            }
            if (passwordInput) {
                passwordInput.value = password;
                passwordInput.classList.add('bg-white');
            }
            submitBtn?.focus();
        };

        // Modern Form Submit with Disabled Loading State
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginSubmitBtn');
        const loginBtnSpinner = document.getElementById('loginBtnSpinner');
        const loginBtnText = document.getElementById('loginBtnText');
        const loginBtnIcon = document.getElementById('loginBtnIcon');

        loginForm?.addEventListener('submit', function (e) {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            // Client Validation Check
            if (!emailInput.value.trim() || !passwordInput.value.trim()) {
                if (!emailInput.value.trim()) emailInput.focus();
                else if (!passwordInput.value.trim()) passwordInput.focus();
                return;
            }

            // Set loading state and disable button
            loginBtn.disabled = true;
            loginBtn.classList.add('disabled');
            loginBtnSpinner?.classList.remove('d-none');
            loginBtnIcon?.classList.add('d-none');
            if (loginBtnText) {
                loginBtnText.textContent = 'Signing in...';
            }
        });

        // Theme Toggle Sync for Login Page
        function syncLoginThemeIcons() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const moonIcon = document.getElementById('authThemeMoonIcon');
            const sunIcon = document.getElementById('authThemeSunIcon');
            if (isDark) {
                moonIcon?.classList.add('d-none');
                sunIcon?.classList.remove('d-none');
            } else {
                moonIcon?.classList.remove('d-none');
                sunIcon?.classList.add('d-none');
            }
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        document.getElementById('authThemeToggleBtn')?.addEventListener('click', function () {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('tc_mode', next);
            syncLoginThemeIcons();
        });

        document.addEventListener('DOMContentLoaded', function () {
            syncLoginThemeIcons();
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
