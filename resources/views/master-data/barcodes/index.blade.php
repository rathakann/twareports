<x-app-layout title="Barcode · Master Data · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Master Data</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Barcode</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Barcodes (OBCD)"
        section="Master Data"
        icon="barcode"
        description="SAP Barcode master records (OBCD) linking product SKU items with international EAN13, Code 128, and internal scan codes.">
        <x-slot:actions>
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#bc-filter-card" title="Toggle Filters" aria-expanded="true">
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
    <div id="bc-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-type" class="form-label small fw-semibold text-secondary mb-1">Symbology / Type</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="scan"></i>
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
                <table id="bc-table" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>Barcode</th>
                            <th>Packaging UoM</th>
                            <th class="text-center">Symbology</th>
                            <th class="text-center">Default</th>
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
    <div class="modal fade" id="modal-bc-details" tabindex="-1" aria-labelledby="modalBcLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i data-lucide="barcode" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modal-bc-title">Barcode Details</h5>
                            <span class="small text-secondary" id="modal-bc-code-sub">Code</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="bg-light p-3 rounded-3 text-center mb-3">
                        <span class="text-secondary small d-block mb-1">Scannable Barcode Value</span>
                        <div class="fw-bold text-dark font-monospace fs-3 py-2 bg-white rounded border d-inline-block px-4" id="modal-bc-val">
                            8851234567890
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Target Item Code</span>
                                <span class="fw-bold text-dark fs-5 font-monospace" id="modal-bc-itemcode">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Packaging Unit of Measure</span>
                                <span class="fw-semibold text-dark fs-6" id="modal-bc-uom">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <span class="text-secondary small d-block">Item Full Description</span>
                        <p class="fw-semibold text-dark mb-0 fs-6" id="modal-bc-desc">-</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Symbology</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary fs-6 mt-1" id="modal-bc-symbology">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Primary Default</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-6 mt-1" id="modal-bc-default">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Status</span>
                                <div id="modal-bc-status" class="mt-1"></div>
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
        function initBcTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initBcTable, 50);
                return;
            }

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'active') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>';
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Inactive</span>';
            };

            const table = $('#bc-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master-data.barcodes.index') }}',
                    data: function (d) {
                        d.barcode_type = $('#filter-type').val() || 'all';
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
                        data: 'item_code',
                        name: 'item_code',
                        render: function(data) {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary font-monospace">${data}</span>`;
                        }
                    },
                    {
                        data: 'item_name',
                        name: 'item_name',
                        render: function(data) { return `<span class="fw-semibold text-dark">${data}</span>`; }
                    },
                    {
                        data: 'barcode',
                        name: 'barcode',
                        render: function(data) {
                            return `<span class="font-monospace fw-bold text-dark px-2 py-0.5 bg-light rounded border">${data}</span>`;
                        }
                    },
                    { data: 'uom_code', name: 'uom_code', render: function(d) { return d || '-'; } },
                    {
                        data: 'barcode_type',
                        name: 'barcode_type',
                        className: 'text-center',
                        render: function(d) {
                            return `<span class="badge bg-secondary bg-opacity-10 text-secondary">${d}</span>`;
                        }
                    },
                    {
                        data: 'is_default',
                        name: 'is_default',
                        className: 'text-center',
                        render: function(d) {
                            return d
                                ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Default</span>'
                                : '<span class="badge bg-light text-muted border">Alt</span>';
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

                function renderModal(bc) {
                    $('#modal-bc-title').text(bc.item_name || 'Barcode Details');
                    $('#modal-bc-code-sub').text(`Item Code: ${bc.item_code || '-'}`);
                    $('#modal-bc-val').text(bc.barcode || '-');
                    $('#modal-bc-itemcode').text(bc.item_code || '-');
                    $('#modal-bc-uom').text(bc.uom_code || 'Base Unit');
                    $('#modal-bc-desc').text(bc.item_name || '-');
                    $('#modal-bc-symbology').text(bc.barcode_type || 'EAN13');
                    $('#modal-bc-default').text(bc.is_default ? 'Primary Default Barcode' : 'Secondary Code');
                    $('#modal-bc-status').html(getStatusBadge(bc.status));

                    const modalEl = document.getElementById('modal-bc-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && rowData.barcode) {
                    renderModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('master-data/barcodes') }}/${uuid}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) renderModal(res.data);
                    },
                    complete: function() { btn.prop('disabled', false); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initBcTable);
    </script>
</x-app-layout>

