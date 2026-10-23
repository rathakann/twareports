<x-app-layout title="User Management · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Access Control</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">User Management</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="User Management"
        section="Access Control"
        icon="users"
        description="Administer user credentials, security status, assigned roles, and granular direct permissions.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#users-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            @can('users.create')
                <a href="{{ route('users.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                    <span>Create User</span>
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

    <div id="users-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-role" class="form-label small fw-semibold text-secondary mb-1">Filter by Role</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="shield"></i>
                        <select id="filter-role" class="form-select js-select2">
                            <option value="all">All Roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-status" class="form-label small fw-semibold text-secondary mb-1">Filter by Status</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="activity"></i>
                        <select id="filter-status" class="form-select js-select2">
                            <option value="all">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-created-date" class="form-label small fw-semibold text-secondary mb-1">Created Date</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="calendar"></i>
                        <input type="text" id="filter-created-date" class="form-control js-datepicker" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3 col-12 d-flex align-items-end justify-content-md-end">
                    <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 shadow-sm btn-reset-filters" title="Reset all filters">
                        <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        <span>Reset Filters</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="users-table" data-export-module="users" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 140px;">Actions</th>
                            <th>User</th>
                            <th>Assigned Roles</th>
                            <th>Direct Permissions</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Handled by Server-Side DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function initUsersTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initUsersTable, 50);
                return;
            }

            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const table = $('#users-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: {
                        first: 'First',
                        previous: 'Previous',
                        next: 'Next',
                        last: 'Last'
                    }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('users.index') }}',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: function (d) {
                        d.role = $('#filter-role').val() || 'all';
                        d.status = $('#filter-status').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Users DataTable AJAX Error:', error, thrown, xhr.responseText);
                    }
                },
                order: [[6, 'desc']],
                columns: [
                    {
                        data: null,
                        name: 'row_number',
                        title: '#',
                        orderable: false,
                        searchable: false,
                        className: 'text-center text-secondary fw-semibold',
                        width: '50px',
                        render: function (data, type, row, meta) {
                            let start = 0;
                            if (meta && meta.settings) {
                                if (typeof meta.settings.displayStart === 'number') {
                                    start = meta.settings.displayStart;
                                } else if (typeof meta.settings._iDisplayStart === 'number') {
                                    start = meta.settings._iDisplayStart;
                                }
                            }
                            const rowIdx = (meta && typeof meta.row === 'number' && !isNaN(meta.row)) ? meta.row : 0;
                            return start + rowIdx + 1;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center text-nowrap',
                        width: '140px'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function (data, type, row) {
                            const nameStr = (data || '').trim();
                            const words = nameStr.split(/\s+/).filter(Boolean);
                            const initials = words.length > 0
                                ? words.slice(0, 2).map(n => n.charAt(0)).join('').toUpperCase()
                                : 'U';
                            return `
                                <div class="d-flex align-items-center gap-2 user-cell">
                                    <div class="avatar user-avatar rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                        ${initials}
                                    </div>
                                    <div class="d-flex flex-column justify-content-center lh-sm user-info" style="min-width: 0;">
                                        <div class="fw-semibold text-dark text-truncate user-name">${nameStr || 'User'}</div>
                                        <small class="text-secondary text-truncate user-email">${row.email || ''}</small>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'roles_list',
                        name: 'roles_list',
                        orderable: false,
                        searchable: false,
                        render: function (roles) {
                            if (!roles || roles.length === 0) {
                                return '<span class="badge bg-secondary bg-opacity-10 text-secondary">None</span>';
                            }
                            return roles.map(role => {
                                const isSuper = role === 'Super Admin';
                                const badgeClass = isSuper
                                    ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'
                                    : 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                                return `<span class="badge ${badgeClass} me-1">${role}</span>`;
                            }).join('');
                        }
                    },
                    {
                        data: 'permissions_list',
                        name: 'permissions_list',
                        orderable: false,
                        searchable: false,
                        render: function (perms) {
                            if (!perms || perms.length === 0) {
                                return '<span class="text-muted small">Inherited only</span>';
                            }
                            if (perms.length <= 2) {
                                return perms.map(p => `<span class="badge bg-info bg-opacity-10 text-info me-1">${p}</span>`).join('');
                            }
                            return `
                                <span class="badge bg-info bg-opacity-10 text-info me-1">${perms[0]}</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary" title="${perms.slice(1).join(', ')}">+${perms.length - 1} more</span>
                            `;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (status, type, row) {
                            const isActive = status === 'active';
                            const isSelf = row.id === {{ auth()->id() ?? 0 }};
                            return `
                                <div class="d-flex align-items-center gap-2 status-cell">
                                    <div class="form-check form-switch status-switch-wrapper mb-0">
                                        <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                            data-user-id="${row.id}"
                                            ${isActive ? 'checked' : ''}
                                            ${isSelf ? 'disabled title="You cannot toggle your own status"' : ''}
                                            style="cursor: pointer;">
                                    </div>
                                    <span class="badge ${isActive ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary'}">
                                        ${isActive ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data) {
                            if (!data) return '-';
                            return `<span>${new Date(data).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })}</span>`;
                        }
                    }
                ],
                drawCallback: function () {
                    if (window.lucide) {
                        window.lucide.createIcons({ icons: window.lucide.icons });
                    }
                }
            });

            window.setupDataTableExtensions?.(table.table().node(), table);

            $('#filter-role, #filter-status, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-role').val('all').trigger('change');
                $('#filter-status').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            // Inline Status Toggle Switch via AJAX
            $(document).on('change', '.status-toggle', function () {
                const checkbox = $(this);
                const userId = checkbox.data('user-id');
                const isChecked = checkbox.is(':checked');
                const targetStatus = isChecked ? 'active' : 'inactive';

                $.ajax({
                    url: `/users/${userId}/status`,
                    type: 'PATCH',
                    contentType: 'application/json',
                    data: JSON.stringify({ status: targetStatus }),
                    success: function (data) {
                        if (window.Swal) {
                            window.Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        checkbox.prop('checked', !isChecked);
                        const message = xhr.responseJSON?.message || 'Failed to update user status.';
                        if (window.Swal) {
                            window.Swal.fire({
                                icon: 'error',
                                title: 'Status Update Denied',
                                text: message
                            });
                        } else {
                            alert(message);
                        }
                    }
                });
            });

            // AJAX Delete User Handler
            $(document).on('click', '.btn-delete-user', function () {
                const button = $(this);
                const userName = button.data('user-name') || 'this user';
                const deleteUrl = button.data('delete-url');

                const performDelete = function () {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        success: function (data) {
                            if (window.Swal) {
                                window.Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message || 'User deleted successfully.',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            }
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            const message = xhr.responseJSON?.message || 'Failed to delete user.';
                            if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Failed',
                                    text: message
                                });
                            } else {
                                alert(message);
                            }
                        }
                    });
                };

                if (window.Swal) {
                    window.Swal.fire({
                        title: `Delete user ${userName}?`,
                        text: 'This action will permanently delete the user account and revoke all assigned roles and permissions.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc3545'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performDelete();
                        }
                    });
                } else if (confirm(`Are you sure you want to delete ${userName}?`)) {
                    performDelete();
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initUsersTable);
        } else {
            initUsersTable();
        }
    </script>
</x-app-layout>
