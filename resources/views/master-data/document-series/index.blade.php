<x-app-layout title="Documents & Document Series · Master Data · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Master Data</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Documents, Document Series</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Documents, Document Series (NNM1)"
        section="Master Data"
        icon="file-spreadsheet"
        description="SAP Document Numbering Series (NNM1) for automatic generation of continuous fiscal sequence numbers across branches.">
        <x-slot:actions>
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#ds-filter-card" title="Toggle Filters" aria-expanded="true">
                <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                <span>Filter</span>
            </button>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm" id="btn-sync-table" title="Sync from SAP">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                <span>Sync</span>
            </button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Filters Section -->
    <div id="ds-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <label for="filter-doctype" class="form-label small fw-semibold text-secondary mb-1">Document Type</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="files"></i>
                        <select id="filter-doctype" class="form-select">
                            <option value="all">All Document Types</option>
                            @foreach ($docTypes as $dt)
                                <option value="{{ $dt }}">{{ $dt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6">
                    <label for="filter-period" class="form-label small fw-semibold text-secondary mb-1">Fiscal Period</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="calendar"></i>
                        <select id="filter-period" class="form-select">
                            <option value="all">All Periods</option>
                            @foreach ($periods as $p)
                                <option value="{{ $p }}">{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-6">
                    <label for="filter-branch" class="form-label small fw-semibold text-secondary mb-1">Branch</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="building-2"></i>
                        <select id="filter-branch" class="form-select">
                            <option value="all">All Branches</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6">
                    <label for="filter-status" class="form-label small fw-semibold text-secondary mb-1">Status</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="activity"></i>
                        <select id="filter-status" class="form-select">
                            <option value="all">All Statuses</option>
                            @foreach ($statuses as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-2 col-md-12 col-12 d-flex align-items-end justify-content-xl-end">
                    <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary w-100 w-xl-auto d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" title="Reset all filters">
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
                <table id="ds-table" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                            <th>Document Type</th>
                            <th>Series Name</th>
                            <th>Series Code</th>
                            <th>Prefix</th>
                            <th class="text-center">Next Number</th>
                            <th>Numbering Range</th>
                            <th>Fiscal Period</th>
                            <th>Branch</th>
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

    <!-- Details Modal -->
    <div class="modal fade" id="modal-ds-details" tabindex="-1" aria-labelledby="modalDsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i data-lucide="file-spreadsheet" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modal-ds-title">Document Series Details</h5>
                            <span class="small text-secondary" id="modal-ds-type-sub">Series</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Target Document Type</span>
                                <span class="fw-bold text-dark fs-5" id="modal-ds-doctype">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Series Display Name</span>
                                <span class="fw-bold text-dark fs-5" id="modal-ds-name">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Series Code</small>
                                <span class="fw-bold fs-6 text-dark font-monospace" id="modal-ds-code">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Prefix Format</small>
                                <span class="fw-bold fs-6 text-primary font-monospace" id="modal-ds-prefix">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Next Document Number</small>
                                <span class="fw-bold fs-5 text-success font-monospace" id="modal-ds-next">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-header bg-transparent py-2">
                            <h6 class="card-title mb-0 small fw-semibold text-uppercase text-secondary">Sequence Boundaries & Branch Scope</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2 text-center">
                                <div class="col-sm-4 border-end">
                                    <small class="text-secondary d-block">Initial Sequence #</small>
                                    <span class="fw-semibold text-dark font-monospace" id="modal-ds-initial">-</span>
                                </div>
                                <div class="col-sm-4 border-end">
                                    <small class="text-secondary d-block">Last Sequence #</small>
                                    <span class="fw-semibold text-dark font-monospace" id="modal-ds-last">-</span>
                                </div>
                                <div class="col-sm-4">
                                    <small class="text-secondary d-block">Scope / Branch</small>
                                    <span class="fw-semibold text-dark" id="modal-ds-branch">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Fiscal Year Period Indicator</span>
                                <span class="fw-semibold text-dark" id="modal-ds-period">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Status</span>
                                <div id="modal-ds-status" class="mt-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initDsTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initDsTable, 50);
                return;
            }

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'active') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>';
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Inactive</span>';
            };

            const table = $('#ds-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master-data.document-series.index') }}',
                    data: function (d) {
                        d.doc_name = $('#filter-doctype').val() || 'all';
                        d.period_indicator = $('#filter-period').val() || 'all';
                        d.branch_code = $('#filter-branch').val() || 'all';
                        d.status = $('#filter-status').val() || 'all';
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center text-secondary fw-semibold',
                        width: '50px',
                        render: function (data, type, row, meta) {
                            const start = (meta && meta.settings && meta.settings._iDisplayStart) ? meta.settings._iDisplayStart : 0;
                            return start + meta.row + 1;
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '70px'
                    },
                    {
                        data: 'doc_name',
                        name: 'doc_name',
                        render: function(data, type, row) {
                            return `<span class="fw-semibold text-dark">${data}</span> <span class="badge bg-secondary bg-opacity-10 text-secondary font-monospace ms-1">${row.object_code || ''}</span>`;
                        }
                    },
                    { data: 'series_name', name: 'series_name' },
                    {
                        data: 'series_code',
                        name: 'series_code',
                        render: function(d) {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary font-monospace">${d}</span>`;
                        }
                    },
                    {
                        data: 'prefix',
                        name: 'prefix',
                        render: function(d) {
                            return d ? `<span class="font-monospace fw-bold text-secondary">${d}</span>` : '-';
                        }
                    },
                    {
                        data: 'next_num',
                        name: 'next_num',
                        className: 'text-center',
                        render: function(d, type, row) {
                            return `<span class="badge bg-success bg-opacity-10 text-success font-monospace fs-6">${row.prefix || ''}${d}</span>`;
                        }
                    },
                    {
                        data: 'initial_num',
                        name: 'initial_num',
                        render: function(d, type, row) {
                            return `<small class="text-secondary font-monospace">${d} – ${row.last_num}</small>`;
                        }
                    },
                    { data: 'period_indicator', name: 'period_indicator', render: function(d) { return d || '-'; } },
                    { data: 'branch_code', name: 'branch_code', render: function(d) { return d || 'All'; } },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center',
                        render: function(status) { return getStatusBadge(status); }
                    }
                ],
                drawCallback: function () {
                    if (window.lucide) {
                        window.lucide.createIcons({ icons: window.lucide.icons });
                    }
                }
            });

            window.setupDataTableExtensions?.(table.table().node(), table);

            $('#filter-doctype, #filter-period, #filter-branch, #filter-status').on('change', function () { table.draw(); });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-doctype').val('all');
                $('#filter-period').val('all');
                $('#filter-branch').val('all');
                $('#filter-status').val('all');
                table.draw();
            });

            $('#btn-sync-table, #btn-refresh-table').on('click', function() {
                const btn = $(this);
                const icon = btn.find('i, svg');
                btn.prop('disabled', true);
                icon.addClass('icon-spin');
                table.ajax.reload(function() {
                    btn.prop('disabled', false);
                    icon.removeClass('icon-spin');
                    const now = new Date();
                    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    const formatted = String(now.getDate()).padStart(2, '0') + '-' + 
                                      months[now.getMonth()] + '-' + 
                                      now.getFullYear() + ' ' + 
                                      String(now.getHours()).padStart(2, '0') + ':' + 
                                      String(now.getMinutes()).padStart(2, '0');
                    $('.last-sync-time').text(formatted);
                }, false);
            });

            $(document).on('click', '.btn-view-item', function() {
                const btn = $(this);
                const uuid = btn.data('id');

                function renderModal(ds) {
                    $('#modal-ds-title').text(ds.series_name || 'Document Series Details');
                    $('#modal-ds-type-sub').text(`Document Type: ${ds.doc_name || ds.doc_type || '-'}`);
                    $('#modal-ds-doctype').text(`${ds.doc_name || ds.doc_type || '-'} (${ds.object_code || ''})`);
                    $('#modal-ds-name').text(ds.series_name || '-');
                    $('#modal-ds-code').text(ds.series_code || '-');
                    $('#modal-ds-prefix').text(ds.prefix || 'None');
                    $('#modal-ds-next').text(`${ds.prefix || ''}${ds.next_num || 1}`);
                    $('#modal-ds-initial').text(ds.initial_num || 1);
                    $('#modal-ds-last').text(ds.last_num || 999999);
                    $('#modal-ds-branch').text(ds.branch_code || 'All Branches');
                    $('#modal-ds-period').text(ds.period_indicator || '2026');
                    $('#modal-ds-status').html(getStatusBadge(ds.status));

                    const modalEl = document.getElementById('modal-ds-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && rowData.series_code) {
                    renderModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('master-data/document-series') }}/${uuid}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) renderModal(res.data);
                    },
                    complete: function() { btn.prop('disabled', false); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initDsTable);
    </script>
</x-app-layout>

