<header class="app-topbar">
    <a class="app-brand" href="{{ route('dashboard') }}" aria-label="TWA Reports dashboard">
        <span class="app-brand-mark">T</span><span class="app-brand-name">TWA Reports</span>
    </a>
    <button class="topbar-icon" type="button" id="btnHamburger" data-sidebar-toggle aria-label="Toggle navigation" title="Toggle navigation"><i data-lucide="panel-left"></i></button>
    <div class="topbar-popover-wrap">
        <button class="topbar-organization btn border-0 bg-transparent p-0 d-inline-flex align-items-center gap-2" type="button" data-panel-toggle="organization-menu" aria-expanded="false" aria-controls="organization-menu" title="Switch Organization">
            <i data-lucide="building-2"></i>
            <span class="topbar-org-label fw-semibold text-dark">NonOil Report</span>
            <i data-lucide="chevron-down" style="width: 13px; height: 13px;" class="text-secondary"></i>
        </button>
        <div class="topbar-popover shadow-sm rounded-3 p-2" id="organization-menu" hidden style="min-width: 190px;">
            <p class="topbar-popover-title px-2 mb-2">Select Organization</p>
            <button type="button" class="org-switch-item btn btn-sm w-100 text-start d-flex align-items-center justify-content-between p-2 rounded-2 mb-1" data-org="NonOil Report">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="layers" style="width: 15px; height: 15px;" class="text-primary"></i>
                    <span class="fw-medium">NonOil Report</span>
                </div>
                <i data-lucide="check" class="org-check text-primary" style="width: 14px; height: 14px;"></i>
            </button>
            <button type="button" class="org-switch-item btn btn-sm w-100 text-start d-flex align-items-center justify-content-between p-2 rounded-2" data-org="Oil Report">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="fuel" style="width: 15px; height: 15px;" class="text-warning"></i>
                    <span class="fw-medium">Oil Report</span>
                </div>
                <i data-lucide="check" class="org-check text-primary" style="width: 14px; height: 14px; display: none;"></i>
            </button>
        </div>
    </div>
    <label class="topbar-search" for="searchInput"><i data-lucide="search"></i><input id="searchInput" type="search" placeholder="Search" disabled aria-describedby="searchPending"><kbd>Ctrl K</kbd></label>
    <span id="searchPending" class="visually-hidden">Search is pending backend integration.</span>
    <div class="topbar-actions">
        <span class="topbar-icon is-pending" aria-label="Notifications pending backend integration" title="Notifications pending"><i data-lucide="bell"></i></span>

        <!-- Language Toggle (Active Stage Only) -->
        <button class="topbar-icon topbar-lang-toggle" type="button" id="languageToggle" aria-label="Toggle Language (English / ភាសាខ្មែរ)" title="Toggle Language (English / ភាសាខ្មែរ)">
            <span class="lang-stage lang-stage-en d-inline-flex align-items-center gap-1">
                <i data-lucide="languages" style="width: 15px; height: 15px;"></i>
                <span class="fw-bold" style="font-size: 11px;">EN</span>
            </span>
            <span class="lang-stage lang-stage-km d-inline-flex align-items-center gap-1" style="display: none;">
                <i data-lucide="languages" style="width: 15px; height: 15px;"></i>
                <span class="fw-bold" style="font-family: 'Nokora', sans-serif; font-size: 11px;">ខ្មែរ</span>
            </span>
        </button>

        <button class="topbar-icon" type="button" data-fullscreen-toggle aria-label="Toggle fullscreen" title="Toggle fullscreen"><i data-lucide="maximize"></i></button>

        <!-- Theme Customization Drawer Toggle -->
        <button class="topbar-icon" type="button" data-theme-drawer-toggle aria-expanded="false" aria-controls="themeDrawer" aria-label="Theme Customization" title="Theme Customization">
            <i data-lucide="palette"></i>
        </button>

        <!-- Appearance Toggle (Active Stage Icon Only) -->
        <button class="topbar-icon topbar-appearance-toggle" type="button" id="themeToggleBtn" role="switch" aria-checked="false" aria-label="Toggle Light / Dark mode" title="Toggle Light / Dark mode">
            <i data-lucide="sun" class="theme-icon-sun"></i>
            <i data-lucide="moon" class="theme-icon-moon"></i>
        </button>
        @auth
            <div class="topbar-popover-wrap">
                <button class="topbar-profile btn border-0 bg-transparent p-0 d-inline-flex align-items-center gap-2" type="button" data-panel-toggle="user-profile-menu" aria-expanded="false" aria-controls="user-profile-menu">
                    <div class="avatar rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 12px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-xl-inline fw-semibold text-dark">{{ auth()->user()->name }}</span>
                </button>
                <div class="topbar-popover" id="user-profile-menu" hidden style="min-width: 200px;">
                    <div class="p-2 border-bottom">
                        <div class="fw-semibold text-dark">{{ auth()->user()->name }}</div>
                        <small class="text-secondary">{{ auth()->user()->email }}</small>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="p-1">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger w-100 text-start text-decoration-none d-flex align-items-center gap-2 px-2 py-1">
                            <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                <i data-lucide="log-in" style="width: 14px; height: 14px;"></i>
                <span>Sign In</span>
            </a>
        @endauth
    </div>
</header>
