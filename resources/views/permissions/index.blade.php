<x-app-layout title="Permissions · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Access Control</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Permission Management</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Permission Management"
        section="Access Control"
        icon="key"
        description="Catalog and administer system permissions and module authorization gates.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#permissions-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            @canany(['permissions.create', 'permissions.manage'])
                <a href="{{ route('permissions.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                    <span>Create Permission</span>
                </a>
            @endcanany
        </x-slot:actions>
    </x-management.page-header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px;"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div id="permissions-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-module" class="form-label small fw-semibold text-secondary mb-1">Filter by Module</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="layers"></i>
                        <select id="filter-module" class="form-select js-select2">
                            <option value="all">All Modules</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
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
                <table id="permissions-table" data-export-module="permissions" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 140px;">Actions</th>
                            <th>Permission Name</th>
                            <th>Module</th>
                            <th>Roles Assigned</th>
                            <th>Users Directly Assigned</th>
                            <th>Guard</th>
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
        function initPermissionsTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initPermissionsTable, 50);
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

            const table = $('#permissions-table').DataTable({
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
                    url: '{{ route('permissions.index') }}',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: function (d) {
                        d.module = $('#filter-module').val() || 'all';
                        d.guard = $('#filter-guard').val() || 'all';
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
                            return `<code class="fw-semibold text-primary">${data}</code>`;
                        }
                    },
                    {
                        data: 'module',
                        name: 'module',
                        render: function (data) {
                            return `<span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase">${data}</span>`;
                        }
                    },
                    {
                        data: 'roles_count',
                        name: 'roles_count',
                        searchable: false,
                        render: function (data) {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary">${data} roles</span>`;
                        }
                    },
                    {
                        data: 'users_count',
                        name: 'users_count',
                        searchable: false,
                        render: function (data) {
                            return `<span class="badge bg-info bg-opacity-10 text-info">${data} users</span>`;
                        }
                    },
                    {
                        data: 'guard_name',
                        name: 'guard_name',
                        render: function (data) {
                            return `<span class="badge bg-light text-secondary border">${data}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data) {
                            if (!data) return '<span class="text-secondary">—</span>';
                            const date = new Date(data);
                            return `<span class="text-secondary small">${date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</span>`;
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

            $('#filter-module, #filter-guard, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-module').val('all').trigger('change');
                $('#filter-guard').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            // AJAX Delete Permission Handler
            $(document).on('click', '.btn-delete-permission', function () {
                const button = $(this);
                const permissionName = button.data('permission-name') || 'this permission';
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
                                    title: data.message || 'Permission deleted successfully.',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            }
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            const message = xhr.responseJSON?.message || 'Failed to delete permission.';
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
                        title: `Delete permission ${permissionName}?`,
                        text: 'Are you sure you want to delete this permission?',
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
                } else if (confirm(`Are you sure you want to delete ${permissionName}?`)) {
                    performDelete();
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPermissionsTable);
        } else {
            initPermissionsTable();
        }
    </script>
</x-app-layout>
