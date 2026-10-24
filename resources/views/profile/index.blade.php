<x-app-layout title="User Profile · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house" class="me-1" style="width: 14px; height: 14px;"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Workspace</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Profile</span>
    </x-slot:breadcrumb>

    <!-- Success Feedback Notification -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 shadow-sm" role="alert">
            <i data-lucide="check-circle-2" style="width: 16px; height: 16px;" class="text-success me-2 flex-shrink-0"></i>
            <span class="small fw-medium">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem;"></button>
        </div>
    @endif

    <!-- Error Validation Notification -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 shadow-sm" role="alert">
            <div class="d-flex align-items-center mb-1 fw-bold small">
                <i data-lucide="alert-triangle" style="width: 16px; height: 16px;" class="text-danger me-2 flex-shrink-0"></i>
                <span>Please resolve the following issues:</span>
            </div>
            <ul class="mb-0 ps-3 fs-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem;"></button>
        </div>
    @endif

    @php
        $activeTab = $activeTab ?? request()->query('tab', session('active_tab', 'personal'));
        if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation')) {
            $activeTab = 'security';
        } elseif ($errors->has('name') || $errors->has('email')) {
            $activeTab = 'personal';
        }

        $words = preg_split('/\s+/', trim($user->name));
        $initials = count($words) >= 2
            ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
            : mb_substr($user->name, 0, 2);
        $initials = strtoupper($initials);
    @endphp

    <!-- Master Profile Card with Mantis Tab Navigation -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        <!-- Top Profile Banner & Identity Header -->
        <div class="p-3 p-md-4 pb-3 border-bottom bg-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <!-- User Identity (Avatar + Name + Badges + Meta) -->
                <div class="d-flex align-items-center gap-3">
                    <div class="position-relative flex-shrink-0">
                        <div class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle shadow-sm"
                             style="width: 56px; height: 56px; font-size: 20px; background: linear-gradient(135deg, #1890ff 0%, #10239e 100%);">
                            {{ $initials }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 border border-2 border-white rounded-circle {{ $user->isActive() ? 'bg-success' : 'bg-secondary' }}"
                              title="{{ $user->isActive() ? 'Active Account' : 'Inactive Account' }}"
                              style="width: 13px; height: 13px;"></span>
                    </div>

                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h1 class="fs-5 fw-bold mb-0 text-dark">{{ $user->name }}</h1>

                            @if ($user->hasRole('Super Admin'))
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fs-xs fw-semibold d-inline-flex align-items-center">
                                    <i data-lucide="shield-check" class="me-1 flex-shrink-0" style="width: 12px; height: 12px;"></i>
                                    <span>Super Admin</span>
                                </span>
                            @elseif ($roles->isNotEmpty())
                                @foreach ($roles as $role)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fs-xs fw-semibold d-inline-flex align-items-center">
                                        <i data-lucide="shield" class="me-1 flex-shrink-0" style="width: 11px; height: 11px;"></i>
                                        <span>{{ $role->name }}</span>
                                    </span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 fs-xs">
                                    Standard User
                                </span>
                            @endif

                            <span class="badge {{ $user->isActive() ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-2 py-0.5 fs-xs fw-semibold d-inline-flex align-items-center">
                                <span class="rounded-circle me-1.5 {{ $user->isActive() ? 'bg-success' : 'bg-warning' }}" style="width: 6px; height: 6px;"></span>
                                <span>{{ ucfirst($user->status ?? 'active') }}</span>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2 text-secondary fs-xs flex-wrap">
                            <span class="d-inline-flex align-items-center">
                                <i data-lucide="mail" class="me-1 text-secondary" style="width: 12px; height: 12px;"></i>
                                <span>{{ $user->email }}</span>
                            </span>
                            <span>•</span>
                            <span>User ID: <strong class="text-dark">#{{ $user->id }}</strong></span>
                            <span>•</span>
                            <span>Joined: <strong class="text-dark">{{ $user->created_at?->format('M d, Y') ?? 'N/A' }}</strong></span>
                            <span>•</span>
                            <span>Updated: <strong class="text-dark">{{ $user->updated_at?->diffForHumans() ?? 'N/A' }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" data-theme-drawer-toggle aria-expanded="false" aria-controls="themeDrawer" title="Theme Customization">
                        <i data-lucide="palette" class="me-1.5 flex-shrink-0" style="width: 14px; height: 14px;"></i>
                        <span>Appearance</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                        <i data-lucide="layout-dashboard" class="me-1.5 flex-shrink-0" style="width: 14px; height: 14px;"></i>
                        <span>Dashboard</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center">
                            <i data-lucide="log-out" class="me-1.5 flex-shrink-0" style="width: 14px; height: 14px;"></i>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mantis Tabs Navigation Bar -->
        <div class="profile-tabs-nav nav bg-body-tertiary px-3 px-md-4" id="profileTabsNav" role="tablist">
            <button class="profile-tab-btn nav-link {{ $activeTab === 'personal' ? 'active' : '' }}"
                    id="tab-personal"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-personal"
                    data-tab-name="personal"
                    type="button"
                    role="tab"
                    aria-controls="pane-personal"
                    aria-selected="{{ $activeTab === 'personal' ? 'true' : 'false' }}">
                <i data-lucide="user" class="me-1.5 flex-shrink-0"></i>
                <span>Personal Information</span>
            </button>

            <button class="profile-tab-btn nav-link {{ $activeTab === 'security' ? 'active' : '' }}"
                    id="tab-security"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-security"
                    data-tab-name="security"
                    type="button"
                    role="tab"
                    aria-controls="pane-security"
                    aria-selected="{{ $activeTab === 'security' ? 'true' : 'false' }}">
                <i data-lucide="lock" class="me-1.5 flex-shrink-0"></i>
                <span>Security &amp; Password</span>
            </button>

            <button class="profile-tab-btn nav-link {{ $activeTab === 'roles' ? 'active' : '' }}"
                    id="tab-roles"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-roles"
                    data-tab-name="roles"
                    type="button"
                    role="tab"
                    aria-controls="pane-roles"
                    aria-selected="{{ $activeTab === 'roles' ? 'true' : 'false' }}">
                <i data-lucide="shield-check" class="me-1.5 flex-shrink-0"></i>
                <span>Roles &amp; Permissions</span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-1.5 py-0.5 fs-xs ms-1">
                    {{ $permissions->count() }}
                </span>
            </button>

            <button class="profile-tab-btn nav-link {{ $activeTab === 'session' ? 'active' : '' }}"
                    id="tab-session"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-session"
                    data-tab-name="session"
                    type="button"
                    role="tab"
                    aria-controls="pane-session"
                    aria-selected="{{ $activeTab === 'session' ? 'true' : 'false' }}">
                <i data-lucide="monitor" class="me-1.5 flex-shrink-0"></i>
                <span>Session &amp; Diagnostics</span>
            </button>
        </div>

        <!-- Tab Content Panes -->
        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="profileTabPanes">
                <!-- =========================================================================
                     TAB 1: PERSONAL INFORMATION
                     ========================================================================= -->
                <div class="tab-pane fade {{ $activeTab === 'personal' ? 'show active' : '' }}"
                     id="pane-personal"
                     role="tabpanel"
                     aria-labelledby="tab-personal">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-2 p-1.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i data-lucide="user-pen" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div>
                            <h2 class="fs-6 fw-bold mb-0 text-dark">Personal Information</h2>
                            <p class="text-secondary fs-xs mb-0">Update your account display name and official communication email address.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fs-xs fw-semibold text-secondary mb-1">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon-left">
                                    <i class="input-icon-addon" data-lucide="user"></i>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}"
                                           required
                                           placeholder="Enter your full name">
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block fs-xs">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fs-xs fw-semibold text-secondary mb-1">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-icon-left">
                                    <i class="input-icon-addon" data-lucide="mail"></i>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           placeholder="name@example.com">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block fs-xs">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Read-only Account Status Row -->
                        <div class="p-3 bg-light rounded-2 border border-secondary-subtle mb-3">
                            <div class="row g-2 text-secondary fs-xs">
                                <div class="col-sm-4">
                                    <span class="text-secondary d-block">Account Status</span>
                                    <span class="fw-semibold text-dark d-inline-flex align-items-center mt-0.5">
                                        <span class="rounded-circle me-1.5 {{ $user->isActive() ? 'bg-success' : 'bg-warning' }}" style="width: 7px; height: 7px;"></span>
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </div>
                                <div class="col-sm-4">
                                    <span class="text-secondary d-block">Email Verification</span>
                                    <span class="fw-semibold text-dark d-inline-flex align-items-center mt-0.5">
                                        <i data-lucide="{{ $user->email_verified_at ? 'check-circle' : 'shield-alert' }}" class="{{ $user->email_verified_at ? 'text-success' : 'text-secondary' }} me-1" style="width: 14px; height: 14px;"></i>
                                        {{ $user->email_verified_at ? 'Verified (' . $user->email_verified_at->format('M d, Y') . ')' : 'Direct System Account' }}
                                    </span>
                                </div>
                                <div class="col-sm-4">
                                    <span class="text-secondary d-block">Authentication Guard</span>
                                    <span class="fw-semibold text-dark font-monospace d-block mt-0.5">web (session)</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top border-light-subtle">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center px-3 shadow-sm">
                                <i data-lucide="save" class="me-2 flex-shrink-0" style="width: 15px; height: 15px;"></i>
                                <span>Save Personal Details</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- =========================================================================
                     TAB 2: SECURITY & PASSWORD
                     ========================================================================= -->
                <div class="tab-pane fade {{ $activeTab === 'security' ? 'show active' : '' }}"
                     id="pane-security"
                     role="tabpanel"
                     aria-labelledby="tab-security">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-2 p-1.5 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div>
                            <h2 class="fs-6 fw-bold mb-0 text-dark">Security &amp; Password</h2>
                            <p class="text-secondary fs-xs mb-0">Ensure your account is protected with a robust, confidential password.</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <x-form.password-input
                                    id="current_password"
                                    name="current_password"
                                    label="Current Password"
                                    placeholder="Enter current password"
                                    required />
                                @error('current_password')
                                    <div class="invalid-feedback d-block fs-xs">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <x-form.password-input
                                    id="password"
                                    name="password"
                                    label="New Password"
                                    placeholder="Min. 8 characters"
                                    required />
                                @error('password')
                                    <div class="invalid-feedback d-block fs-xs">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <x-form.password-input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    label="Confirm New Password"
                                    placeholder="Re-type new password"
                                    required />
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block fs-xs">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Security Criteria Checklist with Guaranteed Spacing -->
                        <div class="p-3 bg-light rounded-2 border border-secondary-subtle mb-3">
                            <div class="small fw-semibold text-dark mb-2 d-flex align-items-center">
                                <i data-lucide="shield-alert" class="text-primary me-2 flex-shrink-0" style="width: 16px; height: 16px;"></i>
                                <span>Password Security Criteria:</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 text-secondary fs-xs">
                                <span class="badge bg-white text-secondary border px-2.5 py-1.5 d-inline-flex align-items-center shadow-xs">
                                    <i data-lucide="check" class="text-success me-1.5 flex-shrink-0" style="width: 12px; height: 12px;"></i>
                                    <span>At least 8 characters in length</span>
                                </span>
                                <span class="badge bg-white text-secondary border px-2.5 py-1.5 d-inline-flex align-items-center shadow-xs">
                                    <i data-lucide="check" class="text-success me-1.5 flex-shrink-0" style="width: 12px; height: 12px;"></i>
                                    <span>Uppercase and lowercase letters</span>
                                </span>
                                <span class="badge bg-white text-secondary border px-2.5 py-1.5 d-inline-flex align-items-center shadow-xs">
                                    <i data-lucide="check" class="text-success me-1.5 flex-shrink-0" style="width: 12px; height: 12px;"></i>
                                    <span>Numbers and special symbols</span>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top border-light-subtle">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center px-3 shadow-sm">
                                <i data-lucide="key-round" class="me-2 flex-shrink-0" style="width: 15px; height: 15px;"></i>
                                <span>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- =========================================================================
                     TAB 3: ROLES & PERMISSIONS
                     ========================================================================= -->
                <div class="tab-pane fade {{ $activeTab === 'roles' ? 'show active' : '' }}"
                     id="pane-roles"
                     role="tabpanel"
                     aria-labelledby="tab-roles">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-2 p-1.5 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i data-lucide="key" style="width: 16px; height: 16px;"></i>
                            </div>
                            <div>
                                <h2 class="fs-6 fw-bold mb-0 text-dark">Roles &amp; Operational Clearance</h2>
                                <p class="text-secondary fs-xs mb-0">Role-based access permissions, system capabilities, and administrative clearance.</p>
                            </div>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 fs-xs fw-medium">
                            {{ $permissions->count() }} active rules
                        </span>
                    </div>

                    @if ($user->hasRole('Super Admin'))
                        <div class="p-3 rounded-2 border d-flex align-items-center gap-3 bg-light mb-3">
                            <div class="rounded-2 p-2 bg-danger-subtle text-danger flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark fs-6 mb-1">Super Administrator Access (Unrestricted Access)</h3>
                                <p class="text-secondary fs-xs mb-0">
                                    You have full administrative privileges across all system modules, financial reports, operational settings, audit trails, and RBAC user access control.
                                </p>
                            </div>
                        </div>

                        <!-- Highlights Grid -->
                        <div class="row g-2.5 mb-4">
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-2.5 rounded-2 border bg-card h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i data-lucide="bar-chart-3" class="text-primary me-1 flex-shrink-0" style="width: 16px; height: 16px;"></i>
                                        <span class="fw-semibold text-dark fs-xs">Financial Reports</span>
                                    </div>
                                    <p class="text-secondary fs-xs mb-0">Full clearance to Balance Sheet, P&amp;L, General Ledger, and trial balance exports.</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-2.5 rounded-2 border bg-card h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i data-lucide="shopping-cart" class="text-success me-1 flex-shrink-0" style="width: 16px; height: 16px;"></i>
                                        <span class="fw-semibold text-dark fs-xs">Sales &amp; Purchases</span>
                                    </div>
                                    <p class="text-secondary fs-xs mb-0">Unrestricted review of AR/AP invoices, purchase orders, and payment records.</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-2.5 rounded-2 border bg-card h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i data-lucide="users" class="text-info me-1 flex-shrink-0" style="width: 16px; height: 16px;"></i>
                                        <span class="fw-semibold text-dark fs-xs">User Management</span>
                                    </div>
                                    <p class="text-secondary fs-xs mb-0">Ability to create, update, deactivate accounts, and oversee security settings.</p>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="p-2.5 rounded-2 border bg-card h-100">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i data-lucide="shield" class="text-danger me-1 flex-shrink-0" style="width: 16px; height: 16px;"></i>
                                        <span class="fw-semibold text-dark fs-xs">RBAC &amp; Security</span>
                                    </div>
                                    <p class="text-secondary fs-xs mb-0">Manage roles, allocate specific permissions, and maintain governance standards.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- =====================================================================
                         EFFECTIVE OPERATIONAL PERMISSIONS GROUPED BY ROLE
                         ===================================================================== -->
                    <div class="perm-section-wrapper">
                        <!-- Toolbar: Section Title, Search Input, and Collapse/Expand Buttons -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <h6 class="fw-bold text-dark fs-xs text-uppercase mb-0 d-flex align-items-center">
                                    <i data-lucide="key-round" class="text-primary me-2 flex-shrink-0" style="width: 15px; height: 15px;"></i>
                                    <span>Effective Operational Permissions by Role</span>
                                </h6>
                                <p class="text-secondary fs-xs mb-0 mt-0.5">
                                    Role-governed privileges, operational report access, and administrative actions grouped by assigned role.
                                </p>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="input-group input-group-sm" style="max-width: 240px;">
                                    <span class="input-group-text bg-body-tertiary border-end-0 text-secondary py-1 px-2">
                                        <i data-lucide="search" style="width: 13px; height: 13px;"></i>
                                    </span>
                                    <input type="search" id="perm-search-input" class="form-control form-control-sm border-start-0 ps-1"
                                           placeholder="Filter permissions..." aria-label="Search permissions">
                                </div>

                                <button type="button" id="btn-perm-expand-all" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 py-1 px-2 fs-xs" title="Expand all roles">
                                    <i data-lucide="chevrons-down" style="width: 13px; height: 13px;"></i>
                                    <span>Expand All</span>
                                </button>

                                <button type="button" id="btn-perm-collapse-all" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 py-1 px-2 fs-xs" title="Collapse all roles">
                                    <i data-lucide="chevrons-up" style="width: 13px; height: 13px;"></i>
                                    <span>Collapse All</span>
                                </button>
                            </div>
                        </div>

                        <!-- Quick Category Filter Chips -->
                        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-3" id="perm-module-filters">
                            <button type="button" class="btn btn-xs rounded-pill active perm-filter-btn" data-module-filter="all">
                                All Modules
                            </button>
                            <button type="button" class="btn btn-xs rounded-pill perm-filter-btn" data-module-filter="financial">
                                <i data-lucide="bar-chart-3" style="width: 12px; height: 12px;"></i> Financials
                            </button>
                            <button type="button" class="btn btn-xs rounded-pill perm-filter-btn" data-module-filter="sales">
                                <i data-lucide="shopping-cart" style="width: 12px; height: 12px;"></i> Sales
                            </button>
                            <button type="button" class="btn btn-xs rounded-pill perm-filter-btn" data-module-filter="purchase">
                                <i data-lucide="shopping-bag" style="width: 12px; height: 12px;"></i> Purchases
                            </button>
                            <button type="button" class="btn btn-xs rounded-pill perm-filter-btn" data-module-filter="users">
                                <i data-lucide="users" style="width: 12px; height: 12px;"></i> User &amp; RBAC
                            </button>
                            <button type="button" class="btn btn-xs rounded-pill perm-filter-btn" data-module-filter="export">
                                <i data-lucide="file-output" style="width: 12px; height: 12px;"></i> Exports
                            </button>
                        </div>

                        <!-- Container of Role Cards -->
                        <div class="d-flex flex-column gap-3" id="role-permissions-container">
                            @forelse ($roles as $role)
                                @php
                                    $roleModules = $rolePermissionsGrouped[$role->id] ?? [];
                                    $rolePermCount = $role->permissions->count();
                                    $roleSlug = Str::slug($role->name);
                                    $landingPageLabel = \App\Support\PermissionHelper::getLandingPageLabel($role->landing_page);
                                    $isSuper = $role->name === 'Super Admin';
                                @endphp

                                <div class="card border rounded-3 overflow-hidden shadow-xs role-card-wrapper" data-role-slug="{{ $roleSlug }}">
                                    <!-- Role Header Bar with Toggle Collapse -->
                                    <div class="card-header bg-card border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 cursor-pointer role-collapse-trigger"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#collapse-role-{{ $role->id }}"
                                         aria-expanded="true"
                                         aria-controls="collapse-role-{{ $role->id }}"
                                         role="button">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="rounded-2 p-1.5 {{ $isSuper ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }} flex-shrink-0 d-flex align-items-center justify-content-center"
                                                 style="width: 34px; height: 34px;">
                                                <i data-lucide="{{ $isSuper ? 'shield-alert' : 'shield-check' }}" style="width: 18px; height: 18px;"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span class="fw-bold text-dark fs-sm">{{ $role->name }}</span>
                                                    @if ($isSuper)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5 fs-xs">
                                                            Unrestricted
                                                        </span>
                                                    @endif
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 fs-xs">
                                                        Guard: {{ $role->guard_name }}
                                                    </span>
                                                    @if ($landingPageLabel)
                                                        <span class="badge bg-body-tertiary text-secondary border px-2 py-0.5 fs-xs d-inline-flex align-items-center gap-1">
                                                            <i data-lucide="compass" style="width: 11px; height: 11px;"></i>
                                                            <span>Landing: {{ $landingPageLabel }}</span>
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-secondary fs-xs d-block mt-0.5">
                                                    {{ $rolePermCount }} {{ Str::plural('permission', $rolePermCount) }} granted by this role
                                                </span>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $isSuper ? 'bg-danger-subtle text-danger border-danger-subtle' : 'bg-primary-subtle text-primary border-primary-subtle' }} border px-2.5 py-1 fs-xs fw-semibold">
                                                {{ $rolePermCount }} Rules
                                            </span>
                                            <i data-lucide="chevron-down" class="role-chevron text-secondary transition-transform" style="width: 18px; height: 18px;"></i>
                                        </div>
                                    </div>

                                    <!-- Role Permissions Content -->
                                    <div class="collapse show role-collapse-body" id="collapse-role-{{ $role->id }}">
                                        <div class="card-body p-3 bg-body-tertiary">
                                            @if (! empty($roleModules))
                                                <div class="d-flex flex-column gap-3">
                                                    @foreach ($roleModules as $modKey => $module)
                                                        <div class="bg-card border rounded-2 p-3 module-group-wrapper" data-module-key="{{ $modKey }}">
                                                            <!-- Module Category Header -->
                                                            <div class="d-flex align-items-center justify-content-between pb-2 mb-2.5 border-bottom">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="rounded-2 p-1 bg-{{ $module['color'] }}-subtle text-{{ $module['color'] }} d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                                        <i data-lucide="{{ $module['icon'] }}" style="width: 14px; height: 14px;"></i>
                                                                    </div>
                                                                    <span class="fw-semibold text-dark fs-sm">{{ $module['name'] }}</span>
                                                                </div>
                                                                <span class="badge bg-body-tertiary text-secondary border px-2 py-0.5 fs-xs">
                                                                    {{ $module['total_permissions'] }} {{ Str::plural('rule', $module['total_permissions']) }}
                                                                </span>
                                                            </div>

                                                            <!-- Submodule Items Grid -->
                                                            <div class="row g-2">
                                                                @foreach ($module['submodules'] as $subSlug => $submodule)
                                                                    <div class="col-md-6 col-12 submodule-item-wrapper" data-perm-submodule="{{ strtolower($submodule['name']) }}">
                                                                        <div class="p-2.5 rounded-2 border bg-body-tertiary h-100 d-flex flex-column justify-content-between gap-2 shadow-2xs">
                                                                            <div class="d-flex align-items-center gap-1.5">
                                                                                <i data-lucide="{{ $submodule['icon'] }}" class="text-secondary flex-shrink-0" style="width: 13px; height: 13px;"></i>
                                                                                <span class="fw-semibold text-dark fs-xs text-truncate">{{ $submodule['name'] }}</span>
                                                                            </div>
                                                                            <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                                                                @foreach ($submodule['actions'] as $act)
                                                                                    <span class="badge {{ $act['badge_class'] }} border px-2 py-1 fs-xs fw-normal d-inline-flex align-items-center gap-1 cursor-default action-pill"
                                                                                          title="System rule: {{ $act['name'] }}"
                                                                                          data-perm-name="{{ strtolower($act['name']) }}"
                                                                                          data-perm-action="{{ strtolower($act['action']) }}">
                                                                                        <i data-lucide="{{ $act['icon'] }}" style="width: 11px; height: 11px;" class="flex-shrink-0"></i>
                                                                                        <span>{{ $act['action'] }}</span>
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-3 text-center text-secondary fs-xs fst-italic">
                                                    No granular permissions assigned to this role.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 rounded-3 border bg-card text-center">
                                    <div class="rounded-circle p-2 bg-secondary-subtle text-secondary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i data-lucide="shield-off" style="width: 22px; height: 22px;"></i>
                                    </div>
                                    <h6 class="fw-semibold text-dark fs-sm mb-1">No Dedicated Roles Assigned</h6>
                                    <p class="text-secondary fs-xs mb-0">Your account does not currently hold any assigned administrative roles.</p>
                                </div>
                            @endforelse

                            <!-- Direct User Permissions (if any assigned directly to user) -->
                            @if (! empty($directPermissionsGrouped))
                                <div class="card border rounded-3 overflow-hidden shadow-xs role-card-wrapper" data-role-slug="direct-permissions">
                                    <div class="card-header bg-card border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 cursor-pointer role-collapse-trigger"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#collapse-role-direct"
                                         aria-expanded="true"
                                         aria-controls="collapse-role-direct"
                                         role="button">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="rounded-2 p-1.5 bg-info-subtle text-info flex-shrink-0 d-flex align-items-center justify-content-center"
                                                 style="width: 34px; height: 34px;">
                                                <i data-lucide="user-check" style="width: 18px; height: 18px;"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <span class="fw-bold text-dark fs-sm">Direct User Permissions</span>
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 fs-xs">
                                                        Individually Granted
                                                    </span>
                                                </div>
                                                <span class="text-secondary fs-xs d-block mt-0.5">
                                                    {{ $directPermissions->count() }} direct permissions allocated directly to your user account
                                                </span>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 fs-xs fw-semibold">
                                                {{ $directPermissions->count() }} Rules
                                            </span>
                                            <i data-lucide="chevron-down" class="role-chevron text-secondary transition-transform" style="width: 18px; height: 18px;"></i>
                                        </div>
                                    </div>

                                    <div class="collapse show role-collapse-body" id="collapse-role-direct">
                                        <div class="card-body p-3 bg-body-tertiary">
                                            <div class="d-flex flex-column gap-3">
                                                @foreach ($directPermissionsGrouped as $modKey => $module)
                                                    <div class="bg-card border rounded-2 p-3 module-group-wrapper" data-module-key="{{ $modKey }}">
                                                        <div class="d-flex align-items-center justify-content-between pb-2 mb-2.5 border-bottom">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="rounded-2 p-1 bg-{{ $module['color'] }}-subtle text-{{ $module['color'] }} d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                                    <i data-lucide="{{ $module['icon'] }}" style="width: 14px; height: 14px;"></i>
                                                                </div>
                                                                <span class="fw-semibold text-dark fs-sm">{{ $module['name'] }}</span>
                                                            </div>
                                                            <span class="badge bg-body-tertiary text-secondary border px-2 py-0.5 fs-xs">
                                                                {{ $module['total_permissions'] }} {{ Str::plural('rule', $module['total_permissions']) }}
                                                            </span>
                                                        </div>

                                                        <div class="row g-2">
                                                            @foreach ($module['submodules'] as $subSlug => $submodule)
                                                                <div class="col-md-6 col-12 submodule-item-wrapper" data-perm-submodule="{{ strtolower($submodule['name']) }}">
                                                                    <div class="p-2.5 rounded-2 border bg-body-tertiary h-100 d-flex flex-column justify-content-between gap-2 shadow-2xs">
                                                                        <div class="d-flex align-items-center gap-1.5">
                                                                            <i data-lucide="{{ $submodule['icon'] }}" class="text-secondary flex-shrink-0" style="width: 13px; height: 13px;"></i>
                                                                            <span class="fw-semibold text-dark fs-xs text-truncate">{{ $submodule['name'] }}</span>
                                                                        </div>
                                                                        <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                                                            @foreach ($submodule['actions'] as $act)
                                                                                <span class="badge {{ $act['badge_class'] }} border px-2 py-1 fs-xs fw-normal d-inline-flex align-items-center gap-1 cursor-default action-pill"
                                                                                      title="System rule: {{ $act['name'] }}"
                                                                                      data-perm-name="{{ strtolower($act['name']) }}"
                                                                                      data-perm-action="{{ strtolower($act['action']) }}">
                                                                                    <i data-lucide="{{ $act['icon'] }}" style="width: 11px; height: 11px;" class="flex-shrink-0"></i>
                                                                                    <span>{{ $act['action'] }}</span>
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- No Search Results Found Alert -->
                            <div id="perm-no-results-alert" class="p-4 rounded-3 border bg-card text-center d-none">
                                <div class="rounded-circle p-2 bg-secondary-subtle text-secondary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i data-lucide="search-x" style="width: 20px; height: 20px;"></i>
                                </div>
                                <h6 class="fw-semibold text-dark fs-sm mb-1">No Matching Permissions Found</h6>
                                <p class="text-secondary fs-xs mb-0">Try searching with a different keyword (e.g. "view", "export", "users", "invoice").</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =========================================================================
                     TAB 4: SESSION & DIAGNOSTICS
                     ========================================================================= -->
                <div class="tab-pane fade {{ $activeTab === 'session' ? 'show active' : '' }}"
                     id="pane-session"
                     role="tabpanel"
                     aria-labelledby="tab-session">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-2 p-1.5 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i data-lucide="monitor" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div>
                            <h2 class="fs-6 fw-bold mb-0 text-dark">Current Browser Session &amp; System Diagnostics</h2>
                            <p class="text-secondary fs-xs mb-0">Active connection context, security token parameters, and runtime environment.</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-2.5 rounded-2 border bg-card h-100">
                                <span class="text-secondary fs-xs d-block">Connected Client IP</span>
                                <span class="fw-semibold text-dark fs-sm font-monospace d-block mt-0.5">{{ request()->ip() }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-2.5 rounded-2 border bg-card h-100">
                                <span class="text-secondary fs-xs d-block">Security Guard &amp; Session</span>
                                <span class="fw-semibold text-dark fs-sm d-block mt-0.5">web (Active Session)</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-2.5 rounded-2 border bg-card h-100">
                                <span class="text-secondary fs-xs d-block">System Locale &amp; Timezone</span>
                                <span class="fw-semibold text-dark fs-sm d-block mt-0.5">{{ strtoupper(app()->getLocale()) }} / {{ config('app.timezone') }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-2.5 rounded-2 border bg-card h-100">
                                <span class="text-secondary fs-xs d-block">User Identifier (Primary Key)</span>
                                <span class="fw-semibold text-dark fs-sm d-block mt-0.5">#{{ $user->id }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-2.5 rounded-2 border bg-card h-100">
                                <span class="text-secondary fs-xs d-block">Account Registration Date</span>
                                <span class="fw-semibold text-dark fs-sm d-block mt-0.5">{{ $user->created_at?->format('M d, Y · h:i A') ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-2.5 rounded-2 border bg-card h-100">
                                <span class="text-secondary fs-xs d-block">Last Profile Update</span>
                                <span class="fw-semibold text-dark fs-sm d-block mt-0.5">{{ $user->updated_at?->diffForHumans() ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-2.5 rounded-2 border bg-card">
                                <span class="text-secondary fs-xs d-block">User-Agent Signature</span>
                                <span class="fw-normal text-secondary fs-xs font-monospace d-block mt-0.5 text-break">
                                    {{ request()->userAgent() ?? 'Direct HTTP Request' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top border-light-subtle">
                        <span class="text-secondary fs-xs">
                            Session is protected via CSRF token validation and HTTP-only cookies.
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                            <i data-lucide="layout-dashboard" class="me-1.5 flex-shrink-0" style="width: 14px; height: 14px;"></i>
                            <span>Return to Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Immediate Synchronous Tab Rehydration (Prevents any visual flicker on page refresh) -->
    <script>
        (function () {
            try {
                const validTabs = ['personal', 'security', 'roles', 'session'];
                const urlParams = new URLSearchParams(window.location.search);
                const queryTab = urlParams.get('tab');
                const hashTab = window.location.hash ? window.location.hash.replace('#', '').replace('pane-', '').replace('tab-', '') : null;
                const storedTab = localStorage.getItem('profile_active_tab') || sessionStorage.getItem('profile_active_tab');
                const hasErrors = @json($errors->any());
                let targetTab = null;

                if (hasErrors) {
                    targetTab = @json($activeTab);
                } else if (queryTab && validTabs.includes(queryTab)) {
                    targetTab = queryTab;
                } else if (hashTab && validTabs.includes(hashTab)) {
                    targetTab = hashTab;
                } else if (storedTab && validTabs.includes(storedTab)) {
                    targetTab = storedTab;
                }

                if (targetTab && validTabs.includes(targetTab)) {
                    const btns = document.querySelectorAll('#profileTabsNav .profile-tab-btn');
                    btns.forEach(function (b) {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });
                    const targetBtn = document.querySelector('#profileTabsNav button[data-tab-name="' + targetTab + '"]') 
                        || document.getElementById('tab-' + targetTab);
                    if (targetBtn) {
                        targetBtn.classList.add('active');
                        targetBtn.setAttribute('aria-selected', 'true');
                    }

                    const panes = document.querySelectorAll('#profileTabPanes .tab-pane');
                    panes.forEach(function (p) {
                        p.classList.remove('show', 'active');
                    });
                    const targetPane = document.getElementById('pane-' + targetTab);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }
                }
            } catch (e) {}
        })();
    </script>

    <!-- Interactive Tab Handling, Persistence & Collapse/Expand Scripts -->
    @push('scripts')
        <script>
            (function () {
                const validTabs = ['personal', 'security', 'roles', 'session'];

                function switchProfileTab(tabName, persist = true) {
                    if (!validTabs.includes(tabName)) return;

                    const nav = document.getElementById('profileTabsNav');
                    if (!nav) return;

                    const tabBtn = nav.querySelector(`button[data-tab-name="${tabName}"]`) 
                        || document.getElementById('tab-' + tabName);
                    const tabPane = document.getElementById('pane-' + tabName);

                    if (!tabBtn || !tabPane) return;

                    // Update Tab Navigation Buttons
                    nav.querySelectorAll('.profile-tab-btn').forEach(btn => {
                        btn.classList.remove('active');
                        btn.setAttribute('aria-selected', 'false');
                    });
                    tabBtn.classList.add('active');
                    tabBtn.setAttribute('aria-selected', 'true');

                    // Update Tab Content Panes
                    const panesContainer = document.getElementById('profileTabPanes');
                    if (panesContainer) {
                        panesContainer.querySelectorAll('.tab-pane').forEach(pane => {
                            pane.classList.remove('show', 'active');
                        });
                    }
                    tabPane.classList.add('show', 'active');

                    // Try Bootstrap Tab instance if available
                    if (window.bootstrap && window.bootstrap.Tab) {
                        try {
                            const inst = window.bootstrap.Tab.getOrCreateInstance(tabBtn);
                            inst.show();
                        } catch (e) {}
                    }

                    // Save state to localStorage, sessionStorage and URL Hash
                    if (persist) {
                        try {
                            localStorage.setItem('profile_active_tab', tabName);
                            sessionStorage.setItem('profile_active_tab', tabName);
                        } catch (e) {}
                        if (window.history && window.history.replaceState) {
                            window.history.replaceState(null, '', '#' + tabName);
                        }
                    }

                    // Re-render Lucide icons inside newly visible tab
                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons();
                    }
                }

                function initTabListeners() {
                    const nav = document.getElementById('profileTabsNav');
                    if (!nav) return;

                    // Attach direct click handlers to tab buttons
                    nav.querySelectorAll('.profile-tab-btn').forEach(btn => {
                        btn.addEventListener('click', function (e) {
                            const tabName = this.getAttribute('data-tab-name') || this.id.replace('tab-', '');
                            switchProfileTab(tabName, true);
                        });

                        // Also sync on Bootstrap's native event if triggered
                        btn.addEventListener('shown.bs.tab', function (e) {
                            const tabName = this.getAttribute('data-tab-name') || this.id.replace('tab-', '');
                            try {
                                localStorage.setItem('profile_active_tab', tabName);
                                sessionStorage.setItem('profile_active_tab', tabName);
                            } catch (err) {}
                            if (window.history && window.history.replaceState) {
                                window.history.replaceState(null, '', '#' + tabName);
                            }
                            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                                window.lucide.createIcons();
                            }
                        });
                    });

                    // Determine active tab on page load/refresh
                    const urlParams = new URLSearchParams(window.location.search);
                    const queryTab = urlParams.get('tab');
                    const hashTab = window.location.hash ? window.location.hash.replace('#', '').replace('pane-', '').replace('tab-', '') : null;
                    let storedTab = null;
                    try {
                        storedTab = localStorage.getItem('profile_active_tab') || sessionStorage.getItem('profile_active_tab');
                    } catch (e) {}

                    const hasErrors = @json($errors->any());
                    const serverTab = @json($activeTab);

                    if (hasErrors) {
                        switchProfileTab(serverTab, true);
                    } else if (queryTab && validTabs.includes(queryTab)) {
                        switchProfileTab(queryTab, true);
                    } else if (hashTab && validTabs.includes(hashTab)) {
                        switchProfileTab(hashTab, true);
                    } else if (storedTab && validTabs.includes(storedTab)) {
                        switchProfileTab(storedTab, false);
                    } else if (serverTab && validTabs.includes(serverTab)) {
                        switchProfileTab(serverTab, false);
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initTabListeners);
                } else {
                    initTabListeners();
                }

                // =====================================================================
                // 2. Expand All & Collapse All in Effective Operational Permissions
                // =====================================================================
                function setRoleCollapse(body, expand) {
                    if (!body) return;
                    const trigger = document.querySelector(`[data-bs-target="#${body.id}"]`) 
                        || body.closest('.role-card-wrapper')?.querySelector('.role-collapse-trigger');

                    if (expand) {
                        body.classList.add('show');
                        body.style.display = 'block';
                        if (trigger) {
                            trigger.classList.remove('collapsed');
                            trigger.setAttribute('aria-expanded', 'true');
                        }
                        if (window.bootstrap && window.bootstrap.Collapse) {
                            try {
                                window.bootstrap.Collapse.getOrCreateInstance(body, { toggle: false }).show();
                            } catch (e) {}
                        }
                    } else {
                        body.classList.remove('show');
                        body.style.display = 'none';
                        if (trigger) {
                            trigger.classList.add('collapsed');
                            trigger.setAttribute('aria-expanded', 'false');
                        }
                        if (window.bootstrap && window.bootstrap.Collapse) {
                            try {
                                window.bootstrap.Collapse.getOrCreateInstance(body, { toggle: false }).hide();
                            } catch (e) {}
                        }
                    }
                }

                const expandAllBtn = document.getElementById('btn-perm-expand-all');
                const collapseAllBtn = document.getElementById('btn-perm-collapse-all');

                if (expandAllBtn) {
                    expandAllBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        document.querySelectorAll('.role-collapse-body').forEach(el => {
                            setRoleCollapse(el, true);
                        });
                    });
                }

                if (collapseAllBtn) {
                    collapseAllBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        document.querySelectorAll('.role-collapse-body').forEach(el => {
                            setRoleCollapse(el, false);
                        });
                    });
                }

                // Ensure card header collapse toggles update trigger and body smoothly
                document.querySelectorAll('.role-collapse-trigger').forEach(trigger => {
                    trigger.addEventListener('click', function (e) {
                        const targetSelector = this.getAttribute('data-bs-target');
                        const body = targetSelector ? document.querySelector(targetSelector) : null;
                        if (!body) return;

                        // Give Bootstrap a micro-tick, then sync classes and display
                        setTimeout(() => {
                            const isShown = body.classList.contains('show');
                            body.style.display = isShown ? 'block' : 'none';
                            if (isShown) {
                                trigger.classList.remove('collapsed');
                                trigger.setAttribute('aria-expanded', 'true');
                            } else {
                                trigger.classList.add('collapsed');
                                trigger.setAttribute('aria-expanded', 'false');
                            }
                        }, 60);
                    });
                });

                // =====================================================================
                // 3. Category Filter Chips & Live Search
                // =====================================================================
                const searchInput = document.getElementById('perm-search-input');
                const filterButtons = document.querySelectorAll('.perm-filter-btn');
                const noResultsAlert = document.getElementById('perm-no-results-alert');
                let activeModuleFilter = 'all';

                function filterPermissions() {
                    const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
                    let totalVisible = 0;

                    const roleCards = document.querySelectorAll('.role-card-wrapper');
                    roleCards.forEach(card => {
                        let roleHasVisible = false;
                        const moduleGroups = card.querySelectorAll('.module-group-wrapper');

                        moduleGroups.forEach(group => {
                            const modKey = (group.getAttribute('data-module-key') || '').toLowerCase();
                            let moduleMatchesFilter = activeModuleFilter === 'all';
                            if (activeModuleFilter === 'financial' && modKey.includes('financial')) moduleMatchesFilter = true;
                            if (activeModuleFilter === 'sales' && modKey.includes('sales')) moduleMatchesFilter = true;
                            if (activeModuleFilter === 'purchase' && modKey.includes('purchase')) moduleMatchesFilter = true;
                            if (activeModuleFilter === 'users' && (modKey.includes('user') || modKey.includes('role') || modKey.includes('perm') || modKey.includes('rbac'))) moduleMatchesFilter = true;
                            if (activeModuleFilter === 'export' && modKey.includes('export')) moduleMatchesFilter = true;

                            let groupHasVisible = false;
                            const submoduleItems = group.querySelectorAll('.submodule-item-wrapper');

                            submoduleItems.forEach(item => {
                                const subName = (item.getAttribute('data-perm-submodule') || '').toLowerCase();
                                const pills = item.querySelectorAll('.action-pill');
                                let itemHasMatch = false;

                                pills.forEach(pill => {
                                    const permName = (pill.getAttribute('data-perm-name') || '').toLowerCase();
                                    const permAction = (pill.getAttribute('data-perm-action') || '').toLowerCase();

                                    const matchesQuery = !query || permName.includes(query) || permAction.includes(query) || subName.includes(query);
                                    if (matchesQuery) {
                                        pill.classList.remove('d-none');
                                        itemHasMatch = true;
                                    } else {
                                        pill.classList.add('d-none');
                                    }
                                });

                                if (moduleMatchesFilter && itemHasMatch) {
                                    item.classList.remove('d-none');
                                    groupHasVisible = true;
                                } else {
                                    item.classList.add('d-none');
                                }
                            });

                            if (moduleMatchesFilter && groupHasVisible) {
                                group.classList.remove('d-none');
                                roleHasVisible = true;
                            } else {
                                group.classList.add('d-none');
                            }
                        });

                        if (roleHasVisible) {
                            card.classList.remove('d-none');
                            totalVisible++;
                            // Auto expand when actively filtering or searching
                            if (query || activeModuleFilter !== 'all') {
                                const collapseBody = card.querySelector('.role-collapse-body');
                                if (collapseBody) {
                                    setRoleCollapse(collapseBody, true);
                                }
                            }
                        } else {
                            card.classList.add('d-none');
                        }
                    });

                    if (noResultsAlert) {
                        if (totalVisible === 0) {
                            noResultsAlert.classList.remove('d-none');
                        } else {
                            noResultsAlert.classList.add('d-none');
                        }
                    }

                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons();
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', filterPermissions);
                }

                filterButtons.forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        filterButtons.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        activeModuleFilter = this.getAttribute('data-module-filter') || 'all';
                        filterPermissions();
                    });
                });
            })();
        </script>
    @endpush

    @push('styles')
        <style>
            .perm-filter-btn {
                border: 1px solid var(--bdr, #e5e7eb);
                background: var(--bg-card, #ffffff);
                color: var(--tx2, #6b7280);
                font-size: 11px;
                font-weight: 500;
                padding: 3px 10px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                cursor: pointer;
                transition: all 0.15s ease;
            }
            .perm-filter-btn:hover {
                background: var(--pri-soft, rgba(24, 144, 255, 0.08));
                color: var(--pri, #1890ff);
                border-color: var(--pri, #1890ff);
            }
            .perm-filter-btn.active {
                background: var(--pri, #1890ff) !important;
                color: #ffffff !important;
                border-color: var(--pri, #1890ff) !important;
            }
            .role-chevron {
                transition: transform 0.2s ease;
            }
            .role-collapse-trigger.collapsed .role-chevron {
                transform: rotate(-90deg);
            }
            .action-pill {
                font-size: 11px;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }
            .action-pill:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.06);
            }
            .shadow-2xs {
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            }
            [data-theme='dark'] .perm-filter-btn,
            [data-bs-theme='dark'] .perm-filter-btn {
                background: var(--bg-card, #1e2640);
                border-color: var(--bdr, #2a3347);
                color: var(--tx2, #94a3b8);
            }
            [data-theme='dark'] .perm-filter-btn.active,
            [data-bs-theme='dark'] .perm-filter-btn.active {
                background: var(--pri, #1890ff) !important;
                color: #ffffff !important;
                border-color: var(--pri, #1890ff) !important;
            }
        </style>
    @endpush
</x-app-layout>
