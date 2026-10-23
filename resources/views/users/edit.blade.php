<x-app-layout title="Edit User {{ $user->name }} · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Access Control</span>
        <span>/</span>
        <a href="{{ route('users.index') }}" class="text-secondary text-decoration-none">User Management</a>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Edit User</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Edit User: {{ $user->name }}"
        section="Access Control"
        icon="user-pen"
        description="Update user account credentials, change active status, and modify role and permission assignments.">
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

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Account Details -->
            <div class="col-lg-5">
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
                                    value="{{ old('name', $user->name) }}" required>
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
                                    value="{{ old('email', $user->email) }}" required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="form-label small fw-semibold text-secondary">
                                Password <span class="text-muted fw-normal">(Leave blank to keep current)</span>
                            </label>
                            <div class="input-icon-left">
                                <i class="input-icon-addon" data-lucide="lock"></i>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="••••••••">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="form-label small fw-semibold text-secondary">Account Status</label>
                            <div class="input-icon-left">
                                <i class="input-icon-addon" data-lucide="activity"></i>
                                <select name="status" id="status" class="form-select js-select2 @error('status') is-invalid @enderror"
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if ($user->id === auth()->id())
                                <input type="hidden" name="status" value="active">
                                <small class="text-muted d-block mt-1">You cannot deactivate your own account.</small>
                            @endif
                        </div>

                        <hr class="my-2 text-secondary">

                        <div>
                            <label class="form-label small fw-semibold text-secondary d-block mb-2">Assign Roles</label>
                            <div class="d-flex flex-column gap-2">
                                @php($userRoles = old('roles', $user->roles->pluck('name')->toArray()))
                                @foreach ($roles as $role)
                                    @php($isPrimarySuperAdmin = $user->id === 1 && $role->name === 'Super Admin')
                                    <div class="switch-field p-2 rounded-2 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="switch-field-input role-checkbox" type="checkbox" name="roles[]"
                                                value="{{ $role->name }}" id="role_{{ $role->id }}"
                                                data-permissions='@json($role->permissions->pluck("name"))'
                                                {{ in_array($role->name, $userRoles) ? 'checked' : '' }}
                                                {{ $isPrimarySuperAdmin ? 'checked disabled' : '' }}>
                                            <label class="switch-field-control mb-0" for="role_{{ $role->id }}">
                                                <span class="switch-field-thumb"></span>
                                            </label>
                                            <label class="switch-field-label fw-semibold text-dark mb-0" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                        @if ($isPrimarySuperAdmin)
                                            <input type="hidden" name="roles[]" value="Super Admin">
                                        @endif
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

            <!-- Granular Permissions Matrix -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                            <i data-lucide="key" style="width: 18px; height: 18px;" class="text-primary"></i>
                            <span>Direct User Permissions</span>
                        </h2>
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 11px;">
                            Inherited permissions highlighted automatically
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary small mb-3">
                            Direct permissions are granted individually to this user in addition to any permissions inherited from assigned roles.
                        </p>

                        <div class="row g-3">
                            @php($selectedDirectPerms = old('permissions', $directPermissions))
                            @foreach ($permissions as $module => $modulePermissions)
                                <div class="col-12">
                                    <div class="border rounded-3 p-3 bg-white">
                                        <h3 class="h6 fw-bold text-uppercase text-secondary mb-2" style="font-size: 12px; letter-spacing: 0.5px;">
                                            Module: {{ $module }}
                                        </h3>
                                        <div class="row g-2">
                                            @foreach ($modulePermissions as $permission)
                                                <div class="col-md-6">
                                                    <div class="switch-field permission-container p-2 rounded-2 border bg-white d-flex align-items-center justify-content-between" id="perm_wrapper_{{ $permission->name }}">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input class="switch-field-input permission-checkbox" type="checkbox" name="permissions[]"
                                                                value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                                                data-permission-name="{{ $permission->name }}"
                                                                {{ in_array($permission->name, $selectedDirectPerms) ? 'checked' : '' }}>
                                                            <label class="switch-field-control mb-0" for="perm_{{ $permission->id }}">
                                                                <span class="switch-field-thumb"></span>
                                                            </label>
                                                            <label class="switch-field-label small fw-semibold text-dark mb-0" for="perm_{{ $permission->id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary inherited-badge d-none" style="font-size: 10px;">
                                                            Inherited from Role
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');
            const permissionContainers = document.querySelectorAll('.permission-container');

            function updateInheritedPermissions() {
                const inherited = new Set();

                roleCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        try {
                            const perms = JSON.parse(cb.dataset.permissions || '[]');
                            perms.forEach(p => inherited.add(p));
                        } catch (e) {}
                    }
                });

                permissionContainers.forEach(container => {
                    const checkbox = container.querySelector('.permission-checkbox');
                    const badge = container.querySelector('.inherited-badge');
                    const permName = checkbox.dataset.permissionName;

                    if (inherited.has(permName)) {
                        badge.classList.remove('d-none');
                        container.classList.add('bg-primary', 'bg-opacity-10', 'border-primary');
                    } else {
                        badge.classList.add('d-none');
                        container.classList.remove('bg-primary', 'bg-opacity-10', 'border-primary');
                    }
                });
            }

            roleCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateInheritedPermissions);
            });

            updateInheritedPermissions();
        });
    </script>
</x-app-layout>
