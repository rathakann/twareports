<x-app-layout title="Deposits Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Sales</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Deposit</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Deposit Report"
        section="Sales Reports"
        icon="piggy-bank"
        description="Audit banking deposits, treasury slips, cash clearing, and reconciliation statements.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#dep-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#dep-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="dep-definition-card"
        title="Bank Deposits Report"
        subtitle="Banking Operations · Treasury Clearance & Account Reconciliation"
        icon="piggy-bank"
        standard="Cash & Treasury Internal Control Standard"
        definition="The Bank Deposits report tracks all deposits of collections and cash receipts lodged into verified commercial banking accounts, matching daily collections to bank ledger statements."
        purpose="Ensures cash physical security, tracks bank transit settlement delays, enforces complete book-to-bank statement reconciliation, and eliminates unrecorded cash leakage."
        equation="Reconciled Bank Balance = Total Book Deposits - Deposits in Transit + Bank Adjustments"
        :indicators="['Reconciliation Rate', 'Bank Account Allocation', 'Transit Clearances', 'Deposit Consistency']"
    />

    <!-- Filters Section -->
    <div id="dep-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-bank" class="form-label small fw-semibold text-secondary mb-1">Filter by Bank</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="landmark"></i>
                        <select id="filter-bank" class="form-select js-select2">
                            <option value="all">All Banks</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank }}">{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label for="filter-type" class="form-label small fw-semibold text-secondary mb-1">Deposit Type</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="wallet"></i>
                        <select id="filter-type" class="form-select js-select2">
                            <option value="all">All Types</option>
                            @foreach ($depositTypes as $dt)
                                <option value="{{ $dt }}">{{ $dt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label for="filter-recon" class="form-label small fw-semibold text-secondary mb-1">Reconciliation</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="check-check"></i>
                        <select id="filter-recon" class="form-select js-select2">
                            <option value="all">All Statuses</option>
                            @foreach ($reconciliationStatuses as $rc)
                                <option value="{{ $rc }}">{{ $rc }}</option>
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
                <table id="deposits-table" data-export-module="reports.sales.deposits" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Deposit #</th>
                            <th>Deposit Date</th>
                            <th>Bank Name</th>
                            <th>Bank Account</th>
                            <th>Type</th>
                            <th>Journal Ref</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-center">Reconciliation</th>
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

    <!-- Deposit Details Modal -->
    <div class="modal fade" id="modal-deposit-details" tabindex="-1" aria-labelledby="modal-deposit-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-deposit-details-title">
                        <i data-lucide="piggy-bank" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Deposit Record: <span id="modal-dep-num" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Bank & Account</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-bank-name">-</div>
                                <div class="text-muted small">Account #: <span id="modal-bank-account" class="fw-medium font-monospace text-dark">-</span></div>
                                <div class="text-muted small mt-1">Journal Ref: <span id="modal-journal-ref" class="fw-medium text-primary font-monospace">-</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Deposit Status</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Deposit Date:</span>
                                    <span class="fw-medium small text-dark" id="modal-dep-date">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Type:</span>
                                    <span class="fw-medium small text-dark" id="modal-dep-type">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Reconciliation:</span>
                                    <span id="modal-recon-badge">-</span>
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
                            <small class="text-secondary text-uppercase fw-semibold d-block mb-1">Total Deposited Amount</small>
                            <span class="fw-bold fs-2 text-primary font-monospace" id="modal-total-amount">$0.00</span>
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
        function initDepositsTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initDepositsTable, 50);
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
                if (s === 'posted') return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Posted</span>';
                if (s === 'draft') return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Draft</span>';
                if (s === 'cancelled') return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Cancelled</span>';
                return `<span class="badge bg-light text-dark">${status}</span>`;
            };

            const getReconBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'reconciled') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i data-lucide="check" style="width:12px;height:12px;" class="me-1"></i>Reconciled</span>';
                return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i data-lucide="clock" style="width:12px;height:12px;" class="me-1"></i>Unreconciled</span>';
            };

            const table = $('#deposits-table').DataTable({
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
                    url: '{{ route('reports.sales.deposits.index') }}',
                    data: function (d) {
                        d.bank_name = $('#filter-bank').val() || 'all';
                        d.deposit_type = $('#filter-type').val() || 'all';
                        d.reconciliation_status = $('#filter-recon').val() || 'all';
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
                        data: 'deposit_num',
                        name: 'deposit_num',
                        render: function(data) {
                            return `<span class="fw-semibold text-primary font-monospace">${data}</span>`;
                        }
                    },
                    {
                        data: 'deposit_date',
                        name: 'deposit_date',
                        render: function(data) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name',
                        render: function(data) {
                            return `<span class="fw-semibold text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: 'bank_account',
                        name: 'bank_account',
                        render: function(data) {
                            return `<span class="small font-monospace text-secondary">${data}</span>`;
                        }
                    },
                    {
                        data: 'deposit_type',
                        name: 'deposit_type',
                        render: function(data) {
                            return `<span class="badge bg-light text-dark border">${data}</span>`;
                        }
                    },
                    {
                        data: 'journal_ref',
                        name: 'journal_ref',
                        render: function(data) {
                            return data ? `<span class="font-monospace text-secondary small">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        className: 'text-end fw-bold font-monospace text-primary',
                        render: function(data, type, row) {
                            return formatMoney(data, row.currency);
                        }
                    },
                    {
                        data: 'reconciliation_status',
                        name: 'reconciliation_status',
                        className: 'text-center',
                        render: function(data) {
                            return getReconBadge(data);
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

            $('#filter-bank, #filter-type, #filter-recon, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-bank').val('all').trigger('change');
                $('#filter-type').val('all').trigger('change');
                $('#filter-recon').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            // Deposit View Detail Modal Handler
            $(document).on('click', '.btn-view-deposit', function() {
                const depId = $(this).data('id');
                const btn = $(this);
                btn.prop('disabled', true);

                $.ajax({
                    url: `/reports/sales/deposits/${depId}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) {
                            const dep = res.data;
                            $('#modal-dep-num').text(dep.deposit_num);
                            $('#modal-bank-name').text(dep.bank_name);
                            $('#modal-bank-account').text(dep.bank_account);
                            $('#modal-journal-ref').text(dep.journal_ref || 'None');
                            $('#modal-dep-date').text(formatDateText(dep.deposit_date));
                            $('#modal-dep-type').text(dep.deposit_type);
                            $('#modal-recon-badge').html(getReconBadge(dep.reconciliation_status));
                            $('#modal-status-badge').html(getStatusBadge(dep.status));
                            $('#modal-total-amount').text(formatMoney(dep.total_amount, dep.currency));
                            $('#modal-remarks').text(dep.remarks || 'No remarks provided.');

                            const modal = new bootstrap.Modal(document.getElementById('modal-deposit-details'));
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

        document.addEventListener('DOMContentLoaded', initDepositsTable);
    </script>
</x-app-layout>
