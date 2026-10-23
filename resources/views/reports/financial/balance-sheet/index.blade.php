<x-app-layout title="Balance Sheet Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Financial</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Balance Sheet</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Balance Sheet Report"
        section="Financial Reports"
        icon="scale"
        description="Comprehensive statement of financial position tracking assets, liabilities, and shareholder equity.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#bs-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#bs-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="bs-definition-card"
        title="Balance Sheet Report"
        subtitle="Statement of Financial Position · Cutoff Date Valuation"
        icon="scale"
        standard="IFRS (IAS 1) & US GAAP (ASC 210)"
        definition="The Balance Sheet provides a formal accounting snapshot of an organization's financial standing at a specific point in time. It details all economic resources owned (Assets), debts and obligations owed to external parties (Liabilities), and residual owner value (Shareholders' Equity)."
        purpose="Evaluates enterprise liquidity, debt solvency, and asset capitalization. Financial leaders, investors, and lenders analyze this statement to assess creditworthiness, working capital health, and capital structure stability."
        equation="Total Assets = Total Liabilities + Total Shareholders' Equity"
        :indicators="['Working Capital', 'Current Ratio', 'Debt-to-Equity', 'Solvency']"
    />

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-blue rounded-3 flex-shrink-0">
                        <i data-lucide="wallet" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Assets</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalAssets, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-rose rounded-3 flex-shrink-0">
                        <i data-lucide="receipt" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Liabilities</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalLiabilities, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-green rounded-3 flex-shrink-0">
                        <i data-lucide="building-2" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Shareholders' Equity</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalEquity, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-purple rounded-3 flex-shrink-0">
                        <i data-lucide="activity" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Working Capital</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($workingCapital, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div id="bs-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label for="filter-category" class="form-label small fw-semibold text-secondary mb-1">Filter by Category</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="layers"></i>
                        <select id="filter-category" class="form-select js-select2">
                            <option value="all">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <x-reports.date-filter id="filter-created-date" label="Date Range" placeholder="YYYY-MM-DD to YYYY-MM-DD" />
                </div>
                <div class="col-md-4 col-12 d-flex align-items-end justify-content-md-end">
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
                <table id="balance-sheet-table" data-export-module="reports.financial.balance-sheet" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Category</th>
                            <th>As of Date</th>
                            <th class="text-end">Current Balance</th>
                            <th class="text-end">Prior Period</th>
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

    <!-- Balance Sheet Details Modal -->
    <div class="modal fade" id="modal-bs-details" tabindex="-1" aria-labelledby="modal-bs-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-bs-details-title">
                        <i data-lucide="scale" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Account Statement: <span id="modal-acc-code" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Account Overview</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-acc-name">-</div>
                                <div class="text-muted small">Category: <span id="modal-acc-category" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Status: <span id="modal-acc-status" class="badge bg-success bg-opacity-10 text-success">Active</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Financial Balances</h6>
                                <div class="text-muted small">As of Date: <span id="modal-as-of-date" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Current Balance: <span id="modal-current-balance" class="fw-bold text-dark">-</span></div>
                                <div class="text-muted small mt-1">Prior Period Balance: <span id="modal-prior-balance" class="fw-medium text-dark">-</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border rounded-3 bg-white">
                        <h6 class="fw-semibold text-dark small text-uppercase mb-1">Account Notes & Audit Disclosures</h6>
                        <p class="text-muted small mb-0" id="modal-notes">-</p>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initBalanceSheetTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initBalanceSheetTable, 50);
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

            const getCategoryBadge = (cat) => {
                const c = String(cat || '').toLowerCase();
                if (c.includes('asset')) return `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${cat}</span>`;
                if (c.includes('liabilit')) return `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">${cat}</span>`;
                return `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">${cat}</span>`;
            };

            const table = $('#balance-sheet-table').DataTable({
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
                    url: '{{ route('reports.financial.balance-sheet.index') }}',
                    data: function (d) {
                        d.category = $('#filter-category').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Balance Sheet DataTable AJAX Error:', error, thrown, xhr.responseText);
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
                        data: 'category',
                        name: 'category',
                        render: function (cat) {
                            return getCategoryBadge(cat);
                        }
                    },
                    {
                        data: 'as_of_date',
                        name: 'as_of_date',
                        render: function (d) {
                            return formatDate(d);
                        }
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        className: 'text-end',
                        render: function (amt) {
                            return `<span class="fw-bold text-dark font-monospace">${formatMoney(amt)}</span>`;
                        }
                    },
                    {
                        data: 'previous_amount',
                        name: 'previous_amount',
                        className: 'text-end',
                        render: function (prev) {
                            return `<span class="text-secondary font-monospace">${formatMoney(prev)}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function (st) {
                            return `<span class="badge bg-success bg-opacity-10 text-success">${st || 'Active'}</span>`;
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

            $('#filter-category, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-category').val('all').trigger('change');
                $('#filter-date-preset').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            $(document).on('click', '.btn-view-item', function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/reports/financial/balance-sheet/${id}`,
                    type: 'GET',
                    success: function (res) {
                        if (res && res.data) {
                            const d = res.data;
                            $('#modal-acc-code').text(d.account_code);
                            $('#modal-acc-name').text(d.account_name);
                            $('#modal-acc-category').text(d.category);
                            $('#modal-acc-status').text(d.status || 'Active');
                            $('#modal-as-of-date').text(formatDateText(d.as_of_date));
                            $('#modal-current-balance').text(formatMoney(d.amount));
                            $('#modal-prior-balance').text(formatMoney(d.previous_amount));
                            $('#modal-notes').text(d.notes || 'No audit notes recorded for this line item.');

                            const modalEl = document.getElementById('modal-bs-details');
                            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modal.show();
                            if (window.lucide) window.lucide.createIcons();
                        }
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBalanceSheetTable);
        } else {
            initBalanceSheetTable();
        }
    </script>
</x-app-layout>
