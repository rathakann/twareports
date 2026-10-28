<x-app-layout title="Navigation Groups · Administrator · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Administrator</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Navigation Groups</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Navigation Groups"
        section="Administrator"
        icon="layers"
        description="Configure, rename, reorder, and scale navigation groups dynamically across horizontal and sidebar menus.">
        <x-slot:actions>
            <a href="{{ route('admin.modules.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm">
                <i data-lucide="boxes" style="width: 16px; height: 16px;"></i>
                <span>Manage Modules</span>
            </a>
            @can('admin.modules.manage')
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="collapse" data-bs-target="#collapse-create-group">
                    <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                    <span>Create Group</span>
                </button>
            @endcan
        </x-slot:actions>
    </x-management.page-header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Create Group Collapse Card -->
    <div class="collapse mb-4" id="collapse-create-group">
        <div class="page-card">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;" class="text-primary"></i>
                    <span>Add New Navigation Group</span>
                </h6>
                <form action="{{ route('admin.navigation-groups.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="create_label" class="form-label small fw-semibold text-secondary">Group Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" id="create_label" class="form-control @error('label') is-invalid @enderror" placeholder="e.g. Purchasing, Warehouse" required>
                            @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="create_icon" class="form-label small fw-semibold text-secondary">Lucide Icon</label>
                            <input type="text" name="icon" id="create_icon" class="form-control @error('icon') is-invalid @enderror" placeholder="e.g. folder, database, shield" value="folder">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label for="create_order" class="form-label small fw-semibold text-secondary">Sort Order</label>
                            <input type="number" name="sort_order" id="create_order" class="form-control" placeholder="e.g. 70" min="0">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4 shadow-sm">
                                <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                                <span>Save Group</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#collapse-create-group">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Navigation Groups List Card -->
    <div class="page-card">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i data-lucide="list" style="width: 17px; height: 17px;" class="text-primary"></i>
                <span>Configured Navigation Groups</span>
            </h6>
            <span class="badge bg-light text-secondary border font-monospace">{{ $groups->count() }} groups</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover nowrap text-nowrap align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">Order</th>
                            <th>Group Name</th>
                            <th style="width: 180px;">Icon Name</th>
                            <th class="text-center" style="width: 130px;">Attached Modules</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th class="text-end pe-4" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groups as $group)
                            @php
                                $icon = $group->icon ?: 'folder';
                            @endphp
                            <tr id="row-group-{{ $group->id }}">
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border font-monospace px-2 py-1">{{ $group->sort_order }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i data-lucide="{{ $icon }}" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $group->label }}</div>
                                            @if ($group->description)
                                                <small class="text-secondary">{{ $group->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="small text-secondary font-monospace bg-light px-2 py-1 rounded border">{{ $icon }}</code>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.modules.index') }}?group={{ urlencode($group->label) }}" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 text-decoration-none" title="Filter modules in this group">
                                        {{ $group->modules_count }} module{{ $group->modules_count === 1 ? '' : 's' }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if ($group->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @can('admin.modules.manage')
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-icon btn-open-edit"
                                                data-id="{{ $group->id }}"
                                                data-label="{{ $group->label }}"
                                                data-icon="{{ $group->icon ?: 'folder' }}"
                                                data-order="{{ $group->sort_order }}"
                                                data-active="{{ $group->is_active ? 1 : 0 }}"
                                                data-description="{{ $group->description }}"
                                                title="Edit Group">
                                                <i data-lucide="pencil" style="width: 15px; height: 15px;"></i>
                                            </button>
                                            @if ($group->modules_count === 0)
                                                <form action="{{ route('admin.navigation-groups.destroy', $group) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete navigation group \'{{ $group->label }}\'?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Delete Group">
                                                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-icon" disabled title="Cannot delete: group contains modules">
                                                    <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary">
                                    No navigation groups found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Navigation Group Modal -->
    <div class="modal fade" id="modal-edit-nav-group" tabindex="-1" aria-labelledby="modal-edit-nav-group-title" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header bg-light py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2">
                            <i data-lucide="pencil" style="width: 18px; height: 18px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="modal-edit-nav-group-title">Edit Navigation Group</h5>
                            <small class="text-secondary">Renaming a group will update all its modules automatically</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update-nav-group" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_label" class="form-label small fw-semibold text-secondary">Group Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" id="edit_label" class="form-control" required>
                            <div class="form-text small">e.g. Master Data, Reporting, Procurement</div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="edit_icon" class="form-label small fw-semibold text-secondary">Lucide Icon</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary" id="edit-icon-preview">
                                        <i data-lucide="folder" style="width: 16px; height: 16px;"></i>
                                    </span>
                                    <input type="text" name="icon" id="edit_icon" class="form-control" placeholder="e.g. database, folder">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="edit_order" class="form-label small fw-semibold text-secondary">Sort Order</label>
                                <input type="number" name="sort_order" id="edit_order" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label small fw-semibold text-secondary">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2" placeholder="Optional description..."></textarea>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                            <div>
                                <label class="form-check-label fw-semibold text-dark d-block" for="edit_active">Active Group</label>
                                <small class="text-secondary">Visible in navigation menus.</small>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="edit_active" name="is_active" value="1" style="width: 2.25rem; height: 1.25rem;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm">
                            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const editModal = new bootstrap.Modal(document.getElementById('modal-edit-nav-group'));
                const formUpdate = document.getElementById('form-update-nav-group');
                const editLabel = document.getElementById('edit_label');
                const editIcon = document.getElementById('edit_icon');
                const editOrder = document.getElementById('edit_order');
                const editDescription = document.getElementById('edit_description');
                const editActive = document.getElementById('edit_active');
                const editIconPreview = document.getElementById('edit-icon-preview');

                if (editIcon && editIconPreview) {
                    editIcon.addEventListener('input', function () {
                        const val = this.value.trim() || 'folder';
                        editIconPreview.innerHTML = `<i data-lucide="${val}" style="width: 16px; height: 16px;"></i>`;
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                }

                document.querySelectorAll('.btn-open-edit').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const id = this.dataset.id;
                        const label = this.dataset.label;
                        const icon = this.dataset.icon || 'folder';
                        const order = this.dataset.order || 0;
                        const active = parseInt(this.dataset.active) === 1;
                        const desc = this.dataset.description || '';

                        formUpdate.action = `/admin/navigation-groups/${id}`;
                        editLabel.value = label;
                        editIcon.value = icon;
                        editOrder.value = order;
                        editDescription.value = desc;
                        editActive.checked = active;

                        if (editIconPreview) {
                            editIconPreview.innerHTML = `<i data-lucide="${icon}" style="width: 16px; height: 16px;"></i>`;
                        }

                        if (window.lucide) {
                            window.lucide.createIcons();
                        }

                        editModal.show();
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>

