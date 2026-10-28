<x-app-layout title="Business Partner · Master Data · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Master Data</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Business Partner</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Business Partner (OCRD)"
        section="Master Data"
        icon="users-round"
        description="SAP Business Partners master catalog consolidating commercial customers, petroleum suppliers/vendors, and prospective leads.">
        <x-slot:actions>
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#bp-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm" id="btn-sync-table" title="Sync from SAP">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                <span>Sync</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Filters Section -->
    <div id="bp-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-type" class="form-label small fw-semibold text-secondary mb-1">Partner Type</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="tag"></i>
                        <select id="filter-type" class="form-select">
                            <option value="all">All Types</option>
                            @foreach ($types as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
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
                            @foreach ($statuses as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-12 d-flex align-items-end justify-content-md-end">
                    <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" title="Reset all filters">
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
                <table id="bp-table" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                            <th>Card Code</th>
                            <th>Partner Name</th>
                            <th class="text-center">Type</th>
                            <th>Industry Group</th>
                            <th>Phone / Email</th>
                            <th class="text-end">Balance (USD)</th>
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
    <div class="modal fade" id="modal-bp-details" tabindex="-1" aria-labelledby="modalBpLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i data-lucide="users-round" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modal-bp-title">Business Partner Details</h5>
                            <span class="small text-secondary" id="modal-bp-cardcode">Code</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Partner Classification</span>
                                <div id="modal-bp-type-badge" class="mt-1"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Industry / Customer Group</span>
                                <span class="fw-semibold text-dark fs-6" id="modal-bp-group">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Telephone</span>
                                <span class="fw-semibold text-dark" id="modal-bp-phone">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Email Address</span>
                                <span class="fw-semibold text-dark" id="modal-bp-email">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-header bg-transparent py-2">
                            <h6 class="card-title mb-0 small fw-semibold text-uppercase text-secondary">Credit & Financial Standing</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-sm-4 text-center border-end">
                                    <small class="text-secondary d-block">Current Balance</small>
                                    <span class="fw-bold fs-5 text-primary" id="modal-bp-balance">$0.00</span>
                                </div>
                                <div class="col-sm-4 text-center border-end">
                                    <small class="text-secondary d-block">Credit Limit</small>
                                    <span class="fw-semibold fs-6 text-dark" id="modal-bp-limit">$0.00</span>
                                </div>
                                <div class="col-sm-4 text-center">
                                    <small class="text-secondary d-block">Payment Terms</small>
                                    <span class="fw-semibold fs-6 text-dark" id="modal-bp-terms">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Tax Registration (VAT)</span>
                                <span class="fw-semibold text-dark font-monospace" id="modal-bp-vat">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Status</span>
                                <div id="modal-bp-status" class="mt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initBpTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initBpTable, 50);
                return;
            }

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'active') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>';
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Inactive</span>';
            };

            const getTypeBadge = (type) => {
                const t = String(type || '').toLowerCase();
                if (t === 'customer') return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Customer</span>';
                if (t === 'vendor') return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Vendor</span>';
                return '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Lead</span>';
            };

            const table = $('#bp-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master-data.business-partners.index') }}',
                    data: function (d) {
                        d.card_type = $('#filter-type').val() || 'all';
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
                        data: 'card_code',
                        name: 'card_code',
                        render: function(data) {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary font-monospace">${data}</span>`;
                        }
                    },
                    {
                        data: 'card_name',
                        name: 'card_name',
                        render: function(data) {
                            return `<span class="fw-semibold text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'card_type',
                        name: 'card_type',
                        className: 'text-center',
                        render: function(t) { return getTypeBadge(t); }
                    },
                    {
                        data: 'group_name',
                        name: 'group_name',
                        render: function(d) { return d || '-'; }
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        render: function(data, type, row) {
                            return `<span>${data || '-'}</span>${row.email ? `<br><small class="text-secondary">${row.email}</small>` : ''}`;
                        }
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        className: 'text-end',
                        render: function(d) {
                            const num = parseFloat(d) || 0;
                            return `<span class="fw-semibold font-monospace">$${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(status) { return getStatusBadge(status); }
                    }
                ],
                drawCallback: function () {
                    if (window.lucide) {
                        window.lucide.createIcons({ icons: window.lucide.icons });
                    }
                }
            });

            window.setupDataTableExtensions?.(table.table().node(), table);

            $('#filter-type, #filter-status').on('change', function () { table.draw(); });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-type').val('all');
                $('#filter-status').val('all');
                table.draw();
            });

            $('#btn-sync-table, #btn-refresh-table').on('click', function() {
                const btn = $(this);
                const icon = btn.find('i, svg');
                btn.prop('disabled', true);
                icon.addClass('icon-spin');
                table.ajax.reload(function() {
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
                }, false);
            });

            $(document).on('click', '.btn-view-item', function() {
                const btn = $(this);
                const uuid = btn.data('id');

                function renderModal(bp) {
                    $('#modal-bp-title').text(bp.card_name || 'Business Partner Details');
                    $('#modal-bp-cardcode').text(`Card Code: ${bp.card_code || '-'}`);
                    $('#modal-bp-type-badge').html(getTypeBadge(bp.card_type));
                    $('#modal-bp-group').text(bp.group_name || 'General');
                    $('#modal-bp-phone').text(bp.phone || '-');
                    $('#modal-bp-email').text(bp.email || '-');
                    $('#modal-bp-balance').text(`$${(parseFloat(bp.balance) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
                    $('#modal-bp-limit').text(`$${(parseFloat(bp.credit_limit) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
                    $('#modal-bp-terms').text(bp.payment_terms || 'Standard');
                    $('#modal-bp-vat').text(bp.vat_num || 'N/A');
                    $('#modal-bp-status').html(getStatusBadge(bp.status));

                    const modalEl = document.getElementById('modal-bp-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && rowData.card_code) {
                    renderModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('master-data/business-partners') }}/${uuid}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) renderModal(res.data);
                    },
                    complete: function() { btn.prop('disabled', false); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initBpTable);
    </script>
</x-app-layout>

