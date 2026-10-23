<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In · TWA Reports</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="container" style="max-width: 440px;">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-3">
                <i data-lucide="shield-check" style="width: 36px; height: 36px;"></i>
            </div>
            <h1 class="h3 fw-bold mb-1">TWA Reports</h1>
            <p class="text-secondary small">Access Control & Role Management Platform</p>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <h2 class="h5 fw-bold mb-3 text-center">Sign in to your account</h2>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="d-flex flex-column gap-3">
                    @csrf

                    <div>
                        <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent text-secondary border-end-0">
                                <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="name@twareports.test">
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label small fw-semibold text-secondary mb-0">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent text-secondary border-end-0">
                                <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                            </span>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="switch-field">
                        <input class="switch-field-input" type="checkbox" name="remember" id="remember" role="switch" {{ old('remember') ? 'checked' : '' }}>
                        <label class="switch-field-control" for="remember">
                            <span class="switch-field-thumb"></span>
                        </label>
                        <label class="switch-field-label small text-secondary" for="remember">
                            Remember me on this device
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-3 mt-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <span>Sign In</span>
                        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-secondary small mb-0">Demo Accounts: <code>superadmin@twareports.test</code> / <code>password</code></p>
        </div>
    </div>
</body>
</html>
