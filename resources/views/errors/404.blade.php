<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found · {{ config('app.name', 'TWA Reports') }}</title>
    <script>
        (() => {
            const root = document.documentElement;
            const mode = localStorage.getItem('tc_mode') === 'dark' ? 'dark' : 'light';
            const primary = localStorage.getItem('tc_primary') || '#1b4fe0';
            root.setAttribute('data-theme', mode);
            root.setAttribute('data-bs-theme', mode);
            root.style.colorScheme = mode;
            root.style.setProperty('--pri', primary);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3" style="background-color: var(--bg-page, #f4f6fb);">
    <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5" style="max-width: 520px; width: 100%; background-color: var(--bg-card, #ffffff);">
        <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle mx-auto" style="width: 88px; height: 88px; background-color: var(--pri-soft, #eef2ff); color: var(--pri, #1b4fe0);">
            <i data-lucide="file-question" style="width: 44px; height: 44px;"></i>
        </div>
        <h1 class="display-6 fw-bold mb-2 text-dark">404</h1>
        <h2 class="h5 fw-semibold mb-2 text-dark">Page Not Found</h2>
        <p class="text-secondary small mb-4">
            The report, page, or resource you are looking for does not exist or may have been moved.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('dashboard') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 shadow-sm">
                <i data-lucide="house" style="width: 16px; height: 16px;"></i>
                <span>Return to Dashboard</span>
            </a>
            <button type="button" onclick="history.back()" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Go Back</span>
            </button>
        </div>
    </div>
</body>
</html>
