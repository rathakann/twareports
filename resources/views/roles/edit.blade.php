<x-app-layout title="Edit Role {{ $role->name }} · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Access Control</span>
        <span>/</span>
        <a href="{{ route('roles.index') }}" class="text-secondary text-decoration-none">Role Management</a>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Edit Role</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Edit Role: {{ $role->name }}"
        section="Access Control"
        icon="shield-alert"
        description="Modify role title and update authorized permission capabilities.">
        <x-slot:actions>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Back to Roles</span>
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

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h2 class="h6 fw-bold mb-0">Role Details</h2>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-semibold text-secondary">Role Name</label>
                        <div class="input-icon-left">
                            <i class="input-icon-addon" data-lucide="shield"></i>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $role->name) }}"
                                {{ $role->name === 'Super Admin' ? 'readonly' : '' }} required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if ($role->name === 'Super Admin')
                            <small class="text-muted d-block mt-1">The primary Super Admin role cannot be renamed.</small>
                        @endif
                    </div>
                    <div class="col-md-6">
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
                                            <option value="{{ $route }}" data-permission="{{ $reqPermStr }}" {{ old('landing_page', $role->landing_page) === $route ? 'selected' : '' }}>
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
                            <span>The selected landing page requires the <strong id="landing-page-required-perm"></strong> permission. Please grant this permission in the matrix below to allow assignment.</span>
                        </div>
                        <small class="text-muted d-block mt-1">Users with this role will be routed to this page upon login unless overridden on their user profile.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-icon text-secondary p-0 border-0 btn-matrix-collapse"
                        data-bs-toggle="collapse" data-bs-target="#matrixCardCollapse" aria-expanded="true"
                        aria-controls="matrixCardCollapse" id="btn-matrix-card-collapse" title="Collapse matrix">
                        <i data-lucide="chevron-down" class="matrix-card-chevron transition-transform" style="width: 20px; height: 20px;"></i>
                    </button>
                    <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2 cursor-pointer"
                        data-bs-toggle="collapse" data-bs-target="#matrixCardCollapse" role="button" aria-expanded="true">
                        <i data-lucide="key" style="width: 18px; height: 18px;" class="text-primary"></i>
                        <span>Permission Capabilities Matrix</span>
                    </h2>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1" style="font-size: 11px;">
                        {{ count($permissions) }} Modules
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" id="btn-toggle-collapse-all" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" data-state="collapsed">
                        <i data-lucide="chevrons-down" id="icon-collapse-all" style="width: 14px; height: 14px;"></i>
                        <span id="text-collapse-all">Expand All</span>
                    </button>
                    <button type="button" id="btn-toggle-all-global" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                        <i data-lucide="check-square" style="width: 14px; height: 14px;"></i>
                        <span>Select All</span>
                    </button>
                    <button type="button" id="btn-clear-all-global" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                        <i data-lucide="square" style="width: 14px; height: 14px;"></i>
                        <span>Clear All</span>
                    </button>
                </div>
            </div>
            <div class="collapse show" id="matrixCardCollapse">
                <div class="card-body p-4">
                    @php
                        $currentRolePerms = old('permissions', $rolePermissions);
                        $columns = [[], []];
                        $idx = 0;
                        foreach ($permissions as $mod => $perms) {
                            $columns[$idx % 2][$mod] = $perms;
                            $idx++;
                        }
                    @endphp
                    <div class="row g-3 align-items-start">
                        @foreach ($columns as $columnModules)
                            <div class="col-md-6 col-12 d-flex flex-column gap-3">
                                @foreach ($columnModules as $module => $modulePermissions)
                                    @php($moduleSlug = \Illuminate\Support\Str::slug($module))
                                    <div class="module-card border rounded-3 overflow-hidden" id="module_card_{{ $moduleSlug }}">
                                        <!-- Collapsible Module Header with Wide Click Area -->
                                        <div class="module-header-bar collapsed d-flex justify-content-between align-items-center p-3 user-select-none">
                                            <!-- Module Title & Toggle Trigger (Excludes the Select All Switch) -->
                                            <div class="module-header-trigger collapsed d-flex align-items-center gap-2.5 flex-grow-1 cursor-pointer py-1"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse_module_{{ $moduleSlug }}"
                                                aria-expanded="false"
                                                role="button">
                                                <i data-lucide="chevron-down" class="module-collapse-icon text-secondary" style="width: 18px; height: 18px;"></i>
                                                <h3 class="h6 fw-bold text-uppercase text-secondary mb-0" style="font-size: 13px; letter-spacing: 0.5px;">
                                                    {{ $module }}
                                                </h3>
                                                <span class="badge module-count-badge" id="badge_module_{{ $moduleSlug }}">
                                                    0 / {{ count($modulePermissions) }}
                                                </span>
                                            </div>

                                            <!-- Isolated Module Select-All Switch -->
                                            <div class="module-select-all-box ms-auto" onclick="event.stopPropagation()">
                                                <div class="switch-field mb-0" onclick="event.stopPropagation()">
                                                    <input class="switch-field-input module-select-all" type="checkbox" id="module_all_{{ $moduleSlug }}"
                                                        data-module="{{ $moduleSlug }}" onclick="event.stopPropagation()">
                                                    <label class="switch-field-control mb-0" for="module_all_{{ $moduleSlug }}" onclick="event.stopPropagation()">
                                                        <span class="switch-field-thumb"></span>
                                                    </label>
                                                </div>
                                                <label class="switch-field-label" for="module_all_{{ $moduleSlug }}" onclick="event.stopPropagation()">
                                                    Select all
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Collapsible Module Body -->
                                        <div class="collapse module-collapse-body" id="collapse_module_{{ $moduleSlug }}">
                                            <div class="border-top p-3 bg-light bg-opacity-25" style="border-color: var(--bdr) !important;">
                                                <div class="d-flex flex-column gap-2">
                                                    @foreach ($modulePermissions as $permission)
                                                        <div class="switch-field p-2.5 rounded-2 bg-white border d-flex align-items-center gap-2">
                                                            <input class="switch-field-input perm-checkbox perm-module-{{ $moduleSlug }}" type="checkbox"
                                                                name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                                                data-module="{{ $moduleSlug }}"
                                                                {{ in_array($permission->name, $currentRolePerms) ? 'checked' : '' }}>
                                                            <label class="switch-field-control mb-0" for="perm_{{ $permission->id }}">
                                                                <span class="switch-field-thumb"></span>
                                                            </label>
                                                            <label class="switch-field-label small fw-semibold text-dark mb-0" for="perm_{{ $permission->id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-end gap-2">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Stop clicks inside module-select-all-box from triggering collapse
            document.querySelectorAll('.module-select-all-box').forEach(box => {
                box.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            });

            // Delegate header bar clicks to trigger if not clicking select-all
            document.querySelectorAll('.module-header-bar').forEach(headerBar => {
                headerBar.addEventListener('click', function (e) {
                    if (e.target.closest('.module-select-all-box')) {
                        return;
                    }
                    if (!e.target.closest('.module-header-trigger')) {
                        const trigger = headerBar.querySelector('.module-header-trigger');
                        trigger?.click();
                    }
                });
            });

            // Module level select all
            document.querySelectorAll('.module-select-all').forEach(toggle => {
                toggle.addEventListener('change', function () {
                    const module = this.dataset.module;
                    const checkboxes = document.querySelectorAll(`.perm-module-${module}`);
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    syncModuleToggles();
                });
            });

            // Global select/clear all
            document.getElementById('btn-toggle-all-global')?.addEventListener('click', function () {
                document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
                syncModuleToggles();
            });

            document.getElementById('btn-clear-all-global')?.addEventListener('click', function () {
                document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
                syncModuleToggles();
            });

            // Expand / Collapse all modules
            const btnCollapseAll = document.getElementById('btn-toggle-collapse-all');
            const textCollapseAll = document.getElementById('text-collapse-all');

            btnCollapseAll?.addEventListener('click', function () {
                const isExpanded = this.dataset.state === 'expanded';
                const collapseElements = document.querySelectorAll('.module-collapse-body');

                collapseElements.forEach(el => {
                    const card = el.closest('.module-card');
                    const headerBar = card?.querySelector('.module-header-bar');
                    const trigger = card?.querySelector('.module-header-trigger');
                    const bsCollapse = window.bootstrap?.Collapse?.getOrCreateInstance(el, { toggle: false });

                    if (isExpanded) {
                        if (bsCollapse) {
                            bsCollapse.hide();
                        } else {
                            el.classList.remove('show');
                        }
                        headerBar?.classList.add('collapsed');
                        trigger?.classList.add('collapsed');
                        trigger?.setAttribute('aria-expanded', 'false');
                    } else {
                        if (bsCollapse) {
                            bsCollapse.show();
                        } else {
                            el.classList.add('show');
                        }
                        headerBar?.classList.remove('collapsed');
                        trigger?.classList.remove('collapsed');
                        trigger?.setAttribute('aria-expanded', 'true');
                    }
                });

                if (isExpanded) {
                    this.dataset.state = 'collapsed';
                    textCollapseAll.textContent = 'Expand All';
                    const icon = this.querySelector('#icon-collapse-all') || this.querySelector('svg') || this.querySelector('i');
                    if (icon) {
                        icon.outerHTML = '<i data-lucide="chevrons-down" id="icon-collapse-all" style="width: 14px; height: 14px;"></i>';
                    }
                } else {
                    this.dataset.state = 'expanded';
                    textCollapseAll.textContent = 'Collapse All';
                    const icon = this.querySelector('#icon-collapse-all') || this.querySelector('svg') || this.querySelector('i');
                    if (icon) {
                        icon.outerHTML = '<i data-lucide="chevrons-up" id="icon-collapse-all" style="width: 14px; height: 14px;"></i>';
                    }
                }

                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });

            // Sync chevron states for individual module collapsibles
            document.querySelectorAll('.module-collapse-body').forEach(bodyEl => {
                const card = bodyEl.closest('.module-card');
                const headerBar = card?.querySelector('.module-header-bar');
                const trigger = card?.querySelector('.module-header-trigger');

                bodyEl.addEventListener('show.bs.collapse', function () {
                    headerBar?.classList.remove('collapsed');
                    trigger?.classList.remove('collapsed');
                    trigger?.setAttribute('aria-expanded', 'true');
                });
                bodyEl.addEventListener('hide.bs.collapse', function () {
                    headerBar?.classList.add('collapsed');
                    trigger?.classList.add('collapsed');
                    trigger?.setAttribute('aria-expanded', 'false');
                });
            });

            // Sync card-level chevron
            const matrixCardCollapse = document.getElementById('matrixCardCollapse');
            const btnCardCollapse = document.getElementById('btn-matrix-card-collapse');
            matrixCardCollapse?.addEventListener('show.bs.collapse', function () {
                btnCardCollapse?.classList.remove('collapsed');
            });
            matrixCardCollapse?.addEventListener('hide.bs.collapse', function () {
                btnCardCollapse?.classList.add('collapsed');
            });

            // Update module checkbox and dynamic badges
            function syncModuleToggles() {
                document.querySelectorAll('.module-select-all').forEach(toggle => {
                    const module = toggle.dataset.module;
                    const checkboxes = document.querySelectorAll(`.perm-module-${module}`);
                    const total = checkboxes.length;
                    const checked = Array.from(checkboxes).filter(cb => cb.checked).length;

                    toggle.checked = total > 0 && checked === total;

                    const badge = document.getElementById(`badge_module_${module}`);
                    if (badge) {
                        badge.textContent = `${checked} / ${total}`;
                        if (checked === total && total > 0) {
                            badge.className = 'badge module-count-badge bg-success bg-opacity-10 text-success';
                        } else if (checked > 0) {
                            badge.className = 'badge module-count-badge bg-primary bg-opacity-10 text-primary';
                        } else {
                            badge.className = 'badge module-count-badge bg-secondary bg-opacity-10 text-secondary';
                        }
                    }
                });
            }

            // Landing page permission validation
            const landingPageSelect = document.getElementById('landing_page');
            const landingPageAlert = document.getElementById('landing-page-permission-alert');
            const landingPagePermText = document.getElementById('landing-page-required-perm');
            const roleNameInput = document.getElementById('name');
            const roleForm = document.querySelector('form[action="{{ route('roles.update', $role) }}"]');

            function validateLandingPagePermission() {
                if (!landingPageSelect || !landingPageAlert) {
                    return true;
                }

                const selectedOption = landingPageSelect.options[landingPageSelect.selectedIndex];
                const required = selectedOption ? (selectedOption.dataset.permission || '') : '';
                const roleName = (roleNameInput ? roleNameInput.value : '').trim().toLowerCase();

                if (!required || roleName === 'super admin') {
                    landingPageAlert.classList.add('d-none');
                    landingPageAlert.classList.remove('d-flex');
                    landingPageSelect.classList.remove('is-invalid');
                    return true;
                }

                const options = required.split('|').map(s => s.trim()).filter(Boolean);
                const checkedBoxes = document.querySelectorAll('.perm-checkbox:checked');
                const checkedValues = new Set(Array.from(checkedBoxes).map(cb => cb.value));

                const hasRequired = options.some(opt => checkedValues.has(opt));

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
                landingPageSelect.addEventListener('change', validateLandingPagePermission);
                if (window.jQuery) {
                    window.jQuery(landingPageSelect).on('change', validateLandingPagePermission);
                }
            }
            if (roleNameInput) {
                roleNameInput.addEventListener('input', validateLandingPagePermission);
            }

            roleForm?.addEventListener('submit', function (e) {
                if (!validateLandingPagePermission()) {
                    e.preventDefault();
                    landingPageSelect?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    landingPageSelect?.focus();
                }
            });

            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    syncModuleToggles();
                    validateLandingPagePermission();
                });
            });

            syncModuleToggles();
            validateLandingPagePermission();
        });
    </script>
</x-app-layout>
