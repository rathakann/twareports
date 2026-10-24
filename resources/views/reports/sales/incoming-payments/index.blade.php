<x-app-layout title="Incoming Payments Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Sales</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Incoming Payment</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Incoming Payment Report"
        section="Sales Reports"
        icon="wallet"
        description="Monitor customer inflows, receipts, wire remittances, and revenue collections.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#in-pay-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#in-pay-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="in-pay-definition-card"
        title="Incoming Payments Report"
        subtitle="Customer Receipts · Cash Collections & Settlements"
        icon="wallet"
        standard="Treasury & Cash Inflow Control"
        definition="The Incoming Payments report tracks all cash receipts and customer payments collected in settlement of issued sales invoices, contracts, and commercial deliveries."
        purpose="Measures cash collection velocity, confirms bank credit entries, verifies customer account reconciliation, and powers treasury cash forecasting."
        equation="Total Cash Receipts = Direct Bank Transfers + Check Clearances + Cash Collections"
        :indicators="['Collection Velocity', 'Unapplied Receipts', 'Customer Settlement Speed', 'Payment Method Mix']"
    />

    <!-- Filters Section -->
    <div id="in-pay-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
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
                <div class="col-md-2 col-sm-6">
                    <label for="filter-payment-method" class="form-label small fw-semibold text-secondary mb-1">Payment Method</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="wallet"></i>
                        <select id="filter-payment-method" class="form-select js-select2">
                            <option value="all">All Methods</option>
                            @foreach ($paymentMethods as $pm)
                                <option value="{{ $pm }}">{{ $pm }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
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
                <div class="col-md-2 col-12 d-flex align-items-end justify-content-md-end">
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
                <table id="incoming-payments-table" data-export-module="reports.sales.incoming-payments" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Payment #</th>
                            <th>Doc Date</th>
                            <th>Customer</th>
                            <th>Method</th>
                            <th>Ref #</th>
                            <th>Bank Account</th>
                            <th class="text-end">Amount Received</th>
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

    <!-- Incoming Payment Details Modal -->
    <div class="modal fade" id="modal-payment-details" tabindex="-1" aria-labelledby="modal-payment-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-payment-details-title">
                        <i data-lucide="wallet" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Incoming Payment: <span id="modal-pay-num" class="fw-bold text-dark"></span></span>
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
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Receipt Details</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Doc Date:</span>
                                    <span class="fw-medium small text-dark" id="modal-doc-date">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Payment Method:</span>
                                    <span class="fw-medium small text-dark" id="modal-method">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Reference #:</span>
                                    <span class="fw-medium small text-primary font-monospace" id="modal-ref-num">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Bank Account:</span>
                                    <span class="fw-medium small text-dark" id="modal-bank-account">-</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Status:</span>
                                    <span id="modal-status-badge">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-body p-4 text-center">
                            <small class="text-secondary text-uppercase fw-semibold d-block mb-1">Total Amount Received</small>
                            <span class="fw-bold fs-2 text-success font-monospace" id="modal-amount-received">$0.00</span>
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
        function initIncomingPaymentsTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initIncomingPaymentsTable, 50);
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
                if (s === 'cleared') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Cleared</span>';
                if (s === 'pending') return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Pending</span>';
                if (s === 'bounced') return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Bounced</span>';
                if (s === 'cancelled') return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Cancelled</span>';
                return `<span class="badge bg-light text-dark">${status}</span>`;
            };

            const table = $('#incoming-payments-table').DataTable({
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
                    url: '{{ route('reports.sales.incoming-payments.index') }}',
                    data: function (d) {
                        d.customer = $('#filter-customer').val() || 'all';
                        d.payment_method = $('#filter-payment-method').val() || 'all';
                        d.status = $('#filter-status').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    }
                },
                order: [[3, 'desc']],
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
                        data: 'payment_num',
                        name: 'payment_num',
                        render: function(data) {
                            return `<span class="fw-semibold text-primary font-monospace">${data}</span>`;
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
                        data: 'payment_method',
                        name: 'payment_method',
                        render: function(data) {
                            return `<span class="badge bg-light text-dark border">${data}</span>`;
                        }
                    },
                    {
                        data: 'reference_num',
                        name: 'reference_num',
                        render: function(data) {
                            return data ? `<span class="font-monospace text-secondary small">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'bank_account',
                        name: 'bank_account',
                        render: function(data) {
                            return data ? `<span class="small text-secondary">${data}</span>` : '-';
                        }
                    },
                    {
                        data: 'amount_received',
                        name: 'amount_received',
                        className: 'text-end fw-bold font-monospace text-success',
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

            $('#filter-customer, #filter-payment-method, #filter-status, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-customer').val('all').trigger('change');
                $('#filter-payment-method').val('all').trigger('change');
                $('#filter-status').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            // Payment View Detail Modal Handler
            $(document).on('click', '.btn-view-payment, .btn-view-payment *', function(e) {
                e.preventDefault();
                const btn = $(this).closest('.btn-view-payment');
                const payId = btn.data('id') || btn.attr('data-id');
                if (!payId) return;

                function renderPaymentModal(pay) {
                    $('#modal-pay-num').text(pay.payment_num);
                    $('#modal-customer-name').text(pay.customer_name);
                    $('#modal-customer-code').text(pay.customer_code);
                    $('#modal-doc-date').text(formatDateText(pay.doc_date));
                    $('#modal-method').text(pay.payment_method);
                    $('#modal-ref-num').text(pay.reference_num || '-');
                    $('#modal-bank-account').text(pay.bank_account || '-');
                    $('#modal-status-badge').html(getStatusBadge(pay.status));
                    $('#modal-amount-received').text(formatMoney(pay.amount_received, pay.currency));
                    $('#modal-remarks').text(pay.remarks || 'No remarks provided.');

                    const modalEl = document.getElementById('modal-payment-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && (rowData.payment_num || rowData.id)) {
                    renderPaymentModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `/reports/sales/incoming-payments/${payId}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) {
                            renderPaymentModal(res.data);
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initIncomingPaymentsTable);
    </script>
</x-app-layout>
