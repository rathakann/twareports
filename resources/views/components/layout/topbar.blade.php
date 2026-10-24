<header class="app-topbar">
    <a class="app-brand" href="{{ route('dashboard') }}" aria-label="TWA Reports dashboard">
        <span class="app-brand-mark">T</span><span class="app-brand-name">TWA Reports</span>
    </a>
    <button class="topbar-icon" type="button" id="btnHamburger" data-sidebar-toggle aria-label="Toggle navigation" title="Toggle navigation"><i data-lucide="panel-left"></i></button>
    @php
        $currentOrg = request()->cookie('twa_org') ? urldecode(request()->cookie('twa_org')) : 'NonOil Report';
    @endphp
    <div class="topbar-popover-wrap">
        <button class="topbar-organization btn border-0 bg-transparent p-0 d-inline-flex align-items-center gap-2" type="button" data-panel-toggle="organization-menu" aria-expanded="false" aria-controls="organization-menu" title="Switch Organization">
            <i data-lucide="building-2"></i>
            <span class="topbar-org-label fw-semibold text-body">{{ $currentOrg }}</span>
            <i data-lucide="chevron-down" style="width: 13px; height: 13px;" class="text-secondary"></i>
        </button>
        <div class="topbar-popover shadow-sm rounded-3 p-2" id="organization-menu" hidden style="min-width: 190px;">
            <p class="topbar-popover-title px-2 mb-2">Select Organization</p>
            <button type="button" class="org-switch-item btn btn-sm w-100 text-start d-flex align-items-center justify-content-between p-2 rounded-2 mb-1 {{ $currentOrg === 'NonOil Report' ? 'active' : '' }}" data-org="NonOil Report">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="layers" style="width: 15px; height: 15px;" class="text-primary"></i>
                    <span class="fw-medium">NonOil Report</span>
                </div>
                <i data-lucide="check" class="org-check text-primary" style="width: 14px; height: 14px; {{ $currentOrg === 'NonOil Report' ? '' : 'display: none;' }}"></i>
            </button>
            <button type="button" class="org-switch-item btn btn-sm w-100 text-start d-flex align-items-center justify-content-between p-2 rounded-2 {{ $currentOrg === 'Oil Report' ? 'active' : '' }}" data-org="Oil Report">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="fuel" style="width: 15px; height: 15px;" class="text-warning"></i>
                    <span class="fw-medium">Oil Report</span>
                </div>
                <i data-lucide="check" class="org-check text-primary" style="width: 14px; height: 14px; {{ $currentOrg === 'Oil Report' ? '' : 'display: none;' }}"></i>
            </button>
        </div>
    </div>
    <button class="topbar-search text-start border-0" type="button" data-spotlight-open aria-label="Search reports and pages (Ctrl+K)" title="Quick search (Ctrl+K)">
        <i data-lucide="search"></i>
        <span class="topbar-search-placeholder text-secondary">Search reports, pages, actions...</span>
        <kbd>Ctrl K</kbd>
    </button>
    <div class="topbar-actions">
        <!-- Notifications Popover -->
        <div class="topbar-popover-wrap">
            <button class="topbar-icon position-relative" type="button" data-panel-toggle="notifications-menu" aria-expanded="false" aria-controls="notifications-menu" aria-label="Notifications" title="Notifications">
                <i data-lucide="bell"></i>
                <span class="position-absolute top-1 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" id="notifBadge" style="width: 8px; height: 8px; margin-top: 10px; margin-left: -12px;">
                    <span class="visually-hidden">New notifications</span>
                </span>
            </button>
            <div class="topbar-popover shadow-lg rounded-3 p-0" id="notifications-menu" hidden style="min-width: 320px; max-width: 360px; right: 0;">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-body-tertiary rounded-top-3">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold mb-0 text-body" style="font-size: 14px;">Notifications</h6>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" style="font-size: 11px;" id="notifCountBadge">2 new</span>
                    </div>
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-secondary" style="font-size: 12px;" id="markAllReadBtn">
                        Mark all as read
                    </button>
                </div>
                <div class="notification-list p-2" style="max-height: 320px; overflow-y: auto;">
                    <a href="{{ route('reports.financial.balance-sheet.index') }}" class="notification-item d-flex gap-3 p-2 rounded-2 text-decoration-none text-body hover-bg mb-1 unread">
                        <div class="rounded-circle p-2 bg-primary-subtle text-primary flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i data-lucide="file-spreadsheet" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span class="fw-semibold text-truncate" style="font-size: 13px;">Balance Sheet Ready</span>
                                <small class="text-secondary" style="font-size: 11px;">10m ago</small>
                            </div>
                            <p class="text-secondary text-truncate mb-0" style="font-size: 12px;">Monthly reconciliation completed.</p>
                        </div>
                    </a>
                    <a href="{{ route('users.index') }}" class="notification-item d-flex gap-3 p-2 rounded-2 text-decoration-none text-body hover-bg mb-1 unread">
                        <div class="rounded-circle p-2 bg-success-subtle text-success flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i data-lucide="user-check" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span class="fw-semibold text-truncate" style="font-size: 13px;">Security Audit Log</span>
                                <small class="text-secondary" style="font-size: 11px;">1h ago</small>
                            </div>
                            <p class="text-secondary text-truncate mb-0" style="font-size: 12px;">Active administrator session verified.</p>
                        </div>
                    </a>
                    <div class="notification-item d-flex gap-3 p-2 rounded-2 text-decoration-none text-body hover-bg">
                        <div class="rounded-circle p-2 bg-info-subtle text-info flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i data-lucide="shield-check" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span class="fw-semibold text-truncate" style="font-size: 13px;">System Backup</span>
                                <small class="text-secondary" style="font-size: 11px;">Yesterday</small>
                            </div>
                            <p class="text-secondary text-truncate mb-0" style="font-size: 12px;">Automated database snapshot completed.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
            @php
                $topUser = auth()->user();
                $topName = $topUser->name ?? 'System Administrator';
                $topEmail = $topUser->email ?? 'admin@bizlite.local';
                $topParts = explode(' ', trim($topName));
                $topInitials = count($topParts) >= 2
                    ? strtoupper(substr($topParts[0], 0, 1) . substr(end($topParts), 0, 1))
                    : strtoupper(substr($topName, 0, 2));
                $topRole = $topUser->roles?->first()?->name ?? 'Administrator';
            @endphp

            <div class="dropdown topbar-user-dropdown">
                <button class="topbar-user-pill btn d-inline-flex align-items-center"
                        type="button"
                        id="topbarUserMenu"
                        data-bs-toggle="dropdown"
                        data-bs-display="static"
                        data-bs-auto-close="true"
                        aria-expanded="false"
                        title="{{ $topName }}">
                    <span class="topbar-user-avatar rounded-circle">
                        {{ $topInitials }}
                    </span>
                    <span class="topbar-user-info d-none d-sm-inline-flex flex-column text-start">
                        <span class="topbar-user-name">{{ $topName }}</span>
                        <span class="topbar-user-role">{{ $topRole }}</span>
                    </span>
                    <i data-lucide="chevron-down" class="topbar-user-chevron"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end topbar-user-menu shadow-lg border rounded-3 p-3 mt-1"
                     aria-labelledby="topbarUserMenu"
                     style="min-width: 250px;">
                    <!-- User Header -->
                    <div class="d-flex align-items-center gap-3 mb-2.5">
                        <div class="topbar-menu-avatar flex-shrink-0">
                            {{ $topInitials }}
                        </div>
                        <div class="overflow-hidden">
                            <h6 class="fw-bold mb-0 text-truncate text-body" style="font-size: 15px;">{{ $topName }}</h6>
                            <small class="text-secondary text-truncate d-block" style="font-size: 12px;">{{ $topEmail }}</small>
                        </div>
                    </div>

                    <hr class="my-2 border-secondary-subtle">

                    <!-- Links -->
                    <div class="d-flex flex-column gap-1">
                        <a class="dropdown-item rounded-2 py-2 px-2.5 d-flex align-items-center gap-2.5 text-secondary"
                           href="{{ route('profile.index') }}">
                            <i data-lucide="user" style="width: 17px; height: 17px;"></i>
                            <span class="fw-medium text-body">My Profile</span>
                        </a>
                        <a class="dropdown-item rounded-2 py-2 px-2.5 d-flex align-items-center gap-2.5 text-secondary"
                           href="{{ route('roles.index') }}">
                            <i data-lucide="shield-check" style="width: 17px; height: 17px;"></i>
                            <span class="fw-medium text-body">Account Settings</span>
                        </a>
                        <button type="button"
                                class="dropdown-item rounded-2 py-2 px-2.5 d-flex align-items-center gap-2.5 text-secondary w-100 border-0 bg-transparent text-start"
                                data-theme-drawer-toggle>
                            <i data-lucide="settings" style="width: 17px; height: 17px;"></i>
                            <span class="fw-medium text-body">Settings</span>
                        </button>
                    </div>

                    <hr class="my-2 border-secondary-subtle">

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <button type="submit"
                                class="dropdown-item rounded-2 py-2 px-2.5 d-flex align-items-center gap-2.5 text-danger w-100 border-0 bg-transparent text-start">
                            <i data-lucide="log-out" style="width: 17px; height: 17px;"></i>
                            <span class="fw-medium">Log out</span>
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
