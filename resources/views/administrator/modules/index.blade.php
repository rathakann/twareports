<x-app-layout title="Modules · Administrator · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Administrator</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Modules</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Modules"
        section="Administrator"
        icon="boxes"
        description="Transform and manage dynamic navigation menus, ordering, routes, permissions, and module hierarchy.">
        <x-slot:actions>
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#modules-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm" id="btn-sync-table" title="Sync / Reload Table">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                <span>Sync</span>
            </button>
            @can('admin.modules.manage')
                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm" id="btn-open-manage-groups" data-bs-toggle="modal" data-bs-target="#modal-manage-groups" title="Manage Navigation Groups">
                    <i data-lucide="layers" style="width: 16px; height: 16px;"></i>
                    <span>Navigation Groups</span>
                </button>
                <a href="{{ route('admin.modules.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                    <span>Create Module</span>
                </a>
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

    <!-- Filters Section -->
    <div id="modules-filter-card" class="page-card mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label for="filter-group" class="form-label small fw-semibold text-secondary mb-1">Navigation Group</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="layers"></i>
                        <select id="filter-group" class="form-select">
                            <option value="all">All Groups</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-status" class="form-label small fw-semibold text-secondary mb-1">Status</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="activity"></i>
                        <select id="filter-status" class="form-select">
                            <option value="all">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5 col-12 d-flex align-items-end justify-content-md-end">
                    <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" title="Reset all filters">
                        <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        <span>Reset Filters</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card rendered cleanly via Yajra DataTable -->
    <div class="page-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-bordered table-hover nowrap text-nowrap align-middle w-100 mb-0']) !!}
            </div>
        </div>
    </div>

    <!-- Module Details Modal -->
    <div class="modal fade" id="modal-module-details" tabindex="-1" aria-labelledby="modal-module-details-title" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header bg-light py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2" id="detail-icon-box">
                            <i data-lucide="boxes" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="detail-label">-</h5>
                            <small class="text-secondary" id="detail-group">-</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Named Route</small>
                                <span class="fw-semibold text-dark font-monospace small" id="detail-route">—</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">URL Path</small>
                                <span class="fw-semibold text-dark font-monospace small" id="detail-url">—</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Required Permission</small>
                                <span class="fw-semibold text-dark font-monospace small" id="detail-permission">Public</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Parent Menu</small>
                                <span class="fw-semibold text-dark small" id="detail-parent">—</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Menu Sort Order</small>
                                <span class="fw-bold fs-6 text-dark font-monospace" id="detail-sort-order">10</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Status</small>
                                <div id="detail-status" class="mt-1"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Description</small>
                                <span class="text-dark small" id="detail-description">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Navigation Groups Modal -->
    <div class="modal fade" id="modal-manage-groups" tabindex="-1" aria-labelledby="modal-manage-groups-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header bg-light py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2">
                            <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="modal-manage-groups-title">Manage Navigation Groups</h5>
                            <small class="text-secondary">Revise, reorder, or scale navigation groups dynamically</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Quick Add Form -->
                    <div class="card border border-light-subtle bg-light bg-opacity-50 rounded-3 mb-4">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-2 small text-dark d-flex align-items-center gap-1.5">
                                <i data-lucide="plus-circle" style="width: 15px; height: 15px;" class="text-primary"></i>
                                <span>Create New Navigation Group</span>
                            </h6>
                            <form id="form-create-group" class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Group Label <span class="text-danger">*</span></label>
                                    <input type="text" id="new-group-label" class="form-control form-control-sm" placeholder="e.g. Purchasing, Warehouse" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Icon Name</label>
                                    <input type="text" id="new-group-icon" class="form-control form-control-sm" placeholder="e.g. folder, layers" value="folder">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Order</label>
                                    <input type="number" id="new-group-order" class="form-control form-control-sm" placeholder="e.g. 70" min="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-1" id="btn-save-new-group">
                                        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
                                        <span>Add</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Groups List Table -->
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0" id="table-navigation-groups">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 80px;">Order</th>
                                    <th>Group Name</th>
                                    <th style="width: 130px;">Icon</th>
                                    <th class="text-center" style="width: 100px;">Modules</th>
                                    <th class="text-center" style="width: 90px;">Status</th>
                                    <th class="text-end pe-3" style="width: 110px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="navigation-groups-tbody">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">
                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                        <span>Loading navigation groups...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Navigation Group Modal -->
    <div class="modal fade" id="modal-edit-group" tabindex="-1" aria-labelledby="modal-edit-group-title" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header bg-light py-2 px-3">
                    <h6 class="modal-title fw-bold text-dark fs-6 mb-0" id="modal-edit-group-title">Revise Group</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-edit-group">
                    <input type="hidden" id="edit-group-id">
                    <div class="modal-body p-3">
                        <div class="mb-2.5">
                            <label for="edit-group-label" class="form-label small fw-semibold text-secondary mb-1">Group Label <span class="text-danger">*</span></label>
                            <input type="text" id="edit-group-label" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-2.5">
                            <label for="edit-group-icon" class="form-label small fw-semibold text-secondary mb-1">Lucide Icon</label>
                            <input type="text" id="edit-group-icon" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2.5">
                            <label for="edit-group-order" class="form-label small fw-semibold text-secondary mb-1">Sort Order</label>
                            <input type="number" id="edit-group-order" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="edit-group-active">
                            <label class="form-check-label small fw-semibold text-secondary" for="edit-group-active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" id="btn-update-group">
                            <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Yajra DataTable scripts --}}
        {!! $dataTable->scripts() !!}

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tableId = 'modules-table';
                const detailsModal = new bootstrap.Modal(document.getElementById('modal-module-details'));

                function getTable() {
                    return window.LaravelDataTables ? window.LaravelDataTables[tableId] : null;
                }

                // Filter group & status change triggers DataTable redraw
                $('#filter-group, #filter-status').on('change', function () {
                    const dt = getTable();
                    if (dt) dt.draw();
                });

                // Reset Filters
                $('#btn-reset-filters').on('click', function () {
                    $('#filter-group').val('all');
                    $('#filter-status').val('all');
                    const dt = getTable();
                    if (dt) dt.draw();
                });

                // Sync / Refresh Table
                $('#btn-sync-table').on('click', function () {
                    const btn = $(this);
                    const icon = btn.find('i, svg');
                    btn.prop('disabled', true);
                    icon.addClass('icon-spin');

                    const dt = getTable();
                    if (dt) {
                        dt.ajax.reload(function () {
                            btn.prop('disabled', false);
                            icon.removeClass('icon-spin');
                            const now = new Date();
                            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                            const formatted = String(now.getDate()).padStart(2, '0') + '-' +
                                              months[now.getMonth()] + '-' +
                                              now.getFullYear() + ' ' +
                                              String(now.getHours()).padStart(2, '0') + ':' +
                                              String(now.getMinutes()).padStart(2, '0');
                            $('.last-sync-time').text(formatted);
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'success', title: 'Modules synchronized successfully.' });
                            }
                        }, false);
                    }
                });

                // View Details
                $(document).on('click', '.btn-view-module', function () {
                    const btn = $(this);
                    const url = btn.data('url') || `/admin/modules/${btn.data('id')}`;

                    $.get(url, function (res) {
                        if (res.success && res.module) {
                            const m = res.module;
                            $('#detail-label').text(m.label || '—');
                            $('#detail-group').text(`Group: ${m.group_label || 'Workspace'} (Position: ${m.group_order || 0})`);
                            $('#detail-route').text(m.route || '—');
                            $('#detail-url').text(m.url || '—');
                            $('#detail-permission').text(m.permission || 'Public');
                            $('#detail-parent').text(m.parent ? m.parent.label : '—');
                            $('#detail-sort-order').text(m.sort_order ?? 0);
                            $('#detail-description').text(m.description || '—');

                            const statusHtml = m.is_active
                                ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><span class="badge-dot bg-success"></span> Active</span>'
                                : '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25"><span class="badge-dot bg-secondary"></span> Inactive</span>';
                            $('#detail-status').html(statusHtml);

                            const iconName = m.icon || 'boxes';
                            $('#detail-icon-box').html(`<i data-lucide="${iconName}" style="width: 20px; height: 20px;"></i>`);
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }

                            detailsModal.show();
                        }
                    }).fail(function (xhr) {
                        if (window.Toast) {
                            window.Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON?.message || 'Failed to load module details.'
                            });
                        }
                    });
                });

                // Inline Status Toggle Switch via AJAX
                $(document).on('change', '.module-status-toggle', function () {
                    const checkbox = $(this);
                    const toggleUrl = checkbox.data('toggle-url') || `/admin/modules/${checkbox.data('module-id')}/status`;
                    const isChecked = checkbox.is(':checked');

                    $.ajax({
                        url: toggleUrl,
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        data: {
                            is_active: isChecked ? 1 : 0
                        },
                        success: function (res) {
                            if (window.Toast) {
                                window.Toast.fire({
                                    icon: 'success',
                                    title: res.message || 'Module status updated successfully.'
                                });
                            }
                            const dt = getTable();
                            if (dt) dt.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            checkbox.prop('checked', !isChecked);
                            const msg = xhr.responseJSON?.message || 'Failed to update module status.';
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'error', title: msg });
                            }
                        }
                    });
                });

                // Delete Module
                $(document).on('click', '.btn-delete-module', function () {
                    const btn = $(this);
                    const deleteUrl = btn.data('url') || `/admin/modules/${btn.data('id')}`;
                    const label = btn.data('label') || 'this module';

                    const executeDelete = function () {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                if (window.Toast) {
                                    window.Toast.fire({ icon: 'success', title: res.message || 'Deleted successfully.' });
                                }
                                const dt = getTable();
                                if (dt) dt.ajax.reload(null, false);
                            },
                            error: function (xhr) {
                                const msg = xhr.responseJSON?.message || 'Failed to delete module.';
                                if (window.Toast) {
                                    window.Toast.fire({ icon: 'error', title: msg });
                                }
                            }
                        });
                    };

                    if (window.Swal) {
                        window.Swal.fire({
                            title: `Delete module '${label}'?`,
                            text: 'Submodules under this menu will be detached to top-level. This action cannot be undone.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, delete it'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                executeDelete();
                            }
                        });
                    } else if (confirm(`Are you sure you want to delete module '${label}'?`)) {
                        executeDelete();
                    }
                });

                // --- Navigation Groups Dynamic Management ---
                const editGroupModal = new bootstrap.Modal(document.getElementById('modal-edit-group'));

                function renderNavigationGroups(groups) {
                    const tbody = $('#navigation-groups-tbody');
                    tbody.empty();

                    if (!groups || groups.length === 0) {
                        tbody.append('<tr><td colspan="6" class="text-center py-3 text-muted">No navigation groups configured.</td></tr>');
                        return;
                    }

                    groups.forEach(function (group) {
                        const icon = group.icon || 'folder';
                        const statusBadge = group.is_active
                            ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-0.5">Active</span>'
                            : '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-0.5">Inactive</span>';

                        const tr = $(`
                            <tr data-group-id="${group.id}">
                                <td class="ps-3 font-monospace small text-muted">${group.sort_order}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light border rounded d-inline-flex align-items-center justify-content-center text-primary" style="width: 28px; height: 28px;">
                                            <i data-lucide="${icon}" style="width: 14px; height: 14px;"></i>
                                        </div>
                                        <span class="fw-semibold text-dark">${group.label}</span>
                                    </div>
                                </td>
                                <td><code class="small text-muted font-monospace">${icon}</code></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 font-monospace">${group.modules_count || 0}</span>
                                </td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-icon btn-edit-group"
                                            data-id="${group.id}"
                                            data-label="${group.label}"
                                            data-icon="${group.icon || 'folder'}"
                                            data-order="${group.sort_order}"
                                            data-active="${group.is_active ? 1 : 0}"
                                            title="Edit Group">
                                            <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-icon btn-delete-group"
                                            data-id="${group.id}"
                                            data-label="${group.label}"
                                            data-count="${group.modules_count || 0}"
                                            title="Delete Group"
                                            ${(group.modules_count || 0) > 0 ? 'disabled' : ''}>
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `);
                        tbody.append(tr);
                    });

                    if (window.lucide) {
                        window.lucide.createIcons();
                    }

                    // Also refresh filter-group dropdown options
                    const filterSelect = $('#filter-group');
                    const currentVal = filterSelect.val();
                    filterSelect.find('option:not([value="all"])').remove();
                    groups.forEach(function (g) {
                        filterSelect.append(new Option(g.label, g.label, false, g.label === currentVal));
                    });
                }

                function fetchNavigationGroups() {
                    $.get('/admin/navigation-groups', function (res) {
                        if (res.success && res.groups) {
                            renderNavigationGroups(res.groups);
                        }
                    });
                }

                // Open modal triggers fetch
                $('#modal-manage-groups').on('show.bs.modal', function () {
                    fetchNavigationGroups();
                });

                // Create group
                $('#form-create-group').on('submit', function (e) {
                    e.preventDefault();
                    const btn = $('#btn-save-new-group');
                    btn.prop('disabled', true);

                    const data = {
                        label: $('#new-group-label').val().trim(),
                        icon: $('#new-group-icon').val().trim() || 'folder',
                        sort_order: $('#new-group-order').val() || null,
                        is_active: 1
                    };

                    $.ajax({
                        url: '/admin/navigation-groups',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        data: data,
                        success: function (res) {
                            btn.prop('disabled', false);
                            $('#new-group-label').val('');
                            $('#new-group-order').val('');
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'success', title: res.message || 'Group created.' });
                            }
                            fetchNavigationGroups();
                            const dt = getTable();
                            if (dt) dt.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            btn.prop('disabled', false);
                            const msg = xhr.responseJSON?.message || 'Failed to create navigation group.';
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'error', title: msg });
                            }
                        }
                    });
                });

                // Edit group button clicked
                $(document).on('click', '.btn-edit-group', function () {
                    const btn = $(this);
                    $('#edit-group-id').val(btn.data('id'));
                    $('#edit-group-label').val(btn.data('label'));
                    $('#edit-group-icon').val(btn.data('icon'));
                    $('#edit-group-order').val(btn.data('order'));
                    $('#edit-group-active').prop('checked', parseInt(btn.data('active')) === 1);
                    editGroupModal.show();
                });

                // Submit update group
                $('#form-edit-group').on('submit', function (e) {
                    e.preventDefault();
                    const id = $('#edit-group-id').val();
                    const btn = $('#btn-update-group');
                    btn.prop('disabled', true);

                    const data = {
                        label: $('#edit-group-label').val().trim(),
                        icon: $('#edit-group-icon').val().trim(),
                        sort_order: $('#edit-group-order').val(),
                        is_active: $('#edit-group-active').is(':checked') ? 1 : 0
                    };

                    $.ajax({
                        url: `/admin/navigation-groups/${id}`,
                        type: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        data: data,
                        success: function (res) {
                            btn.prop('disabled', false);
                            editGroupModal.hide();
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'success', title: res.message || 'Group updated.' });
                            }
                            fetchNavigationGroups();
                            const dt = getTable();
                            if (dt) dt.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            btn.prop('disabled', false);
                            const msg = xhr.responseJSON?.message || 'Failed to update navigation group.';
                            if (window.Toast) {
                                window.Toast.fire({ icon: 'error', title: msg });
                            }
                        }
                    });
                });

                // Delete group
                $(document).on('click', '.btn-delete-group', function () {
                    const btn = $(this);
                    const id = btn.data('id');
                    const label = btn.data('label');
                    const count = parseInt(btn.data('count') || 0);

                    if (count > 0) {
                        if (window.Toast) {
                            window.Toast.fire({ icon: 'error', title: `Group '${label}' contains modules and cannot be deleted.` });
                        }
                        return;
                    }

                    if (confirm(`Are you sure you want to delete navigation group '${label}'?`)) {
                        $.ajax({
                            url: `/admin/navigation-groups/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            success: function (res) {
                                if (window.Toast) {
                                    window.Toast.fire({ icon: 'success', title: res.message || 'Group deleted.' });
                                }
                                fetchNavigationGroups();
                                const dt = getTable();
                                if (dt) dt.ajax.reload(null, false);
                            },
                            error: function (xhr) {
                                const msg = xhr.responseJSON?.message || 'Failed to delete group.';
                                if (window.Toast) {
                                    window.Toast.fire({ icon: 'error', title: msg });
                                }
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
