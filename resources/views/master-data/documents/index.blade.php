<x-app-layout title="Documents (Document Types) · Master Data · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Master Data</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Documents</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Documents (ONNM - Document Master)"
        section="Master Data"
        icon="files"
        description="SAP Document Master Objects (ONNM) defining standard commercial, banking, procurement, and inventory document types and their SAP database tables.">
        <x-slot:actions>
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 bg-light border rounded text-secondary small me-1">
                <i data-lucide="clock" style="width: 14px; height: 14px;"></i>
                <span>Last sync date:</span>
                <span class="fw-semibold text-dark font-monospace last-sync-time">{{ now()->format('d-M-Y H:i') }}</span>
            </div>
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm btn-filter-toggle" data-target="#doc-filter-card" title="Toggle Filters" aria-expanded="true">
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
    <div id="doc-filter-card" class="card border-0 shadow-sm rounded-3 mb-4 filter-section">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="filter-category" class="form-label small fw-semibold text-secondary mb-1">Functional Module</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="folder"></i>
                        <select id="filter-category" class="form-select">
                            <option value="all">All Modules</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
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
                <div class="col-md-6 col-12 d-flex align-items-end justify-content-md-end">
                    <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-2 shadow-sm" title="Reset all filters">
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
                <table id="doc-table" class="table table-striped table-hover align-middle w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 70px;">Action</th>
                            <th>Object Code</th>
                            <th>Document Name</th>
                            <th>SAP Master Table</th>
                            <th class="text-center">Module Category</th>
                            <th class="text-center">Has Series</th>
                            <th class="text-center">Series Count</th>
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
    <div class="modal fade" id="modal-doc-details" tabindex="-1" aria-labelledby="modalDocLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i data-lucide="files" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modal-doc-title">Document Details</h5>
                            <span class="small text-secondary" id="modal-doc-code-sub">Object Code</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">SAP Object Type Code</span>
                                <span class="fw-bold text-dark fs-5 font-monospace" id="modal-doc-code">-</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="bg-light p-3 rounded-3">
                                <span class="text-secondary small d-block">Underlying SAP Table</span>
                                <span class="fw-bold text-primary fs-5 font-monospace" id="modal-doc-table">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <span class="text-secondary small d-block">Document Full Title</span>
                        <p class="fw-bold text-dark fs-5 mb-0" id="modal-doc-name">-</p>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Module Group</small>
                                <span class="fw-bold fs-6 text-dark" id="modal-doc-category">-</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Numbering Series</small>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fs-6 mt-1" id="modal-doc-series">Enabled</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="bg-light p-3 rounded-3 text-center">
                                <small class="text-secondary d-block">Active Series</small>
                                <span class="fw-bold fs-6 text-primary" id="modal-doc-count">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3">
                        <span class="text-secondary small d-block">Status</span>
                        <div id="modal-doc-status" class="mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initDocTable() {
            if (typeof $ === 'undefined' || !$.fn || !$.fn.DataTable) {
                setTimeout(initDocTable, 50);
                return;
            }

            const getStatusBadge = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'active') return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Active</span>';
                return '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Inactive</span>';
            };

            const table = $('#doc-table').DataTable({
                pagingType: 'full_numbers',
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: { first: 'First', previous: 'Previous', next: 'Next', last: 'Last' }
                },
                dom: "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('master-data.documents.index') }}',
                    data: function (d) {
                        d.category = $('#filter-category').val() || 'all';
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
                        data: 'object_code',
                        name: 'object_code',
                        render: function(data) {
                            return `<span class="badge bg-primary bg-opacity-10 text-primary font-monospace fs-6">${data}</span>`;
                        }
                    },
                    {
                        data: 'doc_name',
                        name: 'doc_name',
                        render: function(data) { return `<span class="fw-semibold text-dark">${data}</span>`; }
                    },
                    {
                        data: 'sap_table_name',
                        name: 'sap_table_name',
                        render: function(d) {
                            return `<span class="badge bg-secondary bg-opacity-10 text-secondary font-monospace">${d}</span>`;
                        }
                    },
                    {
                        data: 'category',
                        name: 'category',
                        className: 'text-center',
                        render: function(d) {
                            return `<span class="badge bg-light text-dark border">${d}</span>`;
                        }
                    },
                    {
                        data: 'has_series',
                        name: 'has_series',
                        className: 'text-center',
                        render: function(d) {
                            return d
                                ? '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">Yes</span>'
                                : '<span class="badge bg-light text-muted border">No</span>';
                        }
                    },
                    {
                        data: 'series_count',
                        name: 'series_count',
                        className: 'text-center',
                        render: function(d) {
                            return `<span class="badge bg-secondary bg-opacity-10 text-secondary">${d} series</span>`;
                        }
                    },
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

            $('#filter-category, #filter-status').on('change', function () { table.draw(); });

            $('#btn-reset-filters').on('click', function () {
                $('#filter-category').val('all');
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

                function renderModal(doc) {
                    $('#modal-doc-title').text(doc.doc_name || 'Document Details');
                    $('#modal-doc-code-sub').text(`Object Code: ${doc.object_code || '-'}`);
                    $('#modal-doc-code').text(doc.object_code || '-');
                    $('#modal-doc-table').text(doc.sap_table_name || '-');
                    $('#modal-doc-name').text(doc.doc_name || '-');
                    $('#modal-doc-category').text(doc.category || '-');
                    $('#modal-doc-series').text(doc.has_series ? 'Enabled (NNM1)' : 'Disabled');
                    $('#modal-doc-count').text(`${doc.series_count || 1} Series configured`);
                    $('#modal-doc-status').html(getStatusBadge(doc.status));

                    const modalEl = document.getElementById('modal-doc-details');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                    if (window.lucide) window.lucide.createIcons();
                }

                const rowData = table.row(btn.closest('tr')).data();
                if (rowData && rowData.object_code) {
                    renderModal(rowData);
                    return;
                }

                btn.prop('disabled', true);
                $.ajax({
                    url: `{{ url('master-data/documents') }}/${uuid}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success && res.data) renderModal(res.data);
                    },
                    complete: function() { btn.prop('disabled', false); }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initDocTable);
    </script>
</x-app-layout>

