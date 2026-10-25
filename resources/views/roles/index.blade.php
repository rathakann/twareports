<x-app-layout title="Roles · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Access Control</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Role Management</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Role Management"
        section="Access Control"
        icon="shield"
        description="Define security roles and configure access permissions bundles across modules.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#roles-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            @can('roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                    <span>Create Role</span>
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

    <div id="roles-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-guard" class="form-label small fw-semibold text-secondary mb-1">Filter by Guard</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="shield-check"></i>
                        <select id="filter-guard" class="form-select js-select2">
                            <option value="all">All Guards</option>
                            @foreach ($guards as $guard)
                                <option value="{{ $guard }}">{{ $guard }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-assigned" class="form-label small fw-semibold text-secondary mb-1">User Assignment</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="users"></i>
                        <select id="filter-assigned" class="form-select js-select2">
                            <option value="all">All Roles</option>
                            <option value="with_users">Assigned to Users</option>
                            <option value="without_users">No Users Assigned</option>
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
                <table id="roles-table" data-export-module="roles" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 140px;">Actions</th>
                            <th>Role Name</th>
                            <th>Guard</th>
                            <th>Users Assigned</th>
                            <th>Permissions Count</th>
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
        function initRolesTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initRolesTable, 50);
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

            const table = $('#roles-table').DataTable({
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
                    url: '{{ route('roles.index') }}',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: function (d) {
                        d.guard = $('#filter-guard').val() || 'all';
                        d.assigned = $('#filter-assigned').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    }
                },
                order: [[2, 'asc']],
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
                        render: function (data) {
                            const isSuper = data === 'Super Admin';
                            const badge = isSuper
                                ? '<span class="badge bg-danger bg-opacity-10 text-danger ms-2 border border-danger border-opacity-25">Super Admin</span>'
                                : '';
                            return `<div class="fw-semibold text-dark d-inline-flex align-items-center">${data}${badge}</div>`;
                        }
                    },
                    {
                        data: 'guard_name',
                        name: 'guard_name',
                        render: function (data) {
                            return `<span class="badge bg-secondary bg-opacity-10 text-secondary">${data}</span>`;
                        }
                    },
                    {
                        data: 'users_count',
                        name: 'users_count',
                        searchable: false,
                        render: function (data) {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary">${data} users</span>`;
                        }
                    },
                    {
                        data: 'permissions_count',
                        name: 'permissions_count',
                        searchable: false,
                        render: function (data) {
                            return `<span class="badge bg-info bg-opacity-10 text-info">${data} permissions</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data) {
                            if (!data) return '-';
                            return new Date(data).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
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

            $('#filter-guard, #filter-assigned, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-guard').val('all').trigger('change');
                $('#filter-assigned').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            // AJAX Delete Role Handler
            $(document).on('click', '.btn-delete-role', function () {
                const button = $(this);
                const roleName = button.data('role-name') || 'this role';
                const deleteUrl = button.data('delete-url');

                const performDelete = function () {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        success: function (data) {
                            if (window.Toast) {
                                window.Toast.fire({
                                    icon: 'success',
                                    title: data.message || 'Role deleted successfully.'
                                });
                            } else if (window.Swal) {
                                window.Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message || 'Role deleted successfully.',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            }
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            const message = xhr.responseJSON?.message || 'Failed to delete role.';
                            if (window.Toast) {
                                window.Toast.fire({
                                    icon: 'error',
                                    title: message
                                });
                            } else if (window.Swal) {
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Failed',
                                    text: message
                                });
                            }
                        }
                    });
                };

                if (window.Swal) {
                    window.Swal.fire({
                        title: `Delete role ${roleName}?`,
                        text: 'Are you sure you want to delete this role?',
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
                } else {
                    performDelete();
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initRolesTable);
        } else {
            initRolesTable();
        }
    </script>
</x-app-layout>
