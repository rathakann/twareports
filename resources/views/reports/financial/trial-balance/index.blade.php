<x-app-layout title="Trial Balance Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Financial</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Trial Balance</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Trial Balance Report"
        section="Financial Reports"
        icon="table-2"
        description="Verify mathematical accuracy of double-entry ledger accounts with debit and credit balance proofing.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#tb-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#tb-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="tb-definition-card"
        title="Trial Balance Report"
        subtitle="Internal General Ledger Integrity Audit"
        icon="book-marked"
        standard="Double-Entry Bookkeeping Verification Standard"
        definition="The Trial Balance is an internal accounting diagnostic worksheet compiling the aggregated ending debit and credit balances of every general ledger account as of a specific date. It verifies the fundamental arithmetic integrity of the double-entry accounting ledger."
        purpose="Confirms that total debit entries mathematically equal total credit entries across all accounts. Financial controllers and auditors utilize the trial balance as the essential pre-closing check before adjusting journal entries and generating the Balance Sheet and Income Statement."
        equation="Total Debit Balances = Total Credit Balances (Variance = $0.00)"
        :indicators="['Debits vs Credits Equality', 'Zero Balance Proof', 'Pre-Closing Audit', 'Account Accuracy']"
    />

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-blue rounded-3 flex-shrink-0">
                        <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Active Accounts</div>
                        <div class="fs-5 fw-bold text-dark">{{ $accountCount }} Accounts</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-green rounded-3 flex-shrink-0">
                        <i data-lucide="arrow-down-left" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Debits</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalDebits, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-rose rounded-3 flex-shrink-0">
                        <i data-lucide="arrow-up-right" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Credits</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalCredits, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-purple rounded-3 flex-shrink-0">
                        <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Balance Proof</div>
                        <div class="fs-5 fw-bold text-success">{{ $balanceDiff == 0 ? 'Balanced ($0.00)' : 'Diff: $'.number_format($balanceDiff, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div id="tb-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-classification" class="form-label small fw-semibold text-secondary mb-1">Account Classification</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="tag"></i>
                        <select id="filter-classification" class="form-select js-select2">
                            <option value="all">All Classifications</option>
                            @foreach ($classifications as $cls)
                                <option value="{{ $cls }}">{{ $cls }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-statement" class="form-label small fw-semibold text-secondary mb-1">Financial Statement</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="file-spreadsheet"></i>
                        <select id="filter-statement" class="form-select js-select2">
                            <option value="all">All Statements</option>
                            @foreach ($statements as $st)
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
                <table id="trial-balance-table" data-export-module="reports.financial.trial-balance" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Code</th>
                            <th>Account Name</th>
                            <th>Class</th>
                            <th>Statement</th>
                            <th class="text-end">Opening</th>
                            <th class="text-end text-primary">Debit</th>
                            <th class="text-end text-danger">Credit</th>
                            <th class="text-end">Ending Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Handled by Server-Side DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Trial Balance Account Details Modal -->
    <div class="modal fade" id="modal-tb-details" tabindex="-1" aria-labelledby="modal-tb-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-tb-details-title">
                        <i data-lucide="table-2" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Trial Balance Card: <span id="modal-acc-code" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Account Identification</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-acc-name">-</div>
                                <div class="text-muted small">Classification: <span id="modal-acc-class" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Statement: <span id="modal-acc-stmt" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Audit Date: <span id="modal-doc-date" class="fw-medium text-dark">-</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Debit / Credit Reconciliation</h6>
                                <div class="text-muted small">Opening Balance: <span id="modal-open-bal" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Period Debit: <span id="modal-debit-val" class="fw-bold text-primary font-monospace">-</span></div>
                                <div class="text-muted small mt-1">Period Credit: <span id="modal-credit-val" class="fw-bold text-danger font-monospace">-</span></div>
                                <div class="text-muted small mt-1">Ending Balance: <span id="modal-end-bal" class="fw-bold text-dark font-monospace">-</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initTrialBalanceTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initTrialBalanceTable, 50);
                return;
            }

            if (window.initReportDateRangePicker) {
                window.initReportDateRangePicker('#filter-created-date');
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

            const formatMoney = (val) => {
                const num = parseFloat(val) || 0;
                return '$' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            const getClassBadge = (cls) => {
                const c = String(cls || '').toLowerCase();
                if (c === 'asset') return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">Asset</span>';
                if (c === 'liability') return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">Liability</span>';
                if (c === 'equity') return '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Equity</span>';
                if (c === 'revenue') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Revenue</span>';
                return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">Expense</span>';
            };

            const table = $('#trial-balance-table').DataTable({
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
                    url: '{{ route('reports.financial.trial-balance.index') }}',
                    data: function (d) {
                        d.classification = $('#filter-classification').val() || 'all';
                        d.statement_type = $('#filter-statement').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Trial Balance DataTable AJAX Error:', error, thrown, xhr.responseText);
                    }
                },
                order: [[2, 'asc']],
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
                            return start + ((meta && typeof meta.row === 'number') ? meta.row : 0) + 1;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        title: 'Action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center text-nowrap',
                        width: '60px'
                    },
                    {
                        data: 'account_code',
                        name: 'account_code',
                        render: function (code) {
                            return `<span class="fw-bold font-monospace text-dark">${code}</span>`;
                        }
                    },
                    {
                        data: 'account_name',
                        name: 'account_name',
                        render: function (name) {
                            return `<span class="fw-semibold text-dark">${name}</span>`;
                        }
                    },
                    {
                        data: 'classification',
                        name: 'classification',
                        render: function (cls) {
                            return getClassBadge(cls);
                        }
                    },
                    {
                        data: 'statement_type',
                        name: 'statement_type',
                        render: function (st) {
                            return `<span class="badge bg-light text-secondary border">${st}</span>`;
                        }
                    },
                    {
                        data: 'beginning_balance',
                        name: 'beginning_balance',
                        className: 'text-end',
                        render: function (b) {
                            return `<span class="text-secondary font-monospace">${formatMoney(b)}</span>`;
                        }
                    },
                    {
                        data: 'debit_amount',
                        name: 'debit_amount',
                        className: 'text-end',
                        render: function (dr) {
                            return `<span class="fw-semibold text-primary font-monospace">${formatMoney(dr)}</span>`;
                        }
                    },
                    {
                        data: 'credit_amount',
                        name: 'credit_amount',
                        className: 'text-end',
                        render: function (cr) {
                            return `<span class="fw-semibold text-danger font-monospace">${formatMoney(cr)}</span>`;
                        }
                    },
                    {
                        data: 'ending_balance',
                        name: 'ending_balance',
                        className: 'text-end',
                        render: function (end) {
                            return `<span class="fw-bold text-dark font-monospace">${formatMoney(end)}</span>`;
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

            $('#filter-classification, #filter-statement, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-classification').val('all').trigger('change');
                $('#filter-statement').val('all').trigger('change');
                $('#filter-date-preset').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            $(document).on('click', '.btn-view-account, .btn-view-account *', function (e) {
                e.preventDefault();
                const btn = $(this).closest('.btn-view-account');
                const id = btn.data('id') || btn.attr('data-id');
                if (!id) return;

                function renderAccountModal(d) {
                    $('#modal-acc-code').text(d.account_code);
                    $('#modal-acc-name').text(d.account_name);
                    $('#modal-acc-class').text(d.classification);
                    $('#modal-acc-stmt').text(d.statement_type);
                    $('#modal-doc-date').text(formatDateText(d.doc_date));
                    $('#modal-open-bal').text(formatMoney(d.beginning_balance));
                    $('#modal-debit-val').text(formatMoney(d.debit_amount));
                    $('#modal-credit-val').text(formatMoney(d.credit_amount));
                    $('#modal-end-bal').text(formatMoney(d.ending_balance));

                    const modalEl = document.getElementById('modal-tb-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && (rowData.account_code || rowData.id)) {
                    renderAccountModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `/reports/financial/trial-balance/${id}`,
                    type: 'GET',
                    success: function (res) {
                        if (res && res.data) {
                            renderAccountModal(res.data);
                        }
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTrialBalanceTable);
        } else {
            initTrialBalanceTable();
        }
    </script>
</x-app-layout>
