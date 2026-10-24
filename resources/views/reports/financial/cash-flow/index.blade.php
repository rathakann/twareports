<x-app-layout title="Cash Flow Statement · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Financial</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Cash Flow Statement</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Cash Flow Statement"
        section="Financial Reports"
        icon="activity"
        description="Detailed monitoring of operating, investing, and financing cash inflows, outflows, and net liquidity.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#cf-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#cf-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="cf-definition-card"
        title="Cash Flow Statement"
        subtitle="Statement of Cash Flows · Liquidity & Capital Tracking"
        icon="banknote"
        standard="IFRS (IAS 7) & US GAAP (ASC 230)"
        definition="The Cash Flow Statement tracks all physical cash inflows and outflows across a fiscal period, reconciling net income with actual liquid reserves. It eliminates non-cash accrual adjustments to reveal whether the business generates authentic cash surplus."
        purpose="Evaluates corporate solvency, operational liquidity, and capital self-sufficiency. CFOs, treasury teams, and credit analysts examine this statement to ensure the business can fund daily operations, service bank debt, and finance capital expansion without unexpected cash shortfalls."
        equation="Net Cash Change = Operating Cash Flow + Investing Cash Flow + Financing Cash Flow"
        :indicators="['Operating Cash Flow', 'Free Cash Flow (FCF)', 'Cash to Debt Ratio', 'Liquidity Cushion']"
    />

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-blue rounded-3 flex-shrink-0">
                        <i data-lucide="arrow-down-left" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Operating Cash Flow</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($netOperating, 2) }}</div>
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
                        <div class="text-secondary small fw-medium">Investing Cash Flow</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($netInvesting, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-yellow rounded-3 flex-shrink-0">
                        <i data-lucide="landmark" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Financing Cash Flow</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($netFinancing, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-green rounded-3 flex-shrink-0">
                        <i data-lucide="wallet" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Net Change in Cash</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($netCashChange, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div id="cf-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-activity" class="form-label small fw-semibold text-secondary mb-1">Activity Classification</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="activity"></i>
                        <select id="filter-activity" class="form-select js-select2">
                            <option value="all">All Activities</option>
                            @foreach ($activityTypes as $act)
                                <option value="{{ $act }}">{{ $act }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-flow-type" class="form-label small fw-semibold text-secondary mb-1">Flow Direction</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="arrow-left-right"></i>
                        <select id="filter-flow-type" class="form-select js-select2">
                            <option value="all">All Inflow & Outflow</option>
                            <option value="Inflow">Inflow (Cash In)</option>
                            <option value="Outflow">Outflow (Cash Out)</option>
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
                <table id="cash-flow-table" data-export-module="reports.financial.cash-flow" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Reference #</th>
                            <th>Date</th>
                            <th>Activity Category</th>
                            <th>Description</th>
                            <th>Payment Method</th>
                            <th class="text-end">Cash Inflow</th>
                            <th class="text-end">Cash Outflow</th>
                            <th class="text-end">Net Movement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Handled by Server-Side DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cash Flow Entry Details Modal -->
    <div class="modal fade" id="modal-cf-details" tabindex="-1" aria-labelledby="modal-cf-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-cf-details-title">
                        <i data-lucide="activity" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Cash Flow Entry: <span id="modal-ref-num" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Activity Profile</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-desc">-</div>
                                <div class="text-muted small">Category: <span id="modal-act-type" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Payment Method: <span id="modal-method" class="fw-medium text-dark">-</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Financial Flow</h6>
                                <div class="text-muted small">Transaction Date: <span id="modal-doc-date" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Inflow: <span id="modal-inflow" class="fw-bold text-success font-monospace">-</span></div>
                                <div class="text-muted small mt-1">Outflow: <span id="modal-outflow" class="fw-bold text-danger font-monospace">-</span></div>
                                <div class="text-muted small mt-1">Net Balance: <span id="modal-net" class="fw-bold text-dark font-monospace">-</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border rounded-3 bg-white">
                        <h6 class="fw-semibold text-dark small text-uppercase mb-1">Target Account</h6>
                        <p class="text-muted small mb-0" id="modal-target-acc">-</p>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initCashFlowTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initCashFlowTable, 50);
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

            const getActivityBadge = (act) => {
                const a = String(act || '').toLowerCase();
                if (a.includes('operating')) return `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">${act}</span>`;
                if (a.includes('investing')) return `<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">${act}</span>`;
                return `<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">${act}</span>`;
            };

            const table = $('#cash-flow-table').DataTable({
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
                    url: '{{ route('reports.financial.cash-flow.index') }}',
                    data: function (d) {
                        d.activity_type = $('#filter-activity').val() || 'all';
                        d.flow_type = $('#filter-flow-type').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    },
                    error: function (xhr, error, thrown) {
                        console.error('Cash Flow DataTable AJAX Error:', error, thrown, xhr.responseText);
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
                        data: 'reference_num',
                        name: 'reference_num',
                        render: function (ref) {
                            return `<span class="fw-bold font-monospace text-dark">${ref}</span>`;
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
                        data: 'activity_type',
                        name: 'activity_type',
                        render: function (act) {
                            return getActivityBadge(act);
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (desc) {
                            return `<span class="fw-medium text-dark">${desc}</span>`;
                        }
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        render: function (m) {
                            return `<span class="badge bg-light text-secondary border">${m || 'Bank Transfer'}</span>`;
                        }
                    },
                    {
                        data: 'inflow_amount',
                        name: 'inflow_amount',
                        className: 'text-end',
                        render: function (amt) {
                            const val = parseFloat(amt) || 0;
                            return val > 0 ? `<span class="text-success font-monospace fw-semibold">+${formatMoney(val)}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'outflow_amount',
                        name: 'outflow_amount',
                        className: 'text-end',
                        render: function (amt) {
                            const val = parseFloat(amt) || 0;
                            return val > 0 ? `<span class="text-danger font-monospace fw-semibold">-${formatMoney(val)}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount',
                        className: 'text-end',
                        render: function (net) {
                            const val = parseFloat(net) || 0;
                            const cls = val >= 0 ? 'text-success' : 'text-danger';
                            const sign = val >= 0 ? '+' : '';
                            return `<span class="fw-bold font-monospace ${cls}">${sign}${formatMoney(val)}</span>`;
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

            $('#filter-activity, #filter-flow-type, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-activity').val('all').trigger('change');
                $('#filter-flow-type').val('all').trigger('change');
                $('#filter-date-preset').val('all').trigger('change');
                const dateEl = document.querySelector('#filter-created-date');
                if (dateEl && dateEl._flatpickr) {
                    dateEl._flatpickr.clear();
                } else {
                    $('#filter-created-date').val('');
                }
                table.draw();
            });

            $(document).on('click', '.btn-view-entry, .btn-view-entry *', function (e) {
                e.preventDefault();
                const btn = $(this).closest('.btn-view-entry');
                const id = btn.data('id') || btn.attr('data-id');
                if (!id) return;

                function renderEntryModal(d) {
                    $('#modal-ref-num').text(d.reference_num);
                    $('#modal-desc').text(d.description);
                    $('#modal-act-type').text(d.activity_type);
                    $('#modal-method').text(d.payment_method);
                    $('#modal-doc-date').text(formatDateText(d.doc_date));
                    $('#modal-inflow').text(formatMoney(d.inflow_amount));
                    $('#modal-outflow').text(formatMoney(d.outflow_amount));
                    $('#modal-net').text(formatMoney(d.net_amount));
                    $('#modal-target-acc').text(d.account_name);

                    const modalEl = document.getElementById('modal-cf-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && (rowData.reference_num || rowData.id)) {
                    renderEntryModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `/reports/financial/cash-flow/${id}`,
                    type: 'GET',
                    success: function (res) {
                        if (res && res.data) {
                            renderEntryModal(res.data);
                        }
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                    }
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCashFlowTable);
        } else {
            initCashFlowTable();
        }
    </script>
</x-app-layout>
