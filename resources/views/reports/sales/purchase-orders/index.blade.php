<x-app-layout title="Sales Purchase Orders Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Sales</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Purchase Order</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Sales Purchase Order Report"
        section="Sales Reports"
        icon="file-text"
        description="Review inbound client purchase orders, contract bookings, delivery pipelines, and sales revenue realization.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#spo-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#spo-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="spo-definition-card"
        title="Sales Orders Report"
        subtitle="Customer Sales Commitments · Commercial Bookings & Pipeline"
        icon="file-text"
        standard="Commercial Revenue Pipeline Standard"
        definition="The Sales Orders report logs and tracks confirmed commercial purchasing commitments received from wholesale buyers, fleet operators, and industrial customers."
        purpose="Forecasts sales pipeline velocity, tracks delivery fulfillment commitments, and monitors customer account purchasing trends across all commercial fuel categories."
        equation="Total Order Bookings = Confirmed Order Volume × Contract Price"
        :indicators="['Pipeline Bookings', 'Fulfillment Rate', 'Customer Account Volume', 'Delivery Scheduling']"
    />

    <!-- Filters Section -->
    <div id="spo-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-customer" class="form-label small fw-semibold text-secondary mb-1">Filter by Customer</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="users"></i>
                        <select id="filter-customer" class="form-select js-select2">
                            <option value="all">All Customers</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->customer_code }}">{{ $c->customer_name }} ({{ $c->customer_code }})</option>
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
                <table id="sales-po-table" data-export-module="reports.sales.purchase-orders" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Order #</th>
                            <th>Customer Ref #</th>
                            <th>Customer</th>
                            <th>Doc Date</th>
                            <th>Delivery Date</th>
                            <th>Sales Rep</th>
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

    <!-- Sales PO Details Modal -->
    <div class="modal fade" id="modal-order-details" tabindex="-1" aria-labelledby="modal-order-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-order-details-title">
                        <i data-lucide="file-text" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Sales Order Details: <span id="modal-order-num" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Customer Details</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-customer-name">-</div>
                                <div class="text-muted small">Customer Code: <span id="modal-customer-code" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Customer Ref #: <span id="modal-cust-ref" class="fw-medium text-primary font-monospace">-</span></div>
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
                                    <span class="text-muted small">Sales Rep:</span>
                                    <span class="fw-medium small text-dark" id="modal-sales-rep">-</span>
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
                        <h6 class="fw-semibold text-secondary small text-uppercase mb-1">Remarks</h6>
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
        function initSalesPurchaseOrdersTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initSalesPurchaseOrdersTable, 50);
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

            const table = $('#sales-po-table').DataTable({
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
                    url: '{{ route('reports.sales.purchase-orders.index') }}',
                    data: function (d) {
                        d.customer = $('#filter-customer').val() || 'all';
                        d.status = $('#filter-status').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    }
                },
                order: [[5, 'desc']],
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
                        data: 'customer_ref_num',
                        name: 'customer_ref_num',
                        render: function(data) {
                            return data ? `<span class="font-monospace text-secondary">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-dark">${data}</span>
                                    <small class="text-secondary font-monospace">${row.customer_code || ''}</small>
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
                        data: 'sales_employee',
                        name: 'sales_employee',
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

            $('#filter-customer, #filter-status, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-customer').val('all').trigger('change');
                $('#filter-status').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            // View Details Modal Handler
            $(document).on('click', '.btn-view-order', function() {
                const orderId = $(this).data('id');
                const btn = $(this);
                btn.prop('disabled', true);

                $.ajax({
                    url: `/reports/sales/purchase-orders/${orderId}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) {
                            const ord = res.data;
                            $('#modal-order-num').text(ord.doc_num);
                            $('#modal-cust-ref').text(ord.customer_ref_num || 'None');
                            $('#modal-customer-name').text(ord.customer_name);
                            $('#modal-customer-code').text(ord.customer_code);
                            $('#modal-payment-terms').text(ord.payment_terms || 'Net 30');
                            $('#modal-doc-date').text(formatDateText(ord.doc_date));
                            $('#modal-delivery-date').text(formatDateText(ord.delivery_date));
                            $('#modal-sales-rep').text(ord.sales_employee || '-');
                            $('#modal-status-badge').html(getStatusBadge(ord.status));
                            $('#modal-subtotal').text(formatMoney(ord.subtotal, ord.currency));
                            $('#modal-tax').text(formatMoney(ord.tax_amount, ord.currency));
                            $('#modal-discount').text(formatMoney(ord.discount_amount, ord.currency));
                            $('#modal-total').text(formatMoney(ord.doc_total, ord.currency));
                            $('#modal-remarks').text(ord.remarks || 'No remarks provided.');

                            const modal = new bootstrap.Modal(document.getElementById('modal-order-details'));
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

        document.addEventListener('DOMContentLoaded', initSalesPurchaseOrdersTable);
    </script>
</x-app-layout>
