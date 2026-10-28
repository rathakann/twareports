<x-app-layout title="Branches · Master Data · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Master Data</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Branches</span>
    </x-slot:breadcrumb>

    <x-management.page-header title="Branches (Business Places)" section="Master Data" icon="building-2"
        description="SAP Business Places (OBPL) catalog representing legal operating units, branch taxation profiles, and regional headquarters.">
        <x-slot:actions>
            <div
                class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span
                    class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button"
                class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle"
                data-target="#branches-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm"
                id="btn-sync-table" title="Sync from SAP">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                <span>Sync</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Filters Section -->
    <div id="branches-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-status" class="form-label small fw-semibold text-secondary mb-1">Filter by
                        Status</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="activity"></i>
                        <select id="filter-status" class="form-select">
                            <option value="all">All Statuses</option>
                            @foreach ($statuses as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-9 col-12 d-flex align-items-end justify-content-md-end">
                    <button type="button" id="btn-reset-filters"
                        class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 shadow-sm"
                        title="Reset all filters">
                        <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        <span>Reset Filters</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="branches-table" class="table table-striped table-hover align-middle w-100 mb-0 nowrap">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                            <th>Last Updated</th>
                            <th>BPL Code</th>
                            <th>Branch Name</th>
                            <th>Branch Name(Foreign)</th>
                            <th>Default Warehouse</th>
                            <th>Branch Reg. No</th>
                            <th>Address</th>
                            <th>Street</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Handled by Server-Side DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="modal-branch-details" tabindex="-1" aria-labelledby="modalBranchLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i data-lucide="building-2" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modal-branch-name">Branch Details</h5>
                            <span class="small text-secondary" id="modal-branch-code">BPL Code</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3 h-100">
                                <span class="text-secondary small d-block">English / Alias Name</span>
                                <span class="fw-semibold text-dark fs-6" id="modal-branch-en">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3 h-100">
                                <span class="text-secondary small d-block">Tax Registration Number (VAT)</span>
                                <span class="fw-semibold text-dark fs-6" id="modal-tax-id">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Phone Number</span>
                                <span class="fw-semibold text-dark" id="modal-phone">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Email Address</span>
                                <span class="fw-semibold text-dark" id="modal-email">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <span class="text-secondary small d-block">Physical Street Address</span>
                        <p class="fw-semibold text-dark mb-0" id="modal-address">-</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">City / Province</span>
                                <span class="fw-semibold text-dark" id="modal-city">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Country</span>
                                <span class="fw-semibold text-dark" id="modal-country">Cambodia</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Status</span>
                                <div id="modal-status-badge" class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3">
                        <span class="text-secondary small d-block">Operational Remarks / SAP Notes</span>
                        <p class="text-muted small mb-0" id="modal-remarks">None</p>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatDate(value) {
            if (!value) return '';

            const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);

            if (!match) return '';

            const [, year, month, day] = match;

            const months = [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];

            return `${months[Number(month) - 1]} ${Number(day)}, ${year}`;
        }
        function initBranchesTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initBranchesTable, 50);
                return;
            }

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'active') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>';
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Inactive</span>';
            };

            const table = $('#branches-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master-data.branches.index') }}',
                    data: function (d) {
                        d.status = $('#filter-status').val() || 'all';
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center text-secondary fw-semibold',
                        width: '50px',
                        render: function (data, type, row, meta) {
                            const start = (meta && meta.settings && meta.settings._iDisplayStart) ? meta.settings._iDisplayStart : 0;
                            return start + meta.row + 1;
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '70px'
                    },
                    {
                        data: 'UpdateDate',
                        name: 'UpdateDate',
                        render: function (data, type) {
                            if (type !== 'display') return data ?? '';
                            return data;
                            //return formatDate(data);
                        }
                    },
                    // {
                    //     data: 'BPLId',
                    //     name: 'BPLId',
                    //     render: function (data) {
                    //         return `<span class="badge bg-primary bg-opacity-10 text-primary font-monospace">${data}</span>`;
                    //     }
                    // },
                    // {
                    //     data: 'BPLName',
                    //     name: 'BPLName',
                    //     render: function (data, type, row) {
                    //         return `<span class="fw-semibold text-dark">${data}</span>`;
                    //     }
                    // },
                    {
                        data: 'BPLName',
                        name: 'BPLName',
                        render: function (data, type, row) {
                            if (!data) return '';

                            const branchName = String(data).trim();
                            const separatorIndex = branchName.indexOf('-');

                            const code = separatorIndex !== -1
                                ? branchName.substring(0, separatorIndex).trim()
                                : branchName;

                            if (type !== 'display') return code;
                            return `<span class="badge bg-primary bg-opacity-10 text-primary font-monospace">${$('<div>').text(code).html()}</span>`;
                        }
                    },
                    {
                        data: 'BPLName',
                        name: 'BPLName',
                        render: function (data, type, row) {
                            if (!data) return '';

                            const branchName = String(data).trim();
                            const separatorIndex = branchName.indexOf('-');

                            const name = separatorIndex !== -1
                                ? branchName.substring(separatorIndex + 1).trim()
                                : '';

                            if (type !== 'display') return name;

                            return `<span class="fw-semibold text-dark">${$('<div>').text(name).html()}</span>`;
                        }
                    },
                    {
                        data: 'BPLFrName',
                        name: 'BPLFrName',
                        render: function (data) {
                            return `<span class="fw-semibold text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'DflWhs',
                        name: 'DflWhs',
                        render: function (data) {
                            return `<span class="fw-semibold text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'TaxIdNum',
                        name: 'TaxIdNum',
                        render: function (data, type, row) {
                            return `<span class="fw-semibold text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'Address',
                        name: 'Address',
                        defaultContent: '',
                        render: function (data) {
                            return `<span class="fw-semibold text-dark">${data ?? ''}</span>`;
                        }
                    },
                    {
                        data: 'Street',
                        name: 'Street',
                        defaultContent: '',
                        render: function (data) {
                            return `<span class="fw-semibold text-dark">${data ?? ''}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function (status) { return getStatusBadge(status); }
                    }
                ],
                drawCallback: function () {
                    if (window.lucide) {
                        window.lucide.createIcons({ icons: window.lucide.icons });
                    }
                }
            });

            window.setupDataTableExtensions?.(table.table().node(), table);

            $('#filter-status').on('change', function () { table.draw(); });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-status').val('all');
                table.draw();
            });

            $('#btn-sync-table, #btn-refresh-table').on('click', function () {
                const btn = $(this);
                const icon = btn.find('i, svg');
                btn.prop('disabled', true);
                icon.addClass('icon-spin');
                table.ajax.reload(function () {
                    btn.prop('disabled', false);
                    icon.removeClass('icon-spin');
                    const now = new Date();
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const formatted = String(now.getDate()).padStart(2, '0') + '-' +
                        months[now.getMonth()] + '-' +
                        now.getFullYear() + ' ' +
                        String(now.getHours()).padStart(2, '0') + ':' +
                        String(now.getMinutes()).padStart(2, '0');
                    $('.last-sync-time').text(formatted);
                }, false);
            });

            // Details preview modal
            $(document).on('click', '.btn-view-item', function () {
                const btn = $(this);
                const uuid = btn.data('id');

                function renderModal(branch) {
                    $('#modal-branch-name').text(branch.branch_name || 'Branch Details');
                    $('#modal-branch-code').text(`BPL ID: ${branch.bpl_id || '-'}`);
                    $('#modal-branch-en').text(branch.branch_name_en || '-');
                    $('#modal-tax-id').text(branch.tax_id || '-');
                    $('#modal-phone').text(branch.phone || '-');
                    $('#modal-email').text(branch.email || '-');
                    $('#modal-address').text(branch.street || '-');
                    $('#modal-city').text(branch.city || '-');
                    $('#modal-country').text(branch.country || 'Cambodia');
                    $('#modal-status-badge').html(getStatusBadge(branch.status));
                    $('#modal-remarks').text(branch.remarks || 'No remarks provided.');

                    const modalEl = document.getElementById('modal-branch-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && rowData.bpl_id) {
                    renderModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('master-data/branches') }}/${uuid}`,
                    type: 'GET',
                    success: function (res) {
                        if (res.success && res.data) renderModal(res.data);
                    },
                    complete: function () { btn.prop('disabled', false); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initBranchesTable);
    </script>
</x-app-layout>