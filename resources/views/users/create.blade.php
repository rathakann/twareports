<x-app-layout title="Create User · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Access Control</span>
        <span>/</span>
        <a href="{{ route('users.index') }}" class="text-secondary text-decoration-none">User Management</a>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Create User</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Create New User"
        section="Access Control"
        icon="user-plus"
        description="Provision a new user account and assign authorized roles.">
        <x-slot:actions>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Back to Users</span>
            </a>
        </x-slot:actions>
    </x-management.page-header>

    @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 rounded-3 mb-4">
            <div class="fw-semibold mb-1">Please correct the errors below:</div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- Account Details -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="user" style="width: 18px; height: 18px;" class="text-primary"></i>
                            <span>Account Details</span>
                        </h2>
                    </div>
                    <div class="card-body p-4 d-flex flex-column gap-3">
                        <div>
                            <label for="name" class="form-label small fw-semibold text-secondary">Full Name</label>
                            <div class="input-icon-left">
                                <i class="input-icon-addon" data-lucide="user"></i>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" placeholder="e.g. Jane Doe" required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
                            <div class="input-icon-left">
                                <i class="input-icon-addon" data-lucide="mail"></i>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="e.g. jane@twareports.test" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="form-label small fw-semibold text-secondary">Password</label>
                            <div class="input-icon-left">
                                <i class="input-icon-addon" data-lucide="lock"></i>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimum 8 characters" required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="form-label small fw-semibold text-secondary">Account Status</label>
                            <div class="input-icon-left">
                                <i class="input-icon-addon" data-lucide="activity"></i>
                                <select name="status" id="status" class="form-select js-select2 @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="landing_page" class="form-label small fw-semibold text-secondary d-flex justify-content-between align-items-center">
                                <span>Default Landing Page</span>
                                <span class="text-muted fw-normal" style="font-size: 11px;">Page visited upon login</span>
                            </label>
                            <div class="input-icon-left">
                                <i class="input-icon-addon" data-lucide="compass"></i>
                                <select name="landing_page" id="landing_page" class="form-select js-select2 @error('landing_page') is-invalid @enderror">
                                    <option value="" data-permission="">Default (Dashboard)</option>
                                    @foreach ($landingPageOptions as $group => $options)
                                        <optgroup label="{{ $group }}">
                                            @foreach ($options as $route => $label)
                                                @php
                                                    $reqPerm = \App\Support\Navigation::getRequiredPermissionForRoute($route);
                                                    $reqPermStr = is_array($reqPerm) ? implode('|', $reqPerm) : ($reqPerm ?? '');
                                                @endphp
                                                <option value="{{ $route }}" data-permission="{{ $reqPermStr }}" {{ old('landing_page') === $route ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            @error('landing_page')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="landing-page-permission-alert" class="alert alert-warning py-2 px-3 mt-2 rounded-2 small d-none align-items-center gap-2 mb-0">
                                <i data-lucide="shield-alert" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                                <span>The selected landing page requires the <strong id="landing-page-required-perm"></strong> permission. Please assign a role with this capability.</span>
                            </div>
                            <small class="text-muted d-block mt-1">If not defined, logging in always brings this user to the Dashboard.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Assignment -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="shield-check" style="width: 18px; height: 18px;" class="text-primary"></i>
                            <span>Assign Roles</span>
                        </h2>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 11px;">
                            Role-Based Access
                        </span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column gap-3">
                        <p class="text-secondary small mb-1">
                            User permissions and system access rights are managed entirely through roles. Assign one or more roles to this user.
                        </p>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($roles as $role)
                                <div class="switch-field p-2 rounded-2 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="switch-field-input role-checkbox" type="checkbox" name="roles[]"
                                            value="{{ $role->name }}" id="role_{{ $role->id }}"
                                            data-permissions='@json($role->permissions->pluck("name"))'
                                            {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                        <label class="switch-field-control mb-0" for="role_{{ $role->id }}">
                                            <span class="switch-field-thumb"></span>
                                        </label>
                                        <label class="switch-field-label fw-semibold text-dark mb-0" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 11px;">
                                        {{ $role->permissions->count() }} permissions
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                <span>Create User</span>
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');
            const landingPageSelect = document.getElementById('landing_page');
            const landingPageAlert = document.getElementById('landing-page-permission-alert');
            const landingPagePermText = document.getElementById('landing-page-required-perm');
            const userForm = document.querySelector('form[action="{{ route('users.store') }}"]');

            function getActivePermissions() {
                const permissions = new Set();
                let isSuperAdmin = false;

                roleCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        if (cb.value === 'Super Admin') {
                            isSuperAdmin = true;
                        }
                        try {
                            const perms = JSON.parse(cb.dataset.permissions || '[]');
                            perms.forEach(p => permissions.add(p));
                        } catch (e) {}
                    }
                });

                return { permissions, isSuperAdmin };
            }

            function checkLandingPagePermission() {
                if (!landingPageSelect || !landingPageAlert) {
                    return true;
                }

                const selectedOption = landingPageSelect.options[landingPageSelect.selectedIndex];
                const required = selectedOption ? (selectedOption.dataset.permission || '') : '';
                const { permissions, isSuperAdmin } = getActivePermissions();

                if (!required || isSuperAdmin) {
                    landingPageAlert.classList.add('d-none');
                    landingPageAlert.classList.remove('d-flex');
                    landingPageSelect.classList.remove('is-invalid');
                    return true;
                }

                const options = required.split('|').map(s => s.trim()).filter(Boolean);
                const hasRequired = options.some(opt => permissions.has(opt));

                if (hasRequired) {
                    landingPageAlert.classList.add('d-none');
                    landingPageAlert.classList.remove('d-flex');
                    landingPageSelect.classList.remove('is-invalid');
                    return true;
                } else {
                    if (landingPagePermText) {
                        landingPagePermText.textContent = options.join(' or ');
                    }
                    landingPageAlert.classList.remove('d-none');
                    landingPageAlert.classList.add('d-flex');
                    landingPageSelect.classList.add('is-invalid');
                    return false;
                }
            }

            if (landingPageSelect) {
                landingPageSelect.addEventListener('change', checkLandingPagePermission);
                if (window.jQuery) {
                    window.jQuery(landingPageSelect).on('change', checkLandingPagePermission);
                }
            }

            roleCheckboxes.forEach(cb => {
                cb.addEventListener('change', checkLandingPagePermission);
            });

            userForm?.addEventListener('submit', function (e) {
                if (!checkLandingPagePermission()) {
                    e.preventDefault();
                    landingPageSelect?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    landingPageSelect?.focus();
                }
            });

            checkLandingPagePermission();
        });
    </script>
</x-app-layout>
