<x-app-layout title="Edit Module: {{ $module->label }} · Administrator · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Administrator</span>
        <span>/</span>
        <a href="{{ route('admin.modules.index') }}" class="text-secondary text-decoration-none">Modules</a>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Edit Module</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Edit Module: {{ $module->label }}"
        section="Administrator"
        icon="boxes"
        description="Update navigation attributes, route mappings, ordering, or authorization permissions.">
        <x-slot:actions>
            <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Back to Modules</span>
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

    <form action="{{ route('admin.modules.update', $module) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Left: Navigation & Display Details -->
                    <div class="col-lg-7">
                        <div class="border-bottom pb-3 mb-4">
                            <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                                <i data-lucide="layout-grid" style="width: 18px; height: 18px;" class="text-primary"></i>
                                <span>Navigation & Display Details</span>
                            </h2>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label for="group_label" class="form-label small fw-semibold text-secondary mb-0">Navigation Group <span class="text-danger">*</span></label>
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 d-inline-flex align-items-center gap-1" id="btn-toggle-new-group" style="font-size: 12px;">
                                        <i data-lucide="plus-circle" style="width: 13px; height: 13px;"></i>
                                        <span id="btn-toggle-new-group-text">+ New Group</span>
                                    </button>
                                </div>
                                <div id="group-select-container">
                                    <div class="input-icon-left">
                                        <i class="input-icon-addon" data-lucide="layers"></i>
                                        <select name="group_label" id="group_label" class="form-select @error('group_label') is-invalid @enderror" required>
                                            <option value="" disabled>Select Navigation Group</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group }}" {{ old('group_label', $module->group_label) === $group ? 'selected' : '' }}>
                                                    {{ $group }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="group-input-container" class="d-none">
                                    <div class="input-icon-left">
                                        <i class="input-icon-addon" data-lucide="plus"></i>
                                        <input type="text" id="custom_group_label" class="form-control" placeholder="Type new navigation group name (e.g. Purchasing, Warehouse)">
                                    </div>
                                </div>
                                @error('group_label')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="label" class="form-label small fw-semibold text-secondary">Menu Label <span class="text-danger">*</span></label>
                                <div class="input-icon-left">
                                    <i class="input-icon-addon" data-lucide="tag"></i>
                                    <input type="text" name="label" id="label"
                                        class="form-control @error('label') is-invalid @enderror"
                                        value="{{ old('label', $module->label) }}" placeholder="e.g. Items, Document Series, Users" required>
                                </div>
                                @error('label')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label for="icon" class="form-label small fw-semibold text-secondary">Lucide Icon Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-primary" id="icon-preview-box">
                                            <i data-lucide="{{ old('icon', $module->icon ?: 'circle') }}" style="width: 16px; height: 16px;"></i>
                                        </span>
                                        <input type="text" name="icon" id="icon"
                                            class="form-control @error('icon') is-invalid @enderror"
                                            value="{{ old('icon', $module->icon) }}" placeholder="e.g. package, scale, boxes, shield">
                                    </div>
                                    @error('icon')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label for="sort_order" class="form-label small fw-semibold text-secondary">Sort Order</label>
                                    <div class="input-icon-left">
                                        <i class="input-icon-addon" data-lucide="arrow-down-up"></i>
                                        <input type="number" name="sort_order" id="sort_order"
                                            class="form-control @error('sort_order') is-invalid @enderror"
                                            value="{{ old('sort_order', $module->sort_order) }}" placeholder="e.g. 10, 20" min="0" step="10">
                                    </div>
                                    @error('sort_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label for="parent_id" class="form-label small fw-semibold text-secondary">Parent Module (Submenu)</label>
                                    <div class="input-icon-left">
                                        <i class="input-icon-addon" data-lucide="folder-tree"></i>
                                        <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                            <option value="">— None (Top Level Menu) —</option>
                                            @foreach ($parentModules as $parent)
                                                <option value="{{ $parent->id }}" {{ old('parent_id', $module->parent_id) == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->group_label }} &rarr; {{ $parent->label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('parent_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label for="group_order" class="form-label small fw-semibold text-secondary">Group Position Order</label>
                                    <div class="input-icon-left">
                                        <i class="input-icon-addon" data-lucide="list-ordered"></i>
                                        <input type="number" name="group_order" id="group_order"
                                            class="form-control @error('group_order') is-invalid @enderror"
                                            value="{{ old('group_order', $module->group_order) }}" placeholder="e.g. 10, 20, 30" min="0" step="10">
                                    </div>
                                    @error('group_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="description" class="form-label small fw-semibold text-secondary">Description / Remarks</label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Brief explanation of module purpose and datasets...">{{ old('description', $module->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right: Routing & Access Control -->
                    <div class="col-lg-5">
                        <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
                            <h2 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                                <i data-lucide="shield-check" style="width: 18px; height: 18px;" class="text-primary"></i>
                                <span>Routing & Authorization</span>
                            </h2>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 11px;">
                                Security & URLs
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <div>
                                <label for="route" class="form-label small fw-semibold text-secondary">Laravel Named Route</label>
                                <div class="input-icon-left">
                                    <i class="input-icon-addon" data-lucide="link"></i>
                                    <input type="text" name="route" id="route"
                                        class="form-control font-monospace @error('route') is-invalid @enderror"
                                        value="{{ old('route', $module->route) }}" placeholder="e.g. master-data.items.index">
                                </div>
                                @error('route')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Recommended for internal application pages.</small>
                            </div>

                            <div>
                                <label for="url" class="form-label small fw-semibold text-secondary">Custom / External URL</label>
                                <div class="input-icon-left">
                                    <i class="input-icon-addon" data-lucide="globe"></i>
                                    <input type="text" name="url" id="url"
                                        class="form-control font-monospace @error('url') is-invalid @enderror"
                                        value="{{ old('url', $module->url) }}" placeholder="e.g. /custom-page or https://...">
                                </div>
                                @error('url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Used when linking to a path without a named route.</small>
                            </div>

                            <div>
                                <label for="permission" class="form-label small fw-semibold text-secondary">Required Permission</label>
                                <div class="input-icon-left">
                                    <i class="input-icon-addon" data-lucide="key"></i>
                                    <input type="text" name="permission" id="permission" list="permission-datalist"
                                        class="form-control font-monospace @error('permission') is-invalid @enderror"
                                        value="{{ old('permission', $module->permission) }}" placeholder="e.g. master-data.items.view">
                                </div>
                                <datalist id="permission-datalist">
                                    @foreach ($existingPermissions as $perm)
                                        <option value="{{ $perm }}">
                                    @endforeach
                                </datalist>
                                @error('permission')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Leave empty for public access (all authenticated users).</small>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="badge" class="form-label small fw-semibold text-secondary">Badge Tag</label>
                                    <div class="input-icon-left">
                                        <i class="input-icon-addon" data-lucide="bookmark"></i>
                                        <input type="text" name="badge" id="badge"
                                            class="form-control @error('badge') is-invalid @enderror"
                                            value="{{ old('badge', $module->badge) }}" placeholder="e.g. New, Beta">
                                    </div>
                                    @error('badge')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="target" class="form-label small fw-semibold text-secondary">Link Target</label>
                                    <div class="input-icon-left">
                                        <i class="input-icon-addon" data-lucide="external-link"></i>
                                        <select name="target" id="target" class="form-select @error('target') is-invalid @enderror">
                                            <option value="_self" {{ old('target', $module->target) === '_self' ? 'selected' : '' }}>Same Tab (_self)</option>
                                            <option value="_blank" {{ old('target', $module->target) === '_blank' ? 'selected' : '' }}>New Tab (_blank)</option>
                                        </select>
                                    </div>
                                    @error('target')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="switch-field p-3 rounded-2 border bg-light bg-opacity-50 d-flex align-items-center justify-content-between mt-2">
                                <div>
                                    <label class="switch-field-label fw-semibold text-dark mb-0 d-block" for="is_active">
                                        Active Module
                                    </label>
                                    <small class="text-secondary d-block">Visible in horizontal and vertical navigation bars.</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $module->is_active ? '1' : '0') ? 'checked' : '' }} style="width: 2.25rem; height: 1.25rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                    <span>Update Module</span>
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const iconInput = document.getElementById('icon');
                const iconPreviewBox = document.getElementById('icon-preview-box');

                if (iconInput && iconPreviewBox) {
                    iconInput.addEventListener('input', function () {
                        const val = this.value.trim() || 'circle';
                        iconPreviewBox.innerHTML = `<i data-lucide="${val}" style="width: 16px; height: 16px;"></i>`;
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                }

                const btnToggleNewGroup = document.getElementById('btn-toggle-new-group');
                const btnToggleText = document.getElementById('btn-toggle-new-group-text');
                const selectContainer = document.getElementById('group-select-container');
                const inputContainer = document.getElementById('group-input-container');
                const groupSelect = document.getElementById('group_label');
                const customInput = document.getElementById('custom_group_label');

                if (btnToggleNewGroup && selectContainer && inputContainer) {
                    btnToggleNewGroup.addEventListener('click', function () {
                        const isCustom = !inputContainer.classList.contains('d-none');
                        if (isCustom) {
                            inputContainer.classList.add('d-none');
                            selectContainer.classList.remove('d-none');
                            groupSelect.name = 'group_label';
                            groupSelect.required = true;
                            customInput.removeAttribute('name');
                            customInput.required = false;
                            btnToggleText.textContent = '+ New Group';
                        } else {
                            selectContainer.classList.add('d-none');
                            inputContainer.classList.remove('d-none');
                            groupSelect.removeAttribute('name');
                            groupSelect.required = false;
                            customInput.name = 'group_label';
                            customInput.required = true;
                            customInput.focus();
                            btnToggleText.textContent = '← Select Existing';
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>

