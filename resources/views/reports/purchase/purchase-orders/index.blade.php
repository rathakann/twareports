<x-app-layout title="Purchase Orders Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Purchase</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Purchase Order</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Purchase Order Report"
        section="Purchase Reports"
        icon="shopping-cart"
        description="Comprehensive analysis and tracking of procurement purchase orders, vendor obligations, and order fulfillment.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#po-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#po-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="po-definition-card"
        title="Purchase Orders Report"
        subtitle="Procurement Tracking · Vendor Commitments & Fulfillment"
        icon="shopping-cart"
        standard="Procurement & Supply Chain Standard"
        definition="The Purchase Orders report logs and audits all legally binding commercial commitments issued to suppliers for inventory stocks, fuels, and services prior to billing."
        purpose="Monitors procurement commitments, delivery fulfillment schedules, and outstanding vendor liabilities to maintain inventory continuity and spend control."
        equation="Total PO Value = Subtotal + Applicable Taxes - Trade Discounts"
        :indicators="['Open Commitments', 'Vendor Compliance', 'Delivery Schedules', 'Fulfillment Velocity']"
    />

    <!-- Filters Section -->
    <div id="po-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-vendor" class="form-label small fw-semibold text-secondary mb-1">Filter by Vendor</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="building-2"></i>
                        <select id="filter-vendor" class="form-select js-select2">
                            <option value="all">All Vendors</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->vendor_code }}">{{ $vendor->vendor_name }} ({{ $vendor->vendor_code }})</option>
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
                            @foreach ($statuses as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <x-reports.date-filter id="filter-created-date" label="Date Range" placeholder="YYYY-MM-DD to YYYY-MM-DD" />
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

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="purchase-orders-table" data-export-module="reports.purchase.purchase-orders" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>PO Number</th>
                            <th>Vendor</th>
                            <th>Doc Date</th>
                            <th>Delivery Date</th>
                            <th>Buyer</th>
                            <th class="text-end">Total Amount</th>
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

    <!-- PO Details Modal -->
    <div class="modal fade" id="modal-po-details" tabindex="-1" aria-labelledby="modal-po-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-po-details-title">
                        <i data-lucide="file-text" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Purchase Order Details: <span id="modal-po-num" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Vendor Information</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-vendor-name">-</div>
                                <div class="text-muted small">Vendor Code: <span id="modal-vendor-code" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Payment Terms: <span id="modal-payment-terms" class="fw-medium text-dark">-</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Order Information</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Doc Date:</span>
                                    <span class="fw-medium small text-dark" id="modal-doc-date">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Delivery Date:</span>
                                    <span class="fw-medium small text-dark" id="modal-delivery-date">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Buyer / Agent:</span>
                                    <span class="fw-medium small text-dark" id="modal-buyer-name">-</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Status:</span>
                                    <span id="modal-status-badge">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-header bg-transparent py-2">
                            <h6 class="card-title mb-0 small fw-semibold text-uppercase text-secondary">Financial Summary</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-sm-3 col-6 text-center border-end">
                                    <small class="text-secondary d-block">Subtotal</small>
                                    <span class="fw-semibold fs-6 text-dark" id="modal-subtotal">$0.00</span>
                                </div>
                                <div class="col-sm-3 col-6 text-center border-end">
                                    <small class="text-secondary d-block">Tax Amount</small>
                                    <span class="fw-semibold fs-6 text-dark" id="modal-tax">$0.00</span>
                                </div>
                                <div class="col-sm-3 col-6 text-center border-end">
                                    <small class="text-secondary d-block">Discount</small>
                                    <span class="fw-semibold fs-6 text-danger" id="modal-discount">$0.00</span>
                                </div>
                                <div class="col-sm-3 col-6 text-center">
                                    <small class="text-secondary d-block">Total Amount</small>
                                    <span class="fw-bold fs-5 text-primary" id="modal-total">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3">
                        <h6 class="fw-semibold text-secondary small text-uppercase mb-1">Remarks / Notes</h6>
                        <p class="text-muted small mb-0" id="modal-remarks">None</p>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initPurchaseOrdersTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initPurchaseOrdersTable, 50);
                return;
            }

            if (window.initReportDateRangePicker) {
                window.initReportDateRangePicker('#filter-created-date');
            } else if (window.flatpickr) {
                flatpickr('#filter-created-date', {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'M d, Y',
                    allowInput: true
                });
            }

            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const formatDate = window.formatReportDate || function(v) { return v || '-'; };
            const formatDateText = window.formatReportDateText || function(v) { return v || '-'; };

            const formatMoney = (val, currency = 'USD') => {
                const num = parseFloat(val) || 0;
                return '$' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'open') return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Open</span>';
                if (s === 'closed') return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Closed</span>';
                if (s === 'pending') return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Pending</span>';
                if (s === 'cancelled') return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Cancelled</span>';
                return `<span class="badge bg-light text-dark">${status}</span>`;
            };

            const table = $('#purchase-orders-table').DataTable({
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
                    url: '{{ route('reports.purchase.purchase-orders.index') }}',
                    data: function (d) {
                        d.vendor = $('#filter-vendor').val() || 'all';
                        d.status = $('#filter-status').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    }
                },
                order: [[4, 'desc']],
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
                                if (typeof meta.settings.displayStart === 'number') start = meta.settings.displayStart;
                                else if (typeof meta.settings._iDisplayStart === 'number') start = meta.settings._iDisplayStart;
                            }
                            const rowIdx = (meta && typeof meta.row === 'number' && !isNaN(meta.row)) ? meta.row : 0;
                            return start + rowIdx + 1;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '60px'
                    },
                    {
                        data: 'doc_num',
                        name: 'doc_num',
                        render: function(data) {
                            return `<span class="fw-semibold text-primary font-monospace">${data}</span>`;
                        }
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendor_name',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-dark">${data}</span>
                                    <small class="text-secondary font-monospace">${row.vendor_code || ''}</small>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'doc_date',
                        name: 'doc_date',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'delivery_date',
                        name: 'delivery_date',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'buyer_name',
                        name: 'buyer_name',
                        render: function(data) {
                            return data ? `<span class="text-secondary">${data}</span>` : '-';
                        }
                    },
                    {
                        data: 'doc_total',
                        name: 'doc_total',
                        className: 'text-end fw-bold font-monospace text-dark',
                        render: function(data, type, row) {
                            return formatMoney(data, row.currency);
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(data) {
                            return getStatusBadge(data);
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

            $('#filter-vendor, #filter-status, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-vendor').val('all').trigger('change');
                $('#filter-status').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            // PO View Detail Modal Handler
            $(document).on('click', '.btn-view-po', function() {
                const poId = $(this).data('id');
                const btn = $(this);
                btn.prop('disabled', true);

                $.ajax({
                    url: `/reports/purchase/purchase-orders/${poId}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) {
                            const po = res.data;
                            $('#modal-po-num').text(po.doc_num);
                            $('#modal-vendor-name').text(po.vendor_name);
                            $('#modal-vendor-code').text(po.vendor_code);
                            $('#modal-payment-terms').text(po.payment_terms || 'Standard');
                            $('#modal-doc-date').text(formatDateText(po.doc_date));
                            $('#modal-delivery-date').text(formatDateText(po.delivery_date));
                            $('#modal-buyer-name').text(po.buyer_name || '-');
                            $('#modal-status-badge').html(getStatusBadge(po.status));
                            $('#modal-subtotal').text(formatMoney(po.subtotal, po.currency));
                            $('#modal-tax').text(formatMoney(po.tax_amount, po.currency));
                            $('#modal-discount').text(formatMoney(po.discount_amount, po.currency));
                            $('#modal-total').text(formatMoney(po.doc_total, po.currency));
                            $('#modal-remarks').text(po.remarks || 'No remarks provided.');

                            const modal = new bootstrap.Modal(document.getElementById('modal-po-details'));
                            modal.show();
                            if (window.lucide) window.lucide.createIcons();
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initPurchaseOrdersTable);
    </script>
</x-app-layout>
