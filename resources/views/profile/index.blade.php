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
        if ($errors->userDeletion->isNotEmpty() || $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation')) {
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
                    <div class="position-relative flex-shrink-0 avatar-badge-wrapper cursor-pointer dropdown" id="bannerAvatarWrapper">
                        <div class="profile-avatar-display d-flex align-items-center justify-content-center text-white fw-bold rounded-circle shadow-sm overflow-hidden"
                             style="width: 56px; height: 56px; font-size: 20px; background: linear-gradient(135deg, var(--pri, #1890ff) 0%, #10239e 100%);"
                             id="bannerAvatarTrigger"
                             data-bs-toggle="dropdown"
                             aria-expanded="false"
                             title="Click to manage profile photo">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover user-avatar-image">
                            @else
                                <span class="user-avatar-initials">{{ $initials }}</span>
                            @endif
                        </div>
                        <span class="position-absolute bottom-0 end-0 border border-2 border-white rounded-circle {{ $user->isActive() ? 'bg-success' : 'bg-secondary' }}"
                              title="{{ $user->isActive() ? 'Active Account' : 'Inactive Account' }}"
                              style="width: 13px; height: 13px; z-index: 2;"></span>
                        <span class="avatar-hover-overlay rounded-circle d-flex align-items-center justify-content-center">
                            <i data-lucide="camera" style="width: 18px; height: 18px; color: #ffffff;"></i>
                        </span>

                        <ul class="dropdown-menu shadow-sm border py-1" style="font-size: 13px; min-width: 175px;">
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-1.5" id="btnDropdownUploadAvatar">
                                    <i data-lucide="upload" style="width: 14px; height: 14px;" class="text-primary"></i>
                                    <span>Upload New Photo</span>
                                </button>
                            </li>
                            <li class="btn-remove-avatar-item {{ $user->avatar_url ? '' : 'd-none' }}">
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-1.5 text-danger btn-remove-avatar-trigger">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;" class="text-danger"></i>
                                    <span>Remove Photo</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Hidden input for file selection -->
                        <input type="file" id="avatarFileInput" accept="image/jpeg,image/png,image/webp,image/gif" class="d-none">
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

                    <!-- Danger Zone: Delete Profile Account -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="p-3 p-md-4 rounded-3 border border-danger-subtle bg-danger-subtle bg-opacity-10">
                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="rounded-2 p-1 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                            <i data-lucide="alert-triangle" style="width: 15px; height: 15px;"></i>
                                        </div>
                                        <h3 class="fs-6 fw-bold text-danger mb-0">Delete Account</h3>
                                    </div>
                                    <p class="text-secondary fs-xs mb-0" style="max-width: 620px;">
                                        Once your account profile is permanently deleted, all of your user data, avatar, and assigned permissions will be permanently erased. Please be certain before proceeding.
                                    </p>
                                </div>
                                <button type="button" 
                                        class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1.5 shadow-sm"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteAccountModal">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                    <span>Delete Account</span>
                                </button>
                            </div>

                            @if ($errors->userDeletion->isNotEmpty())
                                <div class="alert alert-danger fs-xs mt-3 mb-0 p-2 d-flex align-items-center gap-2">
                                    <i data-lucide="alert-circle" style="width: 14px; height: 14px;" class="flex-shrink-0"></i>
                                    <span>{{ $errors->userDeletion->first() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
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

    <!-- Avatar Crop & Preview Modal -->
    <div class="modal fade" id="avatarCropModal" tabindex="-1" aria-labelledby="avatarCropModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="modal-header border-bottom py-2.5 px-3 px-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 p-1.5 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i data-lucide="crop" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fs-6 fw-bold mb-0 text-dark" id="avatarCropModalLabel">Crop Profile Photo</h5>
                            <p class="text-secondary fs-xs mb-0">Adjust position and zoom to fit your avatar</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3 p-md-4">
                    <div class="row g-4 align-items-center">
                        <!-- Left: Crop Canvas Workspace -->
                        <div class="col-lg-7">
                            <div class="crop-workspace-container position-relative rounded-3 overflow-hidden d-flex align-items-center justify-content-center shadow-inner" style="height: 340px; background-color: #0f172a; cursor: grab;">
                                <canvas id="cropCanvas" width="340" height="340" class="d-block"></canvas>
                                <!-- Drag hint -->
                                <div class="position-absolute bottom-0 start-50 translate-middle-x mb-2 px-2.5 py-1 rounded-pill bg-black bg-opacity-60 text-white fs-xs pointer-events-none d-flex align-items-center gap-1.5">
                                    <i data-lucide="move" style="width: 12px; height: 12px;"></i>
                                    <span>Drag to reposition image</span>
                                </div>
                            </div>

                            <!-- Zoom & Rotate Controls Toolbar -->
                            <div class="mt-3 p-2.5 bg-body-tertiary rounded-3 border d-flex align-items-center gap-3">
                                <span class="text-secondary fs-xs fw-semibold d-flex align-items-center gap-1 flex-shrink-0">
                                    <i data-lucide="zoom-in" style="width: 14px; height: 14px;"></i>
                                    <span>Zoom:</span>
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-secondary p-1 d-flex align-items-center justify-content-center" id="cropZoomOutBtn" title="Zoom out" style="width: 28px; height: 28px;">
                                    <i data-lucide="minus" style="width: 14px; height: 14px;"></i>
                                </button>
                                <input type="range" class="form-range flex-grow-1" id="cropZoomRange" min="0.5" max="3" step="0.01" value="1">
                                <button type="button" class="btn btn-sm btn-outline-secondary p-1 d-flex align-items-center justify-content-center" id="cropZoomInBtn" title="Zoom in" style="width: 28px; height: 28px;">
                                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                </button>
                                <div class="vr my-1"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary p-1 d-flex align-items-center justify-content-center" id="cropRotateLeftBtn" title="Rotate left 90°" style="width: 28px; height: 28px;">
                                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary p-1 d-flex align-items-center justify-content-center" id="cropRotateRightBtn" title="Rotate right 90°" style="width: 28px; height: 28px;">
                                    <i data-lucide="rotate-cw" style="width: 14px; height: 14px;"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary p-1 d-flex align-items-center justify-content-center" id="cropResetBtn" title="Reset crop" style="width: 28px; height: 28px;">
                                    <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Right: Live Previews -->
                        <div class="col-lg-5">
                            <div class="p-3 bg-body-tertiary rounded-3 border text-center">
                                <h6 class="fs-xs fw-bold text-uppercase text-secondary letter-spacing-1 mb-3">Live Real-time Preview</h6>
                                
                                <div class="d-flex flex-column align-items-center gap-3">
                                    <!-- Large Circle Preview -->
                                    <div>
                                        <div class="preview-box-large rounded-circle overflow-hidden shadow-sm mx-auto border border-2 border-primary" style="width: 120px; height: 120px; background: var(--bg-card);">
                                            <canvas id="cropPreviewLarge" width="120" height="120" class="w-100 h-100"></canvas>
                                        </div>
                                        <small class="text-secondary fs-xs mt-1 d-block">Profile Banner (120px)</small>
                                    </div>

                                    <!-- Small Circle Preview -->
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            <div class="preview-box-small rounded-circle overflow-hidden shadow-sm mx-auto border border-primary" style="width: 44px; height: 44px; background: var(--bg-card);">
                                                <canvas id="cropPreviewSmall" width="44" height="44" class="w-100 h-100"></canvas>
                                            </div>
                                            <small class="text-secondary fs-xs mt-1 d-block">Topbar Nav (44px)</small>
                                        </div>
                                        <div>
                                            <div class="preview-box-xs rounded-circle overflow-hidden shadow-sm mx-auto border border-primary" style="width: 30px; height: 30px; background: var(--bg-card);">
                                                <canvas id="cropPreviewXs" width="30" height="30" class="w-100 h-100"></canvas>
                                            </div>
                                            <small class="text-secondary fs-xs mt-1 d-block">Sidebar (30px)</small>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-secondary fs-xs mt-3 mb-0">
                                    This image will be stored securely in the system folder and displayed across all headers.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-3 px-md-4 bg-body-tertiary">
                    <button type="button" class="btn btn-sm btn-outline-danger me-auto btn-remove-avatar-trigger {{ $user->avatar_url ? '' : 'd-none' }}">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        <span>Remove Current Photo</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm" id="btnSaveAvatarCrop">
                        <span class="spinner-border spinner-border-sm d-none" id="saveAvatarSpinner" role="status" aria-hidden="true"></span>
                        <i data-lucide="check" id="saveAvatarCheckIcon" style="width: 15px; height: 15px;"></i>
                        <span id="saveAvatarText">Save &amp; Apply Photo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header border-bottom pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle p-2 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fs-6 fw-bold text-danger mb-0" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                                <p class="text-secondary fs-xs mb-0">This action cannot be undone.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        <p class="text-secondary fs-sm mb-3">
                            Are you sure you want to delete your profile account? Once deleted, you will be permanently logged out and your account record will be erased.
                        </p>
                        <div class="mb-3">
                            <label for="delete_account_password" class="form-label fs-xs fw-semibold text-dark mb-1">
                                Please enter your password to confirm:
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="delete_account_password" 
                                   class="form-control form-control-sm @if($errors->userDeletion->has('password')) is-invalid @endif" 
                                   placeholder="Your current password" 
                                   required>
                            @if ($errors->userDeletion->has('password'))
                                <div class="invalid-feedback fs-xs">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1.5">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                            <span>Permanently Delete</span>
                        </button>
                    </div>
                </form>
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

            // =========================================================================
            // Interactive Profile Photo Upload, Crop & Preview System
            // =========================================================================
            (function () {
                const bannerTrigger = document.getElementById('bannerAvatarTrigger');
                const avatarFileInput = document.getElementById('avatarFileInput');
                const avatarCropModalEl = document.getElementById('avatarCropModal');
                const cropCanvas = document.getElementById('cropCanvas');
                const cropZoomRange = document.getElementById('cropZoomRange');
                const cropZoomInBtn = document.getElementById('cropZoomInBtn');
                const cropZoomOutBtn = document.getElementById('cropZoomOutBtn');
                const cropRotateLeftBtn = document.getElementById('cropRotateLeftBtn');
                const cropRotateRightBtn = document.getElementById('cropRotateRightBtn');
                const cropResetBtn = document.getElementById('cropResetBtn');
                const btnSaveAvatarCrop = document.getElementById('btnSaveAvatarCrop');
                const saveAvatarSpinner = document.getElementById('saveAvatarSpinner');
                const saveAvatarCheckIcon = document.getElementById('saveAvatarCheckIcon');
                const saveAvatarText = document.getElementById('saveAvatarText');

                const previewLarge = document.getElementById('cropPreviewLarge');
                const previewSmall = document.getElementById('cropPreviewSmall');
                const previewXs = document.getElementById('cropPreviewXs');

                let modalInstance = null;

                function hideCropModal() {
                    if (modalInstance) {
                        modalInstance.hide();
                    } else if (avatarCropModalEl && window.bootstrap && window.bootstrap.Modal) {
                        const inst = window.bootstrap.Modal.getInstance(avatarCropModalEl);
                        if (inst) inst.hide();
                    }
                }

                let currentImage = null;
                let imageWidth = 0;
                let imageHeight = 0;

                // Crop geometry constants
                const canvasSize = 340;
                const cropDiameter = 260;
                const cropRadius = cropDiameter / 2;
                const cropCenter = canvasSize / 2; // 170

                let baseScale = 1;
                let scale = 1;
                let posX = 0;
                let posY = 0;
                let rotation = 0;

                let isDragging = false;
                let startX = 0;
                let startY = 0;

                function triggerFilePicker() {
                    if (avatarFileInput) {
                        avatarFileInput.value = '';
                        avatarFileInput.click();
                    }
                }

                const btnDropdownUploadAvatar = document.getElementById('btnDropdownUploadAvatar');
                if (btnDropdownUploadAvatar) btnDropdownUploadAvatar.addEventListener('click', triggerFilePicker);

                if (avatarFileInput) {
                    avatarFileInput.addEventListener('change', function (e) {
                        const file = e.target.files && e.target.files[0];
                        if (!file) return;

                        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                        if (!validTypes.includes(file.type)) {
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'error', title: 'Please select a valid image (JPG, PNG, WebP, or GIF).' });
                            } else if (window.Swal) {
                                window.Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Please select a valid image (JPG, PNG, WebP, or GIF).' });
                            }
                            return;
                        }

                        if (file.size > 5 * 1024 * 1024) {
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'error', title: 'Image file size must be less than 5MB.' });
                            } else if (window.Swal) {
                                window.Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Image file size must be less than 5MB.' });
                            }
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function (evt) {
                            const img = new Image();
                            img.onload = function () {
                                currentImage = img;
                                imageWidth = img.naturalWidth || img.width;
                                imageHeight = img.naturalHeight || img.height;

                                const minDim = Math.min(imageWidth, imageHeight);
                                baseScale = cropDiameter / minDim;

                                scale = 1;
                                posX = 0;
                                posY = 0;
                                rotation = 0;
                                if (cropZoomRange) cropZoomRange.value = 1;

                                if (!modalInstance && window.bootstrap && window.bootstrap.Modal) {
                                    modalInstance = window.bootstrap.Modal.getOrCreateInstance(avatarCropModalEl);
                                }
                                if (modalInstance) {
                                    modalInstance.show();
                                }

                                setTimeout(drawWorkspace, 120);
                            };
                            img.src = evt.target.result;
                        };
                        reader.readAsDataURL(file);
                    });
                }

                function drawWorkspace() {
                    if (!currentImage || !cropCanvas) return;

                    const ctx = cropCanvas.getContext('2d');
                    ctx.clearRect(0, 0, canvasSize, canvasSize);

                    // 1. Draw transformed image
                    ctx.save();
                    ctx.translate(cropCenter + posX, cropCenter + posY);
                    ctx.rotate((rotation * Math.PI) / 180);
                    const effectiveScale = baseScale * scale;
                    ctx.scale(effectiveScale, effectiveScale);
                    ctx.drawImage(currentImage, -imageWidth / 2, -imageHeight / 2);
                    ctx.restore();

                    // 2. Draw circular crop mask
                    ctx.save();
                    ctx.fillStyle = 'rgba(15, 23, 42, 0.65)';
                    ctx.beginPath();
                    ctx.rect(0, 0, canvasSize, canvasSize);
                    ctx.arc(cropCenter, cropCenter, cropRadius, 0, Math.PI * 2, true);
                    ctx.fill();

                    // 3. Draw circle border guide
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 2;
                    ctx.setLineDash([6, 4]);
                    ctx.beginPath();
                    ctx.arc(cropCenter, cropCenter, cropRadius, 0, Math.PI * 2);
                    ctx.stroke();
                    ctx.restore();

                    // Update live previews
                    drawPreview(previewLarge, 120);
                    drawPreview(previewSmall, 44);
                    drawPreview(previewXs, 30);
                }

                function drawPreview(canvas, targetSize) {
                    if (!canvas || !currentImage) return;
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, targetSize, targetSize);

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(targetSize / 2, targetSize / 2, targetSize / 2, 0, Math.PI * 2);
                    ctx.clip();

                    const previewRatio = targetSize / cropDiameter;
                    ctx.translate(targetSize / 2 + posX * previewRatio, targetSize / 2 + posY * previewRatio);
                    ctx.rotate((rotation * Math.PI) / 180);
                    const effectiveScale = baseScale * scale * previewRatio;
                    ctx.scale(effectiveScale, effectiveScale);
                    ctx.drawImage(currentImage, -imageWidth / 2, -imageHeight / 2);
                    ctx.restore();
                }

                // Drag & Pan handlers
                if (cropCanvas) {
                    cropCanvas.addEventListener('mousedown', function (e) {
                        isDragging = true;
                        startX = e.clientX - posX;
                        startY = e.clientY - posY;
                        cropCanvas.style.cursor = 'grabbing';
                    });

                    window.addEventListener('mousemove', function (e) {
                        if (!isDragging) return;
                        posX = e.clientX - startX;
                        posY = e.clientY - startY;
                        drawWorkspace();
                    });

                    window.addEventListener('mouseup', function () {
                        if (isDragging) {
                            isDragging = false;
                            cropCanvas.style.cursor = 'grab';
                        }
                    });

                    cropCanvas.addEventListener('touchstart', function (e) {
                        if (e.touches.length === 1) {
                            isDragging = true;
                            startX = e.touches[0].clientX - posX;
                            startY = e.touches[0].clientY - posY;
                        }
                    }, { passive: true });

                    cropCanvas.addEventListener('touchmove', function (e) {
                        if (!isDragging || e.touches.length !== 1) return;
                        posX = e.touches[0].clientX - startX;
                        posY = e.touches[0].clientY - startY;
                        drawWorkspace();
                    }, { passive: true });

                    cropCanvas.addEventListener('touchend', function () {
                        isDragging = false;
                    });

                    cropCanvas.addEventListener('wheel', function (e) {
                        e.preventDefault();
                        const delta = e.deltaY > 0 ? -0.06 : 0.06;
                        let nextScale = Math.min(3, Math.max(0.5, scale + delta));
                        scale = parseFloat(nextScale.toFixed(2));
                        if (cropZoomRange) cropZoomRange.value = scale;
                        drawWorkspace();
                    }, { passive: false });
                }

                if (cropZoomRange) {
                    cropZoomRange.addEventListener('input', function () {
                        scale = parseFloat(this.value);
                        drawWorkspace();
                    });
                }

                if (cropZoomInBtn) {
                    cropZoomInBtn.addEventListener('click', function () {
                        scale = Math.min(3, parseFloat((scale + 0.1).toFixed(2)));
                        if (cropZoomRange) cropZoomRange.value = scale;
                        drawWorkspace();
                    });
                }

                if (cropZoomOutBtn) {
                    cropZoomOutBtn.addEventListener('click', function () {
                        scale = Math.max(0.5, parseFloat((scale - 0.1).toFixed(2)));
                        if (cropZoomRange) cropZoomRange.value = scale;
                        drawWorkspace();
                    });
                }

                if (cropRotateLeftBtn) {
                    cropRotateLeftBtn.addEventListener('click', function () {
                        rotation = (rotation - 90 + 360) % 360;
                        drawWorkspace();
                    });
                }

                if (cropRotateRightBtn) {
                    cropRotateRightBtn.addEventListener('click', function () {
                        rotation = (rotation + 90) % 360;
                        drawWorkspace();
                    });
                }

                if (cropResetBtn) {
                    cropResetBtn.addEventListener('click', function () {
                        scale = 1;
                        posX = 0;
                        posY = 0;
                        rotation = 0;
                        if (cropZoomRange) cropZoomRange.value = 1;
                        drawWorkspace();
                    });
                }

                // Save Cropped Avatar
                if (btnSaveAvatarCrop) {
                    btnSaveAvatarCrop.addEventListener('click', function () {
                        if (!currentImage) return;

                        const exportSize = 400;
                        const exportCanvas = document.createElement('canvas');
                        exportCanvas.width = exportSize;
                        exportCanvas.height = exportSize;
                        const expCtx = exportCanvas.getContext('2d');

                        const exportRatio = exportSize / cropDiameter;
                        expCtx.translate(exportSize / 2 + posX * exportRatio, exportSize / 2 + posY * exportRatio);
                        expCtx.rotate((rotation * Math.PI) / 180);
                        const effectiveScale = baseScale * scale * exportRatio;
                        expCtx.scale(effectiveScale, effectiveScale);
                        expCtx.drawImage(currentImage, -imageWidth / 2, -imageHeight / 2);

                        btnSaveAvatarCrop.disabled = true;
                        if (saveAvatarSpinner) saveAvatarSpinner.classList.remove('d-none');
                        if (saveAvatarCheckIcon) saveAvatarCheckIcon.classList.add('d-none');
                        if (saveAvatarText) saveAvatarText.textContent = 'Saving...';

                        const dataUrl = exportCanvas.toDataURL('image/jpeg', 0.92);
                        const formData = new FormData();
                        formData.append('avatar_data', dataUrl);

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        fetch('{{ route("profile.avatar.update", [], false) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(res => {
                            return res.json().then(data => {
                                if (!res.ok) {
                                    throw new Error(data.message || ('Server error ' + res.status));
                                }
                                return data;
                            });
                        })
                        .then(data => {
                            btnSaveAvatarCrop.disabled = false;
                            if (saveAvatarSpinner) saveAvatarSpinner.classList.add('d-none');
                            if (saveAvatarCheckIcon) saveAvatarCheckIcon.classList.remove('d-none');
                            if (saveAvatarText) saveAvatarText.textContent = 'Save & Apply Photo';

                            if (data.success && data.avatar_url) {
                                const newUrl = data.avatar_url + '?t=' + Date.now();
                                updateAllAvatars(newUrl);

                                if (typeof hideCropModal === 'function') {
                                    hideCropModal();
                                } else if (modalInstance) {
                                    modalInstance.hide();
                                }

                                if (window.Toast) {
                                    window.Toast.fire({
                                        icon: 'success',
                                        title: 'Profile photo updated successfully!'
                                    });
                                } else if (window.Swal) {
                                    window.Swal.fire({
                                        icon: 'success',
                                        title: 'Profile Photo Updated',
                                        text: 'Your new photo has been saved to storage and applied.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            } else {
                                if (window.Toast) {
                                    window.Toast.fire({
                                        icon: 'error',
                                        title: data.message || 'Failed to update avatar.'
                                    });
                                } else if (window.Swal) {
                                    window.Swal.fire({
                                        icon: 'error',
                                        title: 'Update Failed',
                                        text: data.message || 'Failed to update avatar.'
                                    });
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Avatar upload error:', err);
                            btnSaveAvatarCrop.disabled = false;
                            if (saveAvatarSpinner) saveAvatarSpinner.classList.add('d-none');
                            if (saveAvatarCheckIcon) saveAvatarCheckIcon.classList.remove('d-none');
                            if (saveAvatarText) saveAvatarText.textContent = 'Save & Apply Photo';
                            if (window.Toast) {
                                window.Toast.fire({
                                    icon: 'error',
                                    title: err.message || 'An error occurred while uploading your avatar.'
                                });
                            } else if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Upload Error',
                                    text: err.message || 'An error occurred while uploading your avatar.'
                                });
                            }
                        });
                    });
                }

                // Remove Avatar Handlers (Supports Header Banner, Personal Info Tab, and Modal Footer)
                function handleAvatarRemoval() {
                    const doRemove = function () {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        fetch('{{ route("profile.avatar.destroy", [], false) }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => {
                            return res.json().then(data => {
                                if (!res.ok) {
                                    throw new Error(data.message || ('Server error ' + res.status));
                                }
                                return data;
                            });
                        })
                        .then(data => {
                            if (data.success) {
                                revertAllAvatarsToInitials('{{ $initials }}');
                                document.querySelectorAll('.btn-remove-avatar-trigger, .btn-remove-avatar-item').forEach(el => {
                                    el.classList.add('d-none');
                                });
                                if (typeof hideCropModal === 'function') {
                                    hideCropModal();
                                } else if (modalInstance) {
                                    modalInstance.hide();
                                }
                                if (window.Toast) {
                                    window.Toast.fire({
                                        icon: 'success',
                                        title: 'Profile photo removed successfully.'
                                    });
                                } else if (window.Swal) {
                                    window.Swal.fire({
                                        icon: 'success',
                                        title: 'Photo Removed',
                                        text: 'Your custom profile photo has been removed and initials restored.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            } else {
                                if (window.Toast) {
                                    window.Toast.fire({
                                        icon: 'error',
                                        title: data.message || 'Failed to remove photo.'
                                    });
                                } else if (window.Swal) {
                                    window.Swal.fire({
                                        icon: 'error',
                                        title: 'Remove Failed',
                                        text: data.message || 'Failed to remove photo.'
                                    });
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Avatar delete error:', err);
                            if (window.Toast) {
                                window.Toast.fire({
                                    icon: 'error',
                                    title: err.message || 'Failed to remove photo.'
                                });
                            } else if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: err.message || 'Failed to remove photo.'
                                });
                            }
                        });
                    };

                    if (window.Swal) {
                        window.Swal.fire({
                            title: 'Remove Profile Photo?',
                            text: 'Your custom avatar will be deleted and initials will be used.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, remove',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#dc3545'
                        }).then(result => {
                            if (result.isConfirmed) {
                                doRemove();
                            }
                        });
                    } else {
                        doRemove();
                    }
                }

                document.querySelectorAll('.btn-remove-avatar-trigger').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        handleAvatarRemoval();
                    });
                });

                function updateAllAvatars(url) {
                    document.querySelectorAll('.profile-avatar-display').forEach(wrapper => {
                        let img = wrapper.querySelector('img');
                        const initials = wrapper.querySelector('.user-avatar-initials');
                        if (initials) initials.remove();
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'w-100 h-100 object-fit-cover user-avatar-image';
                            wrapper.appendChild(img);
                        }
                        img.src = url;
                    });

                    document.querySelectorAll('.topbar-user-avatar, .topbar-menu-avatar, .sidebar-user-avatar, .sidebar-menu-avatar').forEach(wrapper => {
                        let img = wrapper.querySelector('.user-avatar-preview');
                        const txt = wrapper.querySelector('.user-initials-text');
                        if (txt) txt.style.display = 'none';
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'w-100 h-100 object-fit-cover rounded-circle user-avatar-preview';
                            wrapper.appendChild(img);
                        }
                        img.style.display = 'block';
                        img.src = url;
                    });

                    document.querySelectorAll('.btn-remove-avatar-trigger, .btn-remove-avatar-item').forEach(el => {
                        el.classList.remove('d-none');
                    });
                }

                function revertAllAvatarsToInitials(initials) {
                    document.querySelectorAll('.profile-avatar-display').forEach(wrapper => {
                        const img = wrapper.querySelector('img');
                        if (img) img.remove();
                        let span = wrapper.querySelector('.user-avatar-initials');
                        if (!span) {
                            span = document.createElement('span');
                            span.className = 'user-avatar-initials';
                            wrapper.appendChild(span);
                        }
                        span.textContent = initials;
                    });

                    document.querySelectorAll('.topbar-user-avatar, .topbar-menu-avatar, .sidebar-user-avatar, .sidebar-menu-avatar').forEach(wrapper => {
                        const img = wrapper.querySelector('.user-avatar-preview');
                        if (img) img.remove();
                        const txt = wrapper.querySelector('.user-initials-text');
                        if (txt) {
                            txt.style.display = 'inline';
                        } else {
                            const span = document.createElement('span');
                            span.className = 'user-initials-text';
                            span.textContent = initials;
                            wrapper.appendChild(span);
                        }
                    });

                    document.querySelectorAll('.btn-remove-avatar-trigger, .btn-remove-avatar-item').forEach(el => {
                        el.classList.add('d-none');
                    });
                }

                @if ($errors->userDeletion->isNotEmpty())
                    setTimeout(function () {
                        const deleteModalEl = document.getElementById('deleteAccountModal');
                        if (deleteModalEl && window.bootstrap && window.bootstrap.Modal) {
                            window.bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
                        }
                    }, 150);
                @endif
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
            .avatar-badge-wrapper {
                cursor: pointer;
                border-radius: 50%;
            }
            .avatar-hover-overlay {
                position: absolute;
                inset: 0;
                background: rgba(15, 23, 42, 0.55);
                opacity: 0;
                transition: opacity 0.2s ease;
                border-radius: 50%;
                pointer-events: none;
            }
            .avatar-badge-wrapper:hover .avatar-hover-overlay {
                opacity: 1;
            }
            .crop-workspace-container {
                user-select: none;
                touch-action: none;
            }
        </style>
    @endpush
</x-app-layout>
