<x-app-layout title="AP Invoices Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Purchase</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">AP Invoice</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="AP Invoice Report"
        section="Purchase Reports"
        icon="receipt"
        description="Monitor vendor accounts payable invoices, aging, settlement status, and balance liabilities.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#inv-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#inv-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="inv-definition-card"
        title="AP Invoices Report"
        subtitle="Accounts Payable · Trade Liabilities & Settlement Monitoring"
        icon="receipt"
        standard="Accrual Accounting & Trade Liabilities Standard"
        definition="The Accounts Payable (AP) Invoices report compiles all formal vendor billing claims representing verified trade liabilities for received inventory and services."
        purpose="Manages cash outflow timing, tracks vendor payment credit terms (Net 30/60), prevents duplicate billing, and maintains strong supplier relationships."
        equation="Outstanding AP Balance = Total Invoiced - Disbursed Settlements - Withholding Tax"
        :indicators="['Payable Aging', 'Overdue Invoices', 'Payment Terms Compliance', 'Vendor Balances']"
    />

    <!-- Filters Section -->
    <div id="inv-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
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
                <table id="ap-invoices-table" data-export-module="reports.purchase.ap-invoices" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Invoice #</th>
                            <th>PO Ref</th>
                            <th>Vendor</th>
                            <th>Doc Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Doc Total</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Balance Due</th>
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

    <!-- AP Invoice Details Modal -->
    <div class="modal fade" id="modal-invoice-details" tabindex="-1" aria-labelledby="modal-invoice-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-invoice-details-title">
                        <i data-lucide="receipt" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>AP Invoice Details: <span id="modal-inv-num" class="fw-bold text-dark"></span></span>
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
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Invoice Attributes</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">PO Reference:</span>
                                    <span class="fw-medium small text-primary font-monospace" id="modal-po-ref">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Doc Date:</span>
                                    <span class="fw-medium small text-dark" id="modal-doc-date">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Due Date:</span>
                                    <span class="fw-medium small text-dark" id="modal-due-date">-</span>
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
                            <h6 class="card-title mb-0 small fw-semibold text-uppercase text-secondary">Financial Overview</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <div class="col-sm-4 col-6 text-center border-end">
                                    <small class="text-secondary d-block">Subtotal</small>
                                    <span class="fw-semibold fs-6 text-dark" id="modal-subtotal">$0.00</span>
                                </div>
                                <div class="col-sm-4 col-6 text-center border-end">
                                    <small class="text-secondary d-block">Tax Amount</small>
                                    <span class="fw-semibold fs-6 text-dark" id="modal-tax">$0.00</span>
                                </div>
                                <div class="col-sm-4 col-12 text-center">
                                    <small class="text-secondary d-block">Doc Total</small>
                                    <span class="fw-bold fs-5 text-primary" id="modal-total">$0.00</span>
                                </div>
                            </div>
                            <hr class="my-2 text-muted">
                            <div class="row g-2">
                                <div class="col-6 text-center border-end">
                                    <small class="text-secondary d-block">Amount Paid</small>
                                    <span class="fw-bold fs-5 text-success" id="modal-paid">$0.00</span>
                                </div>
                                <div class="col-6 text-center">
                                    <small class="text-secondary d-block">Balance Due</small>
                                    <span class="fw-bold fs-5 text-danger" id="modal-balance">$0.00</span>
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
        function initApInvoicesTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initApInvoicesTable, 50);
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
                if (s === 'paid') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Paid</span>';
                if (s === 'partially paid') return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Partially Paid</span>';
                if (s === 'unpaid') return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Unpaid</span>';
                if (s === 'overdue') return '<span class="badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25">Overdue</span>';
                if (s === 'cancelled') return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Cancelled</span>';
                return `<span class="badge bg-light text-dark">${status}</span>`;
            };

            const table = $('#ap-invoices-table').DataTable({
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
                    url: '{{ route('reports.purchase.ap-invoices.index') }}',
                    data: function (d) {
                        d.vendor = $('#filter-vendor').val() || 'all';
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
                        data: 'invoice_num',
                        name: 'invoice_num',
                        render: function(data) {
                            return `<span class="fw-semibold text-primary font-monospace">${data}</span>`;
                        }
                    },
                    {
                        data: 'po_reference',
                        name: 'po_reference',
                        render: function(data) {
                            return data ? `<span class="font-monospace text-secondary">${data}</span>` : '<span class="text-muted">-</span>';
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
                        data: 'due_date',
                        name: 'due_date',
                        render: function(data) {
                            return formatDate(data);
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
                        data: 'paid_amount',
                        name: 'paid_amount',
                        className: 'text-end fw-semibold font-monospace text-success',
                        render: function(data, type, row) {
                            return formatMoney(data, row.currency);
                        }
                    },
                    {
                        data: 'balance_due',
                        name: 'balance_due',
                        className: 'text-end fw-bold font-monospace text-danger',
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

            // Invoice View Detail Modal Handler
            $(document).on('click', '.btn-view-invoice', function() {
                const invId = $(this).data('id');
                const btn = $(this);
                btn.prop('disabled', true);

                $.ajax({
                    url: `/reports/purchase/ap-invoices/${invId}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) {
                            const inv = res.data;
                            $('#modal-inv-num').text(inv.invoice_num);
                            $('#modal-po-ref').text(inv.po_reference || 'Direct Invoice');
                            $('#modal-vendor-name').text(inv.vendor_name);
                            $('#modal-vendor-code').text(inv.vendor_code);
                            $('#modal-payment-terms').text(inv.payment_terms || 'Net 30');
                            $('#modal-doc-date').text(formatDateText(inv.doc_date));
                            $('#modal-due-date').text(formatDateText(inv.due_date));
                            $('#modal-status-badge').html(getStatusBadge(inv.status));
                            $('#modal-subtotal').text(formatMoney(inv.subtotal, inv.currency));
                            $('#modal-tax').text(formatMoney(inv.tax_amount, inv.currency));
                            $('#modal-total').text(formatMoney(inv.doc_total, inv.currency));
                            $('#modal-paid').text(formatMoney(inv.paid_amount, inv.currency));
                            $('#modal-balance').text(formatMoney(inv.balance_due, inv.currency));
                            $('#modal-remarks').text(inv.remarks || 'No remarks provided.');

                            const modal = new bootstrap.Modal(document.getElementById('modal-invoice-details'));
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

        document.addEventListener('DOMContentLoaded', initApInvoicesTable);
    </script>
</x-app-layout>
