<x-app-layout title="UI Components · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Components</span>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">UI Components</span>
    </x-slot:breadcrumb>

    <x-management.page-header title="UI Components Showcase" section="Components" icon="blocks" description="Production-grade UI primitives inspired by Mantis Dashboard for enterprise reports and forms.">
        <x-slot:actions>
            <x-button variant="outline-primary" icon="external-link" onclick="window.open('https://mantisdashboard.com', '_blank')">Mantis Docs</x-button>
            <x-button icon="plus" data-bs-toggle="modal" data-bs-target="#componentModal">Open Modal Demo</x-button>
        </x-slot:actions>
    </x-management.page-header>

    <!-- Top Notice Alert -->
    <x-alert tone="primary" variant="accent-left" title="Mantis Design System Primitives" :dismissible="true">
        All controls below are built with standard Laravel Blade components, fully compatible with Bootstrap 5 and customized for dark mode, accessible focus states, and seamless theme token integration.
    </x-alert>

    <!-- Navigation Pills for Fast Jump -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 p-2 bg-card">
        <ul class="nav nav-pills gap-2 flex-wrap" id="component-nav-tabs">
            <li class="nav-item">
                <a class="nav-link active py-1.5 px-3 rounded-2 fw-medium" href="#sec-inputs">
                    <i data-lucide="check-square" class="me-1" style="width: 15px; height: 15px;"></i> Inputs & Selection
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1.5 px-3 rounded-2 fw-medium text-secondary" href="#sec-display">
                    <i data-lucide="user-check" class="me-1" style="width: 15px; height: 15px;"></i> Data Display & Avatars
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1.5 px-3 rounded-2 fw-medium text-secondary" href="#sec-feedback">
                    <i data-lucide="bell" class="me-1" style="width: 15px; height: 15px;"></i> Feedback & Alerts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1.5 px-3 rounded-2 fw-medium text-secondary" href="#sec-surfaces">
                    <i data-lucide="layers" class="me-1" style="width: 15px; height: 15px;"></i> Accordions & Surfaces
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1.5 px-3 rounded-2 fw-medium text-secondary" href="#sec-buttons">
                    <i data-lucide="mouse-pointer-click" class="me-1" style="width: 15px; height: 15px;"></i> Buttons & Badges
                </a>
            </li>
        </ul>
    </div>

    <!-- =========================================================================
         SECTION 1: Inputs & Selection Controls (Checkbox, Radio, Switch, etc.)
         ========================================================================= -->
    <div id="sec-inputs" class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Inputs & Selection Controls</h2>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">Mantis Form Controls</span>
        </div>

        <div class="row g-4">
            <!-- 1.1 Checkboxes Showcase -->
            <div class="col-lg-6">
                <x-content-card title="Professional Checkboxes" subtitle="Custom check controls with color tones, sizes & descriptions">
                    <!-- Standard & Tones -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Color Tones</label>
                        <div class="d-flex flex-wrap gap-3">
                            <x-form.checkbox name="cb_primary" :checked="true" tone="primary">Primary</x-form.checkbox>
                            <x-form.checkbox name="cb_success" :checked="true" tone="success">Success</x-form.checkbox>
                            <x-form.checkbox name="cb_danger" :checked="true" tone="danger">Danger</x-form.checkbox>
                            <x-form.checkbox name="cb_warning" :checked="true" tone="warning">Warning</x-form.checkbox>
                            <x-form.checkbox name="cb_info" :checked="true" tone="info">Info</x-form.checkbox>
                            <x-form.checkbox name="cb_purple" :checked="true" tone="purple">Purple</x-form.checkbox>
                        </div>
                    </div>

                    <!-- Sizes & States -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Sizes & Special States</label>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <x-form.checkbox name="cb_sm" size="sm" :checked="true">Small (sm)</x-form.checkbox>
                            <x-form.checkbox name="cb_md" size="md" :checked="true">Default (md)</x-form.checkbox>
                            <x-form.checkbox name="cb_lg" size="lg" :checked="true">Large (lg)</x-form.checkbox>
                            <x-form.checkbox name="cb_indet" :indeterminate="true">Indeterminate</x-form.checkbox>
                            <x-form.checkbox name="cb_dis" :disabled="true" :checked="true">Disabled</x-form.checkbox>
                        </div>
                    </div>

                    <!-- With Descriptions -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">With Helper Descriptions</label>
                        <div class="d-flex flex-column gap-2">
                            <x-form.checkbox name="cb_notif" :checked="true" label="Email Notifications" description="Receive a daily summary of posted GL balance changes and journal entries." />
                            <x-form.checkbox name="cb_audit" label="Audit Logging" description="Track all user record alterations with IP address and timestamp signatures." />
                        </div>
                    </div>

                    <!-- Mantis Card Checkboxes -->
                    <div>
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Selectable Card Checkboxes</label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <x-form.checkbox name="plan_standard" value="standard" :card="true" icon="shield-check" label="Standard Plan" description="Basic reporting and ledger exports." :checked="true" />
                            </div>
                            <div class="col-sm-6">
                                <x-form.checkbox name="plan_pro" value="pro" :card="true" icon="zap" label="Enterprise Pro" description="Unlimited data queries with live sync." />
                            </div>
                        </div>
                    </div>
                </x-content-card>
            </div>

            <!-- 1.2 Radios & Switches Showcase -->
            <div class="col-lg-6">
                <x-content-card title="Radio Buttons & Switches" subtitle="Mutually exclusive radios, toggles and card selectors">
                    <!-- Color Tones -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Radio Color Tones</label>
                        <div class="d-flex flex-wrap gap-3">
                            <x-form.radio name="radio_demo" value="1" tone="primary" :checked="true">Primary</x-form.radio>
                            <x-form.radio name="radio_demo" value="2" tone="success">Success</x-form.radio>
                            <x-form.radio name="radio_demo" value="3" tone="danger">Danger</x-form.radio>
                            <x-form.radio name="radio_demo" value="4" tone="warning">Warning</x-form.radio>
                            <x-form.radio name="radio_demo" value="5" tone="purple">Purple</x-form.radio>
                        </div>
                    </div>

                    <!-- Radio Sizes & Helper -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Radio Sizes</label>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <x-form.radio name="radio_size" value="sm" size="sm" :checked="true">Small</x-form.radio>
                            <x-form.radio name="radio_size" value="md" size="md">Default</x-form.radio>
                            <x-form.radio name="radio_size" value="lg" size="lg">Large</x-form.radio>
                            <x-form.radio name="radio_size" value="dis" :disabled="true">Disabled</x-form.radio>
                        </div>
                    </div>

                    <!-- Selectable Card Radios -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Selectable Card Radios</label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <x-form.radio name="billing_cycle" value="monthly" :card="true" icon="calendar" label="Monthly Billing" description="Billed on the 1st of each month." :checked="true" />
                            </div>
                            <div class="col-sm-6">
                                <x-form.radio name="billing_cycle" value="annual" :card="true" icon="award" label="Annual Billing" description="Save 20% on all enterprise modules." />
                            </div>
                        </div>
                    </div>

                    <!-- Switches -->
                    <div>
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Toggle Switches</label>
                        <div class="d-flex flex-column gap-2">
                            <x-form.switch-field name="sw_active" :checked="true">Enable real-time reporting sync</x-form.switch-field>
                            <x-form.switch-field name="sw_dark">Automatic system theme detection</x-form.switch-field>
                        </div>
                    </div>
                </x-content-card>
            </div>

            <!-- 1.3 Extended Inputs: Password, Slider, Rating -->
            <div class="col-12">
                <x-content-card title="Extended Inputs: Password Reveal, Slider, & Rating" subtitle="Modern interactive controls for passwords, continuum sliders and star scores">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <x-form.label for="password-demo" required>Password with Toggle</x-form.label>
                            <x-form.password-input name="password_demo" id="password-demo" placeholder="Enter secure password" value="Secret1234!" />
                            <small class="text-secondary mt-1 d-block">Click the eye icon to reveal or conceal the characters.</small>
                        </div>

                        <div class="col-md-4">
                            <x-form.label>Range Slider with Live Value</x-form.label>
                            <x-form.range-slider name="volume_slider" label="Export Batch Size" :min="10" :max="500" :step="10" :value="100" unit=" rows" />
                            <small class="text-secondary mt-1 d-block">Bound reactively with real-time value pill.</small>
                        </div>

                        <div class="col-md-4">
                            <x-form.label>Star Rating (Interactive & Readonly)</x-form.label>
                            <div class="d-flex flex-column gap-2 mt-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-secondary" style="width: 75px;">Interactive:</span>
                                    <x-form.rating name="demo_rating" :value="4" :max="5" />
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-secondary" style="width: 75px;">Readonly:</span>
                                    <x-form.rating name="static_rating" :value="5" :max="5" :readonly="true" />
                                </div>
                            </div>
                        </div>
                    </div>
                </x-content-card>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 2: Data Display & Avatars (Avatars, Groups, Badges, Chips, Progress)
         ========================================================================= -->
    <div id="sec-display" class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Data Display & Avatars</h2>
            <span class="badge bg-success-subtle text-success rounded-pill px-2.5">Mantis Data Display</span>
        </div>

        <div class="row g-4">
            <!-- 2.1 Avatars & Avatar Groups -->
            <div class="col-lg-6">
                <x-content-card title="Avatars & Overlapping Clusters" subtitle="Initials, icons, shapes, status indicators & clusters">
                    <!-- Sizes -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Avatar Sizes</label>
                        <div class="d-flex align-items-center gap-3">
                            <x-avatar initials="XS" size="xs" />
                            <x-avatar initials="SM" size="sm" />
                            <x-avatar initials="MD" size="md" />
                            <x-avatar initials="LG" size="lg" />
                            <x-avatar initials="XL" size="xl" />
                        </div>
                    </div>

                    <!-- Shapes & Status Dots -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Shapes & Online Status Dots</label>
                        <div class="d-flex align-items-center gap-3">
                            <x-avatar initials="ON" shape="circle" size="lg" status="online" />
                            <x-avatar initials="BS" shape="rounded" size="lg" status="busy" />
                            <x-avatar initials="AW" shape="square" size="lg" status="away" />
                            <x-avatar icon="user" shape="circle" size="lg" status="offline" />
                        </div>
                    </div>

                    <!-- Overlapping Avatar Group -->
                    <div>
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Stacked Avatar Group</label>
                        <x-avatar-group size="md" excess="8">
                            <x-avatar initials="AL" tone="primary" />
                            <x-avatar initials="BK" tone="success" />
                            <x-avatar initials="CR" tone="warning" />
                            <x-avatar initials="DT" tone="purple" />
                        </x-avatar-group>
                    </div>
                </x-content-card>
            </div>

            <!-- 2.2 Badges & Chips -->
            <div class="col-lg-6">
                <x-content-card title="Badges, Status Dots & Chips" subtitle="Mantis soft badges, pill tags and interactive chips">
                    <!-- Soft Badges -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Soft Context Badges</label>
                        <div class="d-flex flex-wrap gap-2">
                            <x-badge variant="soft" tone="primary">Primary</x-badge>
                            <x-badge variant="soft" tone="success" icon="check">Completed</x-badge>
                            <x-badge variant="soft" tone="danger" icon="alert-circle">Failed</x-badge>
                            <x-badge variant="soft" tone="warning" icon="clock">Pending</x-badge>
                            <x-badge variant="soft" tone="info">Informational</x-badge>
                            <x-badge variant="soft" tone="purple">Enterprise</x-badge>
                        </div>
                    </div>

                    <!-- Pill & Solid Badges -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Solid & Pill Badges</label>
                        <div class="d-flex flex-wrap gap-2">
                            <x-badge variant="solid" tone="primary" :pill="true">Active 12</x-badge>
                            <x-badge variant="solid" tone="success" :pill="true">Approved</x-badge>
                            <x-badge variant="solid" tone="danger" :pill="true">Rejected</x-badge>
                            <x-badge variant="outline" tone="primary">Outline Primary</x-badge>
                            <x-badge variant="dot" tone="success">Operational</x-badge>
                            <x-badge variant="dot" tone="danger">System Offline</x-badge>
                        </div>
                    </div>

                    <!-- Interactive Chips -->
                    <div>
                        <label class="form-label small fw-bold text-uppercase text-secondary d-block mb-2">Mantis Dismissible Chips</label>
                        <div class="d-flex flex-wrap gap-2">
                            <x-chip label="Financial Report" icon="file-text" :dismissible="true" />
                            <x-chip label="Sarah Connor" avatar="SC" :dismissible="true" />
                            <x-chip label="Clickable Filter" icon="filter" :clickable="true" tone="primary" />
                            <x-chip label="Fiscal 2026" :dismissible="true" />
                        </div>
                    </div>
                </x-content-card>
            </div>

            <!-- 2.3 Progress Bars -->
            <div class="col-12">
                <x-content-card title="Progress Bars" subtitle="Sleek track heights, labeled percentages, striped & animated variants">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-progress :value="75" tone="primary" size="md" label="Report Data Ingestion" :show-value="true" />
                            <x-progress :value="45" tone="success" size="sm" label="Monthly Goal Progress" :show-value="true" />
                        </div>
                        <div class="col-md-6">
                            <x-progress :value="90" tone="warning" size="lg" :striped="true" :animated="true" label="Server Cache Rebuild" :show-value="true" />
                            <x-progress :value="20" tone="danger" size="xs" label="Storage Utilization (Critical)" :show-value="true" />
                        </div>
                    </div>
                </x-content-card>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 3: Feedback, Alerts & Skeletons
         ========================================================================= -->
    <div id="sec-feedback" class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Feedback, Alerts & Shimmers</h2>
            <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5">Mantis Feedback</span>
        </div>

        <div class="row g-4">
            <!-- Alerts -->
            <div class="col-lg-6">
                <x-content-card title="Contextual Alerts" subtitle="Dismissible banners with titles, icons and action slots">
                    <div class="d-flex flex-column gap-3">
                        <x-alert tone="success" title="Payment Posted Successfully" icon="check-circle2">
                            The incoming payment transaction #IP-2026-089 has been reconciled against customer invoice.
                        </x-alert>

                        <x-alert tone="warning" variant="accent-left" title="Account Balance Discrepancy" icon="alert-triangle">
                            Trial balance debit and credit totals currently differ by $42.50.
                            <x-slot:action>
                                <x-button variant="outline-primary" size="sm" icon="refresh-cw">Recalculate Totals</x-button>
                            </x-slot:action>
                        </x-alert>

                        <x-alert tone="danger" title="API Gateway Connection Timeout" icon="shield-alert">
                            Unable to contact SAP B1 integration server. Please retry in a few moments.
                        </x-alert>
                    </div>
                </x-content-card>
            </div>

            <!-- Skeleton Loaders -->
            <div class="col-lg-6">
                <x-content-card title="Skeleton Shimmer Loaders" subtitle="Smooth placeholder loading states for async data fetching">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <small class="text-secondary fw-semibold d-block mb-1">Text & Title Shimmers</small>
                            <x-skeleton type="title" />
                            <x-skeleton type="text" :lines="3" />
                        </div>
                        <div>
                            <small class="text-secondary fw-semibold d-block mb-1">Card Skeleton Loader</small>
                            <x-skeleton type="card" />
                        </div>
                    </div>
                </x-content-card>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 4: Accordions & Surfaces
         ========================================================================= -->
    <div id="sec-surfaces" class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Accordions & Surfaces</h2>
            <span class="badge bg-info-subtle text-info rounded-pill px-2.5">Mantis Surfaces</span>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <x-content-card title="Mantis Smooth Accordion" subtitle="Clean collapsibles with icons, active highlight, and rotating chevrons">
                    <x-accordion id="demo-accordion-list">
                        <x-accordion.item id="acc-1" title="General Ledger Posting Logic" icon="book-open" :open="true" badge="Active">
                            Transactions entered into the system automatically generate double-entry journal records. Debit and Credit accounts update running balances instantaneously.
                        </x-accordion.item>
                        <x-accordion.item id="acc-2" title="SAP B1 Database Synchronization" icon="database" badge="Automated">
                            Nightly sync pulls incoming payments, outgoing transfers, and purchase invoice items directly from Microsoft SQL Server into the reporting warehouse.
                        </x-accordion.item>
                        <x-accordion.item id="acc-3" title="Permission & Security Roles" icon="shield-check">
                            Role-based access control enforces module-level reading, writing, and export permissions using standard Spatie Laravel Permission middleware.
                        </x-accordion.item>
                    </x-accordion>
                </x-content-card>
            </div>

            <div class="col-lg-5">
                <x-content-card title="Mantis Underline Tabs" subtitle="Clean responsive navigation tabs">
                    <ul class="mantis-tabs-underline" role="tablist">
                        <li>
                            <a class="mantis-tab-link active" href="javascript:void(0);">
                                <i data-lucide="layout-grid"></i> Overview
                            </a>
                        </li>
                        <li>
                            <a class="mantis-tab-link" href="javascript:void(0);">
                                <i data-lucide="settings"></i> Settings
                            </a>
                        </li>
                        <li>
                            <a class="mantis-tab-link" href="javascript:void(0);">
                                <i data-lucide="shield"></i> Security
                            </a>
                        </li>
                    </ul>
                    <div class="p-3 bg-sub rounded-3 border">
                        <h6 class="fw-bold mb-1">Active Tab Content</h6>
                        <p class="text-secondary small mb-0">Underline indicators glide across active tabs with matching theme color tokens.</p>
                    </div>
                </x-content-card>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 5: Buttons, Modals & Empty States
         ========================================================================= -->
    <div id="sec-buttons" class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Buttons, Modals & Actions</h2>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5">Mantis Actions</span>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <x-content-card title="Button System & Variations" subtitle="Solid, outline, icon, and dynamic loading button states">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <x-button icon="save">Primary</x-button>
                        <x-button variant="secondary" icon="x">Secondary</x-button>
                        <x-button variant="danger" icon="trash-2">Danger</x-button>
                        <x-button variant="outline-primary" icon="download">Outline</x-button>
                        <x-button :loading="true">Processing</x-button>
                        <x-icon-button icon="settings" label="Settings" />
                        <x-icon-button icon="share-2" label="Share" />
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <x-button variant="primary" data-bs-toggle="modal" data-bs-target="#componentModal" icon="square-pen">
                            Launch Demonstration Modal
                        </x-button>
                        <x-delete-confirm-button />
                    </div>
                </x-content-card>
            </div>

            <div class="col-lg-5">
                <x-content-card title="Empty State Representation" subtitle="Modern illustrated empty indicator">
                    <x-empty-state title="No Data Found" message="Try adjusting your filter selection or date range criteria." icon="file-search" />
                </x-content-card>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SECTION 6: Management Table & Data Display
         ========================================================================= -->
    <div id="sec-tables" class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-3">
            <h2 class="fs-5 fw-bold mb-0 text-dark">Management table</h2>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">DataGrid</span>
        </div>

        <x-content-card title="Management table presentation" subtitle="DataTables-compatible container, toolbar, chips, and pagination">
            <x-management.list-toolbar search-placeholder="Search reports" :filter-count="1">
                <x-slot:filters>
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <x-form.select name="showcase_filter" icon="filter" :options="['all' => 'All statuses', 'active' => 'Active']" />
                        </div>
                    </div>
                </x-slot:filters>
                <x-slot:chips>
                    <x-filter-chip label="Status: Active" />
                </x-slot:chips>
                <x-slot:actions>
                    <x-button variant="outline-secondary" icon="columns-3">Columns</x-button>
                </x-slot:actions>
            </x-management.list-toolbar>
            <x-management.table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                        <th>Reference</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center text-secondary fw-semibold">1</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Grouped Actions Demo">
                                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 btn-action" title="Edit">
                                    <i data-lucide="file-pen-line"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1 btn-action" title="Delete">
                                    <i data-lucide="trash-2"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </td>
                        <td class="text-dark fw-medium">Grouped Actions (conjoined button group)</td>
                        <td><x-status-badge status="Grouped" tone="success" /></td>
                    </tr>
                    <tr>
                        <td class="text-center text-secondary fw-semibold">2</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 btn-action" title="View details">
                                <i data-lucide="eye"></i>
                                <span>View</span>
                            </button>
                        </td>
                        <td class="text-dark fw-medium">Single Action Button (standalone rounded)</td>
                        <td><x-status-badge status="Single" tone="info" /></td>
                    </tr>
                    <tr>
                        <td class="text-center text-secondary fw-semibold">3</td>
                        <td class="text-center">
                            <x-dropdown label="Row actions">
                                <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#componentModal">
                                    <i data-lucide="eye"></i> Preview
                                </button>
                            </x-dropdown>
                        </td>
                        <td class="text-muted">Connected DataTable rows appear here.</td>
                        <td><x-status-badge status="Example" tone="warning" /></td>
                    </tr>
                </tbody>
            </x-management.table>
            <x-pagination :current-page="1" :last-page="1" />
        </x-content-card>
    </div>

    <!-- Modal Demonstration -->
    <x-modal id="componentModal" title="Professional Reusable Modal" size="lg">
        <div class="p-2">
            <x-alert tone="info" :dismissible="false" class="mb-3">
                This modal showcases form inputs within an accessible Bootstrap dialog with full dark mode support.
            </x-alert>
            <div class="row g-3">
                <div class="col-md-6">
                    <x-form.label for="modal-input-name" required>Name</x-form.label>
                    <x-form.input name="modal_name" id="modal-input-name" placeholder="E.g. Financial Q1" icon="tag" />
                </div>
                <div class="col-md-6">
                    <x-form.label for="modal-input-pass">Master Passkey</x-form.label>
                    <x-form.password-input name="modal_pass" id="modal-input-pass" placeholder="Enter passkey" />
                </div>
                <div class="col-12">
                    <x-form.checkbox name="modal_confirm" tone="primary" :checked="true" label="Confirm immediate ledger export" description="Generates signed PDF and Excel workbooks directly." />
                </div>
            </div>
        </div>
        <x-slot:footer>
            <x-button variant="secondary" data-bs-dismiss="modal">Close</x-button>
            <x-button variant="primary" icon="check" data-bs-dismiss="modal">Confirm & Save</x-button>
        </x-slot:footer>
    </x-modal>
</x-app-layout>
