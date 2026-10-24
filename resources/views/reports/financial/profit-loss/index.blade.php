<x-app-layout title="Profit & Loss Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Financial</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Profit & Loss</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Profit & Loss Statement"
        section="Financial Reports"
        icon="chart-line"
        description="Monitor operating revenues, cost of goods sold, operating expenses, and net profit margins.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#pl-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#pl-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="pl-definition-card"
        title="Profit & Loss Statement"
        subtitle="Income Statement · Periodic Operating Results"
        icon="trending-up"
        standard="IFRS (IAS 1) & US GAAP (ASC 220)"
        definition="The Profit & Loss statement summarizes corporate revenues earned and expenses incurred across an operating period. It illustrates how revenue translates into gross profit, operating income, and net bottom-line earnings after deducting costs of goods sold, overhead, and taxes."
        purpose="Measures profitability, operational cost efficiency, and revenue margins. Executive management and department heads use this report to optimize pricing strategies, monitor departmental budgets, and ensure operational sustainability."
        equation="Net Profit = Total Revenues - (COGS + Operating Expenses + Financing & Taxes)"
        :indicators="['Gross Margin', 'EBITDA', 'Operating Margin', 'Net Profit Margin']"
    />

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-green rounded-3 flex-shrink-0">
                        <i data-lucide="trending-up" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Revenue</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-blue rounded-3 flex-shrink-0">
                        <i data-lucide="pie-chart" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Gross Profit</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($grossProfit, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-yellow rounded-3 flex-shrink-0">
                        <i data-lucide="credit-card" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Operating Expenses</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalOpex, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-purple rounded-3 flex-shrink-0">
                        <i data-lucide="dollar-sign" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Net Profit</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($netProfit, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div id="pl-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
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
                <div class="col-md-3 col-sm-6">
                    <label for="filter-department" class="form-label small fw-semibold text-secondary mb-1">Filter by Department</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="building"></i>
                        <select id="filter-department" class="form-select js-select2">
                            <option value="all">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
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
                <table id="profit-loss-table" data-export-module="reports.financial.profit-loss" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Category</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th class="text-end">Actual Amount</th>
                            <th class="text-end">Budget Amount</th>
                            <th class="text-center">Variance vs Prior</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Handled by Server-Side DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- P&L Item Details Modal -->
    <div class="modal fade" id="modal-pl-details" tabindex="-1" aria-labelledby="modal-pl-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-pl-details-title">
                        <i data-lucide="chart-line" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Profit & Loss Item: <span id="modal-acc-code" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Item Classification</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-acc-name">-</div>
                                <div class="text-muted small">Category: <span id="modal-acc-category" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Department: <span id="modal-acc-dept" class="fw-medium text-dark">-</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Financial Execution</h6>
                                <div class="text-muted small">Report Period: <span id="modal-doc-date" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Actual Amount: <span id="modal-actual-amount" class="fw-bold text-dark">-</span></div>
                                <div class="text-muted small mt-1">Budget Allocation: <span id="modal-budget-amount" class="fw-medium text-dark">-</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border rounded-3 bg-white">
                        <h6 class="fw-semibold text-dark small text-uppercase mb-1">Variance Commentary & Notes</h6>
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
        function initProfitLossTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initProfitLossTable, 50);
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
                if (c.includes('revenue')) return `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">${cat}</span>`;
                if (c.includes('cost of goods')) return `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">${cat}</span>`;
                if (c.includes('operating expense')) return `<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">${cat}</span>`;
                return `<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">${cat}</span>`;
            };

            const table = $('#profit-loss-table').DataTable({
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
                    url: '{{ route('reports.financial.profit-loss.index') }}',
                    data: function (d) {
                        d.category = $('#filter-category').val() || 'all';
                        d.department = $('#filter-department').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Profit & Loss DataTable AJAX Error:', error, thrown, xhr.responseText);
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
                        data: 'department',
                        name: 'department',
                        render: function (dept) {
                            return `<span class="badge bg-light text-secondary border">${dept || 'Finance'}</span>`;
                        }
                    },
                    {
                        data: 'doc_date',
                        name: 'doc_date',
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
                        data: 'budget_amount',
                        name: 'budget_amount',
                        className: 'text-end',
                        render: function (b) {
                            return `<span class="text-secondary font-monospace">${formatMoney(b)}</span>`;
                        }
                    },
                    {
                        data: 'variance',
                        name: 'variance',
                        className: 'text-center',
                        render: function (v) {
                            const isPositive = String(v).startsWith('+');
                            const badgeClass = isPositive ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                            return `<span class="badge ${badgeClass} font-monospace">${v}</span>`;
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

            $('#filter-category, #filter-department, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-category').val('all').trigger('change');
                $('#filter-department').val('all').trigger('change');
                $('#filter-date-preset').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            $(document).on('click', '.btn-view-item, .btn-view-item *', function (e) {
                e.preventDefault();
                const btn = $(this).closest('.btn-view-item');
                const id = btn.data('id') || btn.attr('data-id');
                if (!id) return;

                function renderItemModal(d) {
                    $('#modal-acc-code').text(d.account_code);
                    $('#modal-acc-name').text(d.account_name);
                    $('#modal-acc-category').text(d.category);
                    $('#modal-acc-dept').text(d.department || 'Finance');
                    $('#modal-doc-date').text(formatDateText(d.doc_date));
                    $('#modal-actual-amount').text(formatMoney(d.amount));
                    $('#modal-budget-amount').text(formatMoney(d.budget_amount));
                    $('#modal-notes').text(d.notes || 'Routine profit and loss transaction.');

                    const modalEl = document.getElementById('modal-pl-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && (rowData.account_code || rowData.id)) {
                    renderItemModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `/reports/financial/profit-loss/${id}`,
                    type: 'GET',
                    success: function (res) {
                        if (res && res.data) {
                            renderItemModal(res.data);
                        }
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initProfitLossTable);
        } else {
            initProfitLossTable();
        }
    </script>
</x-app-layout>
