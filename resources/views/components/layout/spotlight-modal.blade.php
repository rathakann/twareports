<div class="modal fade spotlight-modal" id="spotlightModal" tabindex="-1" aria-labelledby="spotlightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 650px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background-color: var(--bg-card); color: var(--tx1);">
            <div class="spotlight-header p-3 border-bottom d-flex align-items-center gap-2" style="background-color: var(--bg-inp);">
                <i data-lucide="search" class="text-secondary flex-shrink-0" style="width: 18px; height: 18px;"></i>
                <input type="text"
                       id="spotlightSearchInput"
                       class="form-control border-0 bg-transparent shadow-none p-0"
                       style="font-size: 15px; color: var(--tx1) !important;"
                       placeholder="Search reports, management pages, quick actions..."
                       autocomplete="off"
                       aria-label="Search reports and pages">
                <button type="button" class="btn btn-sm btn-link p-0 text-secondary text-decoration-none d-none" id="spotlightClearBtn" aria-label="Clear search">
                    <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                </button>
                <kbd class="spotlight-kbd px-2 py-1 small rounded border" style="background-color: var(--bg-card); color: var(--tx2); border-color: var(--bdr) !important; font-size: 11px;">ESC</kbd>
            </div>

            <div class="spotlight-body p-2" id="spotlightResults" style="max-height: 400px; overflow-y: auto;">
                @php
                    $searchableItems = [
                        // Workspace
                        [
                            'category' => 'Workspace',
                            'title' => 'Dashboard',
                            'description' => 'System metrics, operational activity and financial KPIs overview',
                            'route' => 'dashboard',
                            'icon' => 'layout-dashboard',
                        ],
                        // Purchase Reports
                        [
                            'category' => 'Purchase Reports',
                            'title' => 'Purchase Orders Report',
                            'description' => 'Procurement tracking, document totals and delivery schedules',
                            'route' => 'reports.purchase.purchase-orders.index',
                            'icon' => 'file-text',
                        ],
                        [
                            'category' => 'Purchase Reports',
                            'title' => 'AP Invoices Report',
                            'description' => 'Accounts payable invoices, vendor billing and payment terms',
                            'route' => 'reports.purchase.ap-invoices.index',
                            'icon' => 'receipt',
                        ],
                        [
                            'category' => 'Purchase Reports',
                            'title' => 'Outgoing Payments Report',
                            'description' => 'Disbursements, wire transfers and supplier settlements',
                            'route' => 'reports.purchase.outgoing-payments.index',
                            'icon' => 'credit-card',
                        ],
                        // Sales Reports
                        [
                            'category' => 'Sales Reports',
                            'title' => 'Sales Orders Report',
                            'description' => 'Customer orders, fulfillment status and booking amounts',
                            'route' => 'reports.sales.purchase-orders.index',
                            'icon' => 'file-text',
                        ],
                        [
                            'category' => 'Sales Reports',
                            'title' => 'AR Invoices Report',
                            'description' => 'Accounts receivable, customer invoices and aging totals',
                            'route' => 'reports.sales.ar-invoices.index',
                            'icon' => 'receipt',
                        ],
                        [
                            'category' => 'Sales Reports',
                            'title' => 'Incoming Payments Report',
                            'description' => 'Customer collections, receipts and cash allocations',
                            'route' => 'reports.sales.incoming-payments.index',
                            'icon' => 'wallet',
                        ],
                        [
                            'category' => 'Sales Reports',
                            'title' => 'Deposits Report',
                            'description' => 'Bank deposits, payment vouchers and account reconciliations',
                            'route' => 'reports.sales.deposits.index',
                            'icon' => 'piggy-bank',
                        ],
                        // Financial Reports
                        [
                            'category' => 'Financial Reports',
                            'title' => 'Balance Sheet',
                            'description' => 'Assets, liabilities and equity financial snapshot',
                            'route' => 'reports.financial.balance-sheet.index',
                            'icon' => 'scale',
                        ],
                        [
                            'category' => 'Financial Reports',
                            'title' => 'Profit & Loss Statement',
                            'description' => 'Revenues, operating costs and net profit calculation',
                            'route' => 'reports.financial.profit-loss.index',
                            'icon' => 'chart-line',
                        ],
                        [
                            'category' => 'Financial Reports',
                            'title' => 'Cash Flow Statement',
                            'description' => 'Operating, investing and financing liquidity analysis',
                            'route' => 'reports.financial.cash-flow.index',
                            'icon' => 'activity',
                        ],
                        [
                            'category' => 'Financial Reports',
                            'title' => 'Trial Balance Report',
                            'description' => 'General ledger debit and credit equilibrium audit',
                            'route' => 'reports.financial.trial-balance.index',
                            'icon' => 'table-2',
                        ],
                        [
                            'category' => 'Financial Reports',
                            'title' => 'General Ledger',
                            'description' => 'Detailed journal transactions and account running balances',
                            'route' => 'reports.financial.general-ledger.index',
                            'icon' => 'book-open',
                        ],
                        // Access Control
                        [
                            'category' => 'Access Control',
                            'title' => 'User Management',
                            'description' => 'Manage system operators, assign roles and control access status',
                            'route' => 'users.index',
                            'icon' => 'users',
                        ],
                        [
                            'category' => 'Access Control',
                            'title' => 'Role Management',
                            'description' => 'Configure security roles and permissions assignments',
                            'route' => 'roles.index',
                            'icon' => 'shield',
                        ],
                        [
                            'category' => 'Access Control',
                            'title' => 'Permissions Registry',
                            'description' => 'Audit granular permission codes and module access rights',
                            'route' => 'permissions.index',
                            'icon' => 'key',
                        ],
                        // Account & Profile
                        [
                            'category' => 'Account & Settings',
                            'title' => 'My Profile',
                            'description' => 'Update personal information, landing page and password',
                            'route' => 'profile.index',
                            'icon' => 'user',
                        ],
                        // Theme Showcase
                        [
                            'category' => 'Theme & Development',
                            'title' => 'UI Components Showcase',
                            'description' => 'Mantis-inspired design primitives, badges, inputs and cards',
                            'route' => 'theme.components',
                            'icon' => 'blocks',
                        ],
                        [
                            'category' => 'Theme & Development',
                            'title' => 'Example Listing Pattern',
                            'description' => 'DataTables standard layout, toolbar filters and action buttons',
                            'route' => 'theme.listing',
                            'icon' => 'table-properties',
                        ],
                    ];
                @endphp

                <div class="spotlight-items-list d-flex flex-column gap-1">
                    @foreach ($searchableItems as $item)
                        @if (\App\Support\Navigation::userCanAccess(['route' => $item['route']]))
                            <a href="{{ route($item['route']) }}"
                               class="spotlight-item d-flex align-items-center justify-content-between p-2.5 rounded-3 text-decoration-none"
                               data-title="{{ strtolower($item['title']) }}"
                               data-category="{{ strtolower($item['category']) }}"
                               data-description="{{ strtolower($item['description']) }}">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="spotlight-item-icon rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; background-color: var(--bg-hov); color: var(--pri);">
                                        <i data-lucide="{{ $item['icon'] }}" style="width: 17px; height: 17px;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-semibold text-truncate spotlight-item-title" style="font-size: 13.5px; color: var(--tx1);">{{ $item['title'] }}</div>
                                        <div class="text-secondary small text-truncate" style="font-size: 12px;">{{ $item['description'] }}</div>
                                    </div>
                                </div>
                                <span class="badge rounded-pill px-2 py-1 small flex-shrink-0 ms-2" style="background-color: var(--bg-hov); color: var(--tx2); font-size: 11px;">
                                    {{ $item['category'] }}
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>

                <div id="spotlightEmpty" class="text-center py-4 d-none">
                    <i data-lucide="search-x" class="text-secondary mb-2" style="width: 32px; height: 32px;"></i>
                    <p class="text-secondary small mb-0">No matching pages or reports found.</p>
                </div>
            </div>

            <div class="spotlight-footer p-2.5 px-3 border-top d-flex align-items-center justify-content-between small text-secondary" style="background-color: var(--bg-inp); border-color: var(--bdr) !important; font-size: 11.5px;">
                <div class="d-flex align-items-center gap-3">
                    <span><kbd class="spotlight-kbd px-1.5 py-0.5 rounded border" style="background-color: var(--bg-card); color: var(--tx2); border-color: var(--bdr) !important;">↑</kbd> <kbd class="spotlight-kbd px-1.5 py-0.5 rounded border" style="background-color: var(--bg-card); color: var(--tx2); border-color: var(--bdr) !important;">↓</kbd> Navigate</span>
                    <span><kbd class="spotlight-kbd px-1.5 py-0.5 rounded border" style="background-color: var(--bg-card); color: var(--tx2); border-color: var(--bdr) !important;">↵</kbd> Select</span>
                </div>
                <span>Quick Spotlight Search</span>
            </div>
        </div>
    </div>
</div>
