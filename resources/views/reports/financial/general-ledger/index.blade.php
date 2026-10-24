<x-app-layout title="General Ledger Report · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Report</span>
        <span>/</span>
        <span class="text-secondary">Financial</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">General Ledger</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="General Ledger Report"
        section="Financial Reports"
        icon="book-open"
        description="Chronological audit record of all posted journal debit and credit entries with running balances.">
        <x-slot:actions>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#gl-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-definition-toggle" data-target="#gl-definition-card" title="Toggle Report Definition" aria-expanded="false">
                <i data-lucide="book-open" style="width: 16px; height: 16px;"></i>
                <span>Definition</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Report Definition & Accounting Purpose -->
    <x-reports.report-purpose
        card-id="gl-definition-card"
        title="General Ledger Report"
        subtitle="Master Transaction Audit Trail · Statutory Record"
        icon="database"
        standard="Statutory Audit Trail (ISA 500 & IFRS Standard)"
        definition="The General Ledger is the master accounting record containing all posted debit and credit transactions across every financial account. It stores transaction-level granularity including dates, entry reference numbers, source journals, and poster identities."
        purpose="Provides an immutable, chronological audit trail linking high-level summary statements back to source documents (AR invoices, AP bills, payment vouchers, bank reconciliations). Auditors, controllers, and tax inspectors rely on this report for compliance validation and forensic line-item verification."
        equation="Account Running Balance = Opening Balance + Total Debits - Total Credits"
        :indicators="['Audit Traceability', 'Source Document Matching', 'Running Balances', 'Poster Accountability']"
    />

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-blue rounded-3 flex-shrink-0">
                        <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Entries</div>
                        <div class="fs-5 fw-bold text-dark">{{ number_format($totalEntries) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-green rounded-3 flex-shrink-0">
                        <i data-lucide="plus-circle" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Debits Posted</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalDebit, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-rose rounded-3 flex-shrink-0">
                        <i data-lucide="minus-circle" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Total Credits Posted</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($totalCredit, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="kpi-icon kpi-icon-purple rounded-3 flex-shrink-0">
                        <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-medium">Net Posting Balance</div>
                        <div class="fs-5 fw-bold text-dark">${{ number_format($netMovement, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div id="gl-filter-card" class="card border-0 shadow-sm rounded-3 mb-2 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-account-type" class="form-label small fw-semibold text-secondary mb-1">Account Type</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="tag"></i>
                        <select id="filter-account-type" class="form-select js-select2">
                            <option value="all">All Types</option>
                            @foreach ($accountTypes as $at)
                                <option value="{{ $at }}">{{ $at }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="filter-doc-type" class="form-label small fw-semibold text-secondary mb-1">Source Document</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="file-check"></i>
                        <select id="filter-doc-type" class="form-select js-select2">
                            <option value="all">All Document Types</option>
                            @foreach ($sourceDocTypes as $sdt)
                                <option value="{{ $sdt }}">{{ $sdt }}</option>
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
                <table id="general-ledger-table" data-export-module="reports.financial.general-ledger" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 60px;">Action</th>
                            <th>Entry #</th>
                            <th>Date</th>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Source Doc</th>
                            <th>Description</th>
                            <th class="text-end text-primary">Debit</th>
                            <th class="text-end text-danger">Credit</th>
                            <th class="text-end">Running Bal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Handled by Server-Side DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- General Ledger Details Modal -->
    <div class="modal fade" id="modal-gl-details" tabindex="-1" aria-labelledby="modal-gl-details-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="modal-gl-details-title">
                        <i data-lucide="book-open" class="text-primary" style="width: 20px; height: 20px;"></i>
                        <span>Journal Entry Details: <span id="modal-entry-num" class="fw-bold text-dark"></span></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Posting Attribution</h6>
                                <div class="fs-6 fw-bold text-dark" id="modal-acc-name">-</div>
                                <div class="text-muted small">Account Code: <span id="modal-acc-code" class="fw-medium text-dark font-monospace">-</span></div>
                                <div class="text-muted small mt-1">Source Doc: <span id="modal-source-doc" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Posted By: <span id="modal-posted-by" class="fw-medium text-dark">-</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-semibold text-secondary small text-uppercase mb-2">Ledger Movement</h6>
                                <div class="text-muted small">Posting Date: <span id="modal-doc-date" class="fw-medium text-dark">-</span></div>
                                <div class="text-muted small mt-1">Debit Amount: <span id="modal-debit-val" class="fw-bold text-primary font-monospace">-</span></div>
                                <div class="text-muted small mt-1">Credit Amount: <span id="modal-credit-val" class="fw-bold text-danger font-monospace">-</span></div>
                                <div class="text-muted small mt-1">Cumulative Balance: <span id="modal-balance-val" class="fw-bold text-dark font-monospace">-</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border rounded-3 bg-white">
                        <h6 class="fw-semibold text-dark small text-uppercase mb-1">Journal Entry Description</h6>
                        <p class="text-muted small mb-0" id="modal-desc">-</p>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initGeneralLedgerTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initGeneralLedgerTable, 50);
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

            const table = $('#general-ledger-table').DataTable({
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
                    url: '{{ route('reports.financial.general-ledger.index') }}',
                    data: function (d) {
                        d.account_type = $('#filter-account-type').val() || 'all';
                        d.source_doc_type = $('#filter-doc-type').val() || 'all';
                        d.created_date = $('#filter-created-date').val() || '';
                    },
                    error: function (xhr, error, thrown) {
                        console.error('General Ledger DataTable AJAX Error:', error, thrown, xhr.responseText);
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
                        data: 'entry_number',
                        name: 'entry_number',
                        render: function (num) {
                            return `<span class="fw-bold font-monospace text-dark">${num}</span>`;
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
                        data: 'account_code',
                        name: 'account_code',
                        render: function (code) {
                            return `<span class="fw-semibold font-monospace text-secondary">${code}</span>`;
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
                        data: 'source_doc_type',
                        name: 'source_doc_type',
                        render: function (type, t, row) {
                            const docNum = row.source_doc_num ? `<br><small class="text-muted font-monospace">${row.source_doc_num}</small>` : '';
                            return `<span class="badge bg-light text-dark border">${type}</span>${docNum}`;
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function (desc) {
                            return `<span class="text-muted small">${desc}</span>`;
                        }
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        className: 'text-end',
                        render: function (dr) {
                            const val = parseFloat(dr) || 0;
                            return val > 0 ? `<span class="fw-semibold text-primary font-monospace">${formatMoney(val)}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                        className: 'text-end',
                        render: function (cr) {
                            const val = parseFloat(cr) || 0;
                            return val > 0 ? `<span class="fw-semibold text-danger font-monospace">${formatMoney(val)}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        className: 'text-end',
                        render: function (bal) {
                            return `<span class="fw-bold text-dark font-monospace">${formatMoney(bal)}</span>`;
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

            $('#filter-account-type, #filter-doc-type, #filter-created-date').on('change', function () {
                table.draw();
            });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-account-type').val('all').trigger('change');
                $('#filter-doc-type').val('all').trigger('change');
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
                    $('#modal-entry-num').text(d.entry_number);
                    $('#modal-acc-name').text(d.account_name);
                    $('#modal-acc-code').text(d.account_code);
                    $('#modal-source-doc').text((d.source_doc_type || '') + (d.source_doc_num ? ' (' + d.source_doc_num + ')' : ''));
                    $('#modal-posted-by').text(d.posted_by || 'System');
                    $('#modal-doc-date').text(formatDateText(d.doc_date));
                    $('#modal-debit-val').text(formatMoney(d.debit));
                    $('#modal-credit-val').text(formatMoney(d.credit));
                    $('#modal-balance-val').text(formatMoney(d.balance));
                    $('#modal-desc').text(d.description || 'No detailed narrative provided.');

                    const modalEl = document.getElementById('modal-gl-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && (rowData.entry_number || rowData.id)) {
                    renderEntryModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `/reports/financial/general-ledger/${id}`,
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
            document.addEventListener('DOMContentLoaded', initGeneralLedgerTable);
        } else {
            initGeneralLedgerTable();
        }
    </script>
</x-app-layout>
