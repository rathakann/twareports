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
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="key" style="width: 18px; height: 18px;" class="text-primary"></i>
                    <span>Permission Capabilities Matrix</span>
                </h2>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btn-toggle-all-global" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                        <i data-lucide="check-square" style="width: 14px; height: 14px;"></i>
                        <span>Select All Permissions</span>
                    </button>
                    <button type="button" id="btn-clear-all-global" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                        <i data-lucide="square" style="width: 14px; height: 14px;"></i>
                        <span>Clear All</span>
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @php($currentRolePerms = old('permissions', $rolePermissions))
                    @foreach ($permissions as $module => $modulePermissions)
                        <div class="col-md-6 col-12">
                            <div class="border rounded-3 p-3 bg-light bg-opacity-25 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <h3 class="h6 fw-bold text-uppercase text-secondary mb-0" style="font-size: 13px; letter-spacing: 0.5px;">
                                        {{ $module }}
                                    </h3>
                                    <div class="switch-field mb-0">
                                        <input class="switch-field-input module-select-all" type="checkbox" id="module_all_{{ $module }}"
                                            data-module="{{ $module }}">
                                        <label class="switch-field-control mb-0" for="module_all_{{ $module }}" style="width: 34px; height: 18px;">
                                            <span class="switch-field-thumb" style="width: 12px; height: 12px; top: 3px; left: 3px;"></span>
                                        </label>
                                        <label class="switch-field-label small text-secondary mb-0" for="module_all_{{ $module }}">
                                            Select all
                                        </label>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    @foreach ($modulePermissions as $permission)
                                        <div class="switch-field p-2 rounded-2 bg-white border d-flex align-items-center gap-2">
                                            <input class="switch-field-input perm-checkbox perm-module-{{ $module }}" type="checkbox"
                                                name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                                data-module="{{ $module }}"
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
                    @endforeach
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
            document.querySelectorAll('.module-select-all').forEach(toggle => {
                toggle.addEventListener('change', function () {
                    const module = this.dataset.module;
                    const checkboxes = document.querySelectorAll(`.perm-module-${module}`);
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            });

            document.getElementById('btn-toggle-all-global')?.addEventListener('click', function () {
                document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
                document.querySelectorAll('.module-select-all').forEach(cb => cb.checked = true);
            });

            document.getElementById('btn-clear-all-global')?.addEventListener('click', function () {
                document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
                document.querySelectorAll('.module-select-all').forEach(cb => cb.checked = false);
            });

            function syncModuleToggles() {
                document.querySelectorAll('.module-select-all').forEach(toggle => {
                    const module = toggle.dataset.module;
                    const checkboxes = document.querySelectorAll(`.perm-module-${module}`);
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    toggle.checked = allChecked;
                });
            }

            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                cb.addEventListener('change', syncModuleToggles);
            });

            syncModuleToggles();
        });
    </script>
</x-app-layout>
