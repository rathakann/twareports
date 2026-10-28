<x-app-layout title="Bill of Materials (BOM) · Master Data · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Master Data</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Bom</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Bill of Materials (BOM / OITT)"
        section="Master Data"
        icon="git-fork"
        description="SAP Product Trees (OITT) and multi-component BOM assemblies for kit bundles, decanting operations, and production batches.">
        <x-slot:actions>
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#bom-filter-card" title="Toggle Filters" aria-expanded="true">
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
    <div id="bom-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-type" class="form-label small fw-semibold text-secondary mb-1">BOM Category / Tree Type</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="network"></i>
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
                <table id="bom-table" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                            <th>Tree Code</th>
                            <th>Product / Assembly Name</th>
                            <th class="text-center">Tree Type</th>
                            <th class="text-center">Batch Yield</th>
                            <th class="text-center">Components</th>
                            <th class="text-end">Total Cost</th>
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
    <div class="modal fade" id="modal-bom-details" tabindex="-1" aria-labelledby="modalBomLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i data-lucide="git-fork" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modal-bom-title">BOM Tree Details</h5>
                            <span class="small text-secondary" id="modal-bom-code-sub">Code</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Parent Tree Code</span>
                                <span class="fw-bold text-dark fs-5 font-monospace" id="modal-bom-code">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Tree Category</span>
                                <div id="modal-bom-type-badge" class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <span class="text-secondary small d-block">Finished Product Name</span>
                        <p class="fw-bold text-dark fs-5 mb-0" id="modal-bom-name">-</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Output Batch Yield</small>
                                <span class="fw-bold fs-5 text-dark font-monospace" id="modal-bom-yield">1.0000</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Components Count</small>
                                <span class="fw-bold fs-5 text-primary" id="modal-bom-components">0 items</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Est. Assembly Cost</small>
                                <span class="fw-bold fs-5 text-success font-monospace" id="modal-bom-cost">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Warehouse Storage Location</span>
                                <span class="fw-semibold text-dark font-monospace" id="modal-bom-whs">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Status</span>
                                <div id="modal-bom-status" class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3">
                        <span class="text-secondary small d-block">Assembly Specification Notes</span>
                        <p class="text-muted small mb-0" id="modal-bom-remarks">None</p>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initBomTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initBomTable, 50);
                return;
            }

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'active') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>';
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Inactive</span>';
            };

            const getTypeBadge = (type) => {
                const t = String(type || '').toLowerCase();
                if (t === 'sales') return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Sales Kit</span>';
                if (t === 'production') return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Production</span>';
                if (t === 'assembly') return '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Assembly</span>';
                return `<span class="badge bg-light text-secondary border">${type}</span>`;
            };

            const table = $('#bom-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master-data.boms.index') }}',
                    data: function (d) {
                        d.tree_type = $('#filter-type').val() || 'all';
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
                        data: 'tree_code',
                        name: 'tree_code',
                        render: function(data) {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary font-monospace">${data}</span>`;
                        }
                    },
                    {
                        data: 'tree_name',
                        name: 'tree_name',
                        render: function(data) { return `<span class="fw-semibold text-dark">${data}</span>`; }
                    },
                    {
                        data: 'tree_type',
                        name: 'tree_type',
                        className: 'text-center',
                        render: function(d) { return getTypeBadge(d); }
                    },
                    {
                        data: 'output_qty',
                        name: 'output_qty',
                        className: 'text-center',
                        render: function(d, type, row) {
                            const qty = parseFloat(d) || 1;
                            return `<span class="font-monospace fw-medium">${qty} ${row.uom_code || 'SET'}</span>`;
                        }
                    },
                    {
                        data: 'components_count',
                        name: 'components_count',
                        className: 'text-center',
                        render: function(d) {
                            return `<span class="badge bg-secondary bg-opacity-10 text-secondary">${d} lines</span>`;
                        }
                    },
                    {
                        data: 'total_cost',
                        name: 'total_cost',
                        className: 'text-end',
                        render: function(d) {
                            const cost = parseFloat(d) || 0;
                            return `<span class="fw-semibold font-monospace">$${cost.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`;
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

                function renderModal(bom) {
                    $('#modal-bom-title').text(bom.tree_name || 'BOM Tree Details');
                    $('#modal-bom-code-sub').text(`Tree Code: ${bom.tree_code || '-'}`);
                    $('#modal-bom-code').text(bom.tree_code || '-');
                    $('#modal-bom-name').text(bom.tree_name || '-');
                    $('#modal-bom-type-badge').html(getTypeBadge(bom.tree_type));
                    $('#modal-bom-yield').text(`${parseFloat(bom.output_qty) || 1} ${bom.uom_code || 'SET'}`);
                    $('#modal-bom-components').text(`${bom.components_count || 0} component lines`);
                    $('#modal-bom-cost').text(`$${(parseFloat(bom.total_cost) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
                    $('#modal-bom-whs').text(bom.whs_code || 'Unassigned');
                    $('#modal-bom-status').html(getStatusBadge(bom.status));
                    $('#modal-bom-remarks').text(bom.remarks || 'No remarks provided.');

                    const modalEl = document.getElementById('modal-bom-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && rowData.tree_code) {
                    renderModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('master-data/boms') }}/${uuid}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) renderModal(res.data);
                    },
                    complete: function() { btn.prop('disabled', false); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initBomTable);
    </script>
</x-app-layout>

