<x-app-layout title="Items · Master Data · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Master Data</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Items</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Items (OITM)"
        section="Master Data"
        icon="package"
        description="SAP Item Master Data (OITM) catalog records, stock levels, valuation, sales/purchasing status, and pricing standards.">
        <x-slot:actions>
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#items-filter-card" title="Toggle Filters" aria-expanded="true">
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
    <div id="items-filter-card" class="page-card mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-group" class="form-label small fw-semibold text-secondary mb-1">Item Group</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="tags"></i>
                        <select id="filter-group" class="form-select">
                            <option value="all">All Item Groups</option>
                            @foreach ($itemGroups as $ig)
                                <option value="{{ $ig }}">{{ $ig }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-class" class="form-label small fw-semibold text-secondary mb-1">Item Class</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="layers"></i>
                        <select id="filter-class" class="form-select">
                            <option value="all">All Item Classes</option>
                            @foreach ($itemClasses as $ic)
                                <option value="{{ $ic }}">{{ $ic }}</option>
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
                <div class="col-md-3 col-12 d-flex align-items-end justify-content-md-end">
                    <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" title="Reset all filters">
                        <i data-lucide="rotate-ccw" style="width: 16px; height: 16px;"></i>
                        <span>Reset Filters</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="page-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="items-table" class="table table-bordered table-hover nowrap text-nowrap align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>Item Group</th>
                            <th>Item Class</th>
                            <th class="text-center">UoM</th>
                            <th class="text-end">In Stock</th>
                            <th class="text-end">Committed</th>
                            <th class="text-end">On Order</th>
                            <th class="text-end">Cost Price</th>
                            <th class="text-end">Sales Price</th>
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
    <div class="modal fade" id="modal-item-details" tabindex="-1" aria-labelledby="modalItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i data-lucide="package" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modal-item-title">Item Details</h5>
                            <span class="small text-secondary" id="modal-item-code-sub">Code</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-5">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Item Code (ItemCode)</span>
                                <span class="fw-bold text-dark fs-5 font-monospace" id="modal-item-code">-</span>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Item Name / Description</span>
                                <span class="fw-bold text-dark fs-6" id="modal-item-name">-</span>
                                <small class="text-secondary d-block mt-0.5" id="modal-item-foreign">-</small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Item Group</span>
                                <span class="fw-semibold text-dark" id="modal-item-group">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Item Class</span>
                                <span class="fw-semibold text-dark" id="modal-item-class">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Primary Barcode</span>
                                <span class="font-monospace fw-bold text-dark" id="modal-item-barcode">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Item Roles & Badges -->
                    <div class="card border mb-3">
                        <div class="card-header bg-transparent py-2">
                            <h6 class="card-title mb-0 small fw-semibold text-uppercase text-secondary">SAP Item Roles & Category Flags</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap gap-2" id="modal-item-flags">
                                <!-- Flags inserted via JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Stock Levels -->
                    <div class="card border mb-3">
                        <div class="card-header bg-transparent py-2">
                            <h6 class="card-title mb-0 small fw-semibold text-uppercase text-secondary">Current Inventory Levels (All Warehouses)</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2 text-center">
                                <div class="col-sm-3 border-end">
                                    <small class="text-secondary d-block">In Stock (OnHand)</small>
                                    <span class="fw-bold fs-5 text-dark font-monospace" id="modal-item-onhand">0</span>
                                </div>
                                <div class="col-sm-3 border-end">
                                    <small class="text-secondary d-block">Committed (Reserved)</small>
                                    <span class="fw-bold fs-5 text-warning font-monospace" id="modal-item-committed">0</span>
                                </div>
                                <div class="col-sm-3 border-end">
                                    <small class="text-secondary d-block">On Order (PO)</small>
                                    <span class="fw-bold fs-5 text-info font-monospace" id="modal-item-onorder">0</span>
                                </div>
                                <div class="col-sm-3">
                                    <small class="text-secondary d-block">Available to Sell</small>
                                    <span class="fw-bold fs-5 text-success font-monospace" id="modal-item-available">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Unit Cost Price</small>
                                <span class="fw-bold fs-6 text-dark font-monospace" id="modal-item-cost">$0.00</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Standard Sales Price</small>
                                <span class="fw-bold fs-6 text-success font-monospace" id="modal-item-sales">$0.00</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-secondary d-block">Default Warehouse</small>
                                <span class="fw-semibold text-dark font-monospace" id="modal-item-whs">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Remarks</span>
                                <span class="fw-normal text-secondary" id="modal-item-remarks">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Status</span>
                                <div id="modal-item-status" class="mt-1"></div>
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
        function initItemsTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initItemsTable, 50);
                return;
            }

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'active') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>';
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Inactive</span>';
            };

            const table = $('#items-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master-data.items.index') }}',
                    data: function (d) {
                        d.item_group = $('#filter-group').val() || 'all';
                        d.item_class = $('#filter-class').val() || 'all';
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
                        render: function(data, type, row) {
                            return `<span class="fw-semibold text-dark">${data}</span>${row.foreign_name ? `<br><small class="text-secondary">${row.foreign_name}</small>` : ''}`;
                        }
                    },
                    {
                        data: 'itms_grp_nam',
                        name: 'itms_grp_nam',
                        render: function(d) { return d || '-'; }
                    },
                    {
                        data: 'item_class',
                        name: 'item_class',
                        className: 'text-center',
                        render: function(d) {
                            return `<span class="badge bg-light text-dark border">${d || 'Inventory'}</span>`;
                        }
                    },
                    {
                        data: 'uom_code',
                        name: 'uom_code',
                        className: 'text-center',
                        render: function(d) {
                            return `<span class="badge bg-secondary bg-opacity-10 text-secondary font-monospace">${d || 'PC'}</span>`;
                        }
                    },
                    {
                        data: 'on_hand',
                        name: 'on_hand',
                        className: 'text-end',
                        render: function(d) {
                            const val = parseFloat(d) || 0;
                            return `<span class="font-monospace fw-semibold ${val > 0 ? 'text-dark' : 'text-danger'}">${val.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>`;
                        }
                    },
                    {
                        data: 'is_committed',
                        name: 'is_committed',
                        className: 'text-end',
                        render: function(d) {
                            const val = parseFloat(d) || 0;
                            return `<span class="font-monospace text-secondary">${val.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>`;
                        }
                    },
                    {
                        data: 'on_order',
                        name: 'on_order',
                        className: 'text-end',
                        render: function(d) {
                            const val = parseFloat(d) || 0;
                            return `<span class="font-monospace text-info">${val.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>`;
                        }
                    },
                    {
                        data: 'cost_price',
                        name: 'cost_price',
                        className: 'text-end',
                        render: function(d) {
                            const val = parseFloat(d) || 0;
                            return `<span class="font-monospace">$${val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 })}</span>`;
                        }
                    },
                    {
                        data: 'sales_price',
                        name: 'sales_price',
                        className: 'text-end',
                        render: function(d) {
                            const val = parseFloat(d) || 0;
                            return `<span class="font-monospace fw-semibold text-success">$${val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 })}</span>`;
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

            $('#filter-group, #filter-class, #filter-status').on('change', function () { table.draw(); });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-group').val('all');
                $('#filter-class').val('all');
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

                function renderModal(item) {
                    $('#modal-item-title').text(item.item_name || 'Item Details');
                    $('#modal-item-code-sub').text(`Code: ${item.item_code || '-'}`);
                    $('#modal-item-code').text(item.item_code || '-');
                    $('#modal-item-name').text(item.item_name || '-');
                    $('#modal-item-foreign').text(item.foreign_name ? `Foreign Name: ${item.foreign_name}` : 'No foreign/local name set.');
                    $('#modal-item-group').text(item.itms_grp_nam || item.itms_grp_cod || '-');
                    $('#modal-item-class').text(item.item_class || '-');
                    $('#modal-item-barcode').text(item.barcode || 'N/A');

                    // Flags
                    let flagsHtml = '';
                    if (item.is_inventory_item) flagsHtml += '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Inventory Item</span>';
                    if (item.is_sales_item) flagsHtml += '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Sales Item</span>';
                    if (item.is_purchase_item) flagsHtml += '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Purchase Item</span>';
                    if (item.is_asset_item) flagsHtml += '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Fixed Asset</span>';
                    if (!flagsHtml) flagsHtml = '<span class="badge bg-light text-muted border">None</span>';
                    $('#modal-item-flags').html(flagsHtml);

                    // Stock
                    const onHand = parseFloat(item.on_hand) || 0;
                    const committed = parseFloat(item.is_committed) || 0;
                    const onOrder = parseFloat(item.on_order) || 0;
                    const available = onHand - committed + onOrder;

                    $('#modal-item-onhand').text(onHand.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                    $('#modal-item-committed').text(committed.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                    $('#modal-item-onorder').text(onOrder.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                    $('#modal-item-available').text(available.toLocaleString(undefined, { minimumFractionDigits: 2 }));

                    // Prices
                    const cost = parseFloat(item.cost_price) || 0;
                    const sales = parseFloat(item.sales_price) || 0;
                    $('#modal-item-cost').text('$' + cost.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 }));
                    $('#modal-item-sales').text('$' + sales.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 }));
                    $('#modal-item-whs').text(item.default_whs || 'WH01');

                    $('#modal-item-remarks').text(item.remarks || 'Standard SAP item master.');
                    $('#modal-item-status').html(getStatusBadge(item.status));

                    const modalEl = document.getElementById('modal-item-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && rowData.item_code) {
                    renderModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('master-data/items') }}/${uuid}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) renderModal(res.data);
                    },
                    complete: function() { btn.prop('disabled', false); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initItemsTable);
    </script>
</x-app-layout>

