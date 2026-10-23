<x-app-layout :title="config('app.name')">
    <x-slot:topbar>
        <header class="app-topbar">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-sidebar-toggle aria-label="Toggle navigation"><i data-lucide="menu"></i></button>
            <span class="ms-3 fw-semibold">{{ config('app.name') }}</span>
            <div class="ms-auto d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-theme-mode="light" aria-label="Use light theme"><i data-lucide="sun"></i></button>
                <button class="btn btn-sm btn-outline-secondary" type="button" data-theme-mode="dark" aria-label="Use dark theme"><i data-lucide="moon"></i></button>
            </div>
        </header>
    </x-slot:topbar>
    <x-slot:sidebar>
        <p class="small text-uppercase text-muted mb-2">Navigation</p>
        <span class="d-flex align-items-center gap-2 text-muted"><i data-lucide="layout-dashboard"></i><span>Foundation</span></span>
    </x-slot:sidebar>
    <section class="card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <span class="kpi-icon kpi-icon-blue"><i data-lucide="palette"></i></span>
                <div>
                    <h1 class="h5 mb-1">BizLite theme foundation</h1>
                    <p class="mb-0 text-muted">Bootstrap, runtime appearance preferences, and reusable design tokens are ready for application layouts.</p>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
