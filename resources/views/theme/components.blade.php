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
                <a class="nav-link py-1.5 px-3 rounded-2 fw-medium text-secondary" href="#sec-swal">
                    <i data-lucide="message-square" class="me-1" style="width: 15px; height: 15px;"></i> SweetAlert2
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

            <!-- SweetAlert2 Compact & Modern Showcase -->
            <div class="col-12" id="sec-swal">
                <x-content-card title="SweetAlert2 Modern & Compact Alerts" subtitle="Production-grade, high-density modal alerts, confirmation dialogs, prompt inputs, and animated corner toasts with dark mode support">
                    <x-slot:actions>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">Compact 380px Sizing</span>
                    </x-slot:actions>

                    <!-- Features Highlights Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2.5 rounded-3 border bg-sub h-100 d-flex align-items-center gap-2.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-primary-subtle text-primary p-2" style="width: 34px; height: 34px;">
                                    <i data-lucide="minimize-2" style="width: 16px; height: 16px;"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold small text-dark">Compact 380px Width</div>
                                    <div class="text-secondary" style="font-size: 11.5px;">Optimized density & padding</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2.5 rounded-3 border bg-sub h-100 d-flex align-items-center gap-2.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-success-subtle text-success p-2" style="width: 34px; height: 34px;">
                                    <i data-lucide="sparkles" style="width: 16px; height: 16px;"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold small text-dark">Refined 48px Icons</div>
                                    <div class="text-secondary" style="font-size: 11.5px;">Soft halos & proportional scale</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2.5 rounded-3 border bg-sub h-100 d-flex align-items-center gap-2.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-info-subtle text-info p-2" style="width: 34px; height: 34px;">
                                    <i data-lucide="moon" style="width: 16px; height: 16px;"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold small text-dark">Dark Mode Native</div>
                                    <div class="text-secondary" style="font-size: 11.5px;">Theme token integration</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2.5 rounded-3 border bg-sub h-100 d-flex align-items-center gap-2.5">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-warning-subtle text-warning p-2" style="width: 34px; height: 34px;">
                                    <i data-lucide="layers" style="width: 16px; height: 16px;"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold small text-dark">Backdrop Blur Glass</div>
                                    <div class="text-secondary" style="font-size: 11.5px;">Smooth entrance animation</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Trigger Categories -->
                    <div class="row g-4">
                        <!-- Category 1: Standard Modal Dialogs -->
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 bg-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                            <i data-lucide="layout-template" class="text-primary" style="width: 16px; height: 16px;"></i>
                                            Interactive Modal Dialogs
                                        </h6>
                                        <span class="badge bg-secondary-subtle text-secondary small">Click to test</span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button type="button" class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-1.5" id="btn-swal-success">
                                            <i data-lucide="check-circle-2" style="width: 15px; height: 15px;"></i> Success Alert
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1.5" id="btn-swal-danger">
                                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i> Delete Confirm
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm d-inline-flex align-items-center gap-1.5" id="btn-swal-warning">
                                            <i data-lucide="alert-triangle" style="width: 15px; height: 15px;"></i> Warning Alert
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1.5" id="btn-swal-info">
                                            <i data-lucide="info" style="width: 15px; height: 15px;"></i> Info Notice
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1.5" id="btn-swal-prompt">
                                            <i data-lucide="pen-square" style="width: 15px; height: 15px;"></i> Prompt Input
                                        </button>
                                        <button type="button" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-1.5" id="btn-swal-loading">
                                            <i data-lucide="loader-2" style="width: 15px; height: 15px;"></i> Async Loading
                                        </button>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-2 bg-sub border font-monospace text-secondary mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark small">Modal Usage Example</span>
                                        <span class="badge bg-light text-muted border" style="font-size: 10px;">JavaScript</span>
                                    </div>
                                    <pre class="mb-0 text-secondary" style="font-size: 11.5px; line-height: 1.45; overflow-x: auto;"><code>Swal.fire({
  icon: 'warning',
  title: 'Delete Financial Period?',
  text: 'This action cannot be undone.',
  showCancelButton: true,
  confirmButtonText: 'Yes, delete',
  confirmButtonColor: 'var(--red)'
});</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Category 2: Modern Corner Toasts -->
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 bg-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                            <i data-lucide="bell-ring" class="text-primary" style="width: 16px; height: 16px;"></i>
                                            Corner Toasts (<code class="small text-primary">toast: true</code>)
                                        </h6>
                                        <span class="badge bg-secondary-subtle text-secondary small">Auto-dismissing</span>
                                    </div>

                                    <!-- Toast Position Options -->
                                    <div class="mb-3 p-2.5 bg-sub rounded-2 border">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="form-label mb-0 fw-semibold text-dark small d-flex align-items-center gap-1.5">
                                                <i data-lucide="crosshair" class="text-primary" style="width: 14px; height: 14px;"></i>
                                                Toast Position Option
                                            </label>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-0.5" id="demoToastPosBadge">top-end</span>
                                        </div>
                                        <div class="row g-1.5 mb-1.5">
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1 text-nowrap" data-demo-toast-pos="top-start" style="font-size: 11.5px;">
                                                    <i data-lucide="arrow-up-left" style="width: 13px; height: 13px;"></i> Top Left
                                                </button>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1 text-nowrap" data-demo-toast-pos="top" style="font-size: 11.5px;">
                                                    <i data-lucide="arrow-up" style="width: 13px; height: 13px;"></i> Top Center
                                                </button>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1 text-nowrap active" data-demo-toast-pos="top-end" style="font-size: 11.5px;">
                                                    <i data-lucide="arrow-up-right" style="width: 13px; height: 13px;"></i> Top Right
                                                </button>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1 text-nowrap" data-demo-toast-pos="bottom-start" style="font-size: 11.5px;">
                                                    <i data-lucide="arrow-down-left" style="width: 13px; height: 13px;"></i> Bottom Left
                                                </button>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1 text-nowrap" data-demo-toast-pos="bottom" style="font-size: 11.5px;">
                                                    <i data-lucide="arrow-down" style="width: 13px; height: 13px;"></i> Bottom Center
                                                </button>
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 py-1 text-nowrap" data-demo-toast-pos="bottom-end" style="font-size: 11.5px;">
                                                    <i data-lucide="arrow-down-right" style="width: 13px; height: 13px;"></i> Bottom Right
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 11px;">Select a position to test live triggers and inspect the generated code below.</small>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button type="button" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1.5 text-white" id="btn-toast-success">
                                            <i data-lucide="check" style="width: 15px; height: 15px;"></i> Toast Success
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1.5 text-white" id="btn-toast-error">
                                            <i data-lucide="shield-alert" style="width: 15px; height: 15px;"></i> Toast Error
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center gap-1.5 text-dark" id="btn-toast-warning">
                                            <i data-lucide="clock" style="width: 15px; height: 15px;"></i> Toast Warning
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1.5" id="btn-toast-info">
                                            <i data-lucide="bell" style="width: 15px; height: 15px;"></i> Toast Info
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1.5" id="btn-toast-bootstrap">
                                            <i data-lucide="sparkles" style="width: 15px; height: 15px;"></i> Bootstrap Toast
                                        </button>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-2 bg-sub border font-monospace text-secondary mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark small">Toast Usage Example</span>
                                        <span class="badge bg-light text-muted border" style="font-size: 10px;">JavaScript</span>
                                    </div>
                                    <pre class="mb-0 text-secondary" style="font-size: 11.5px; line-height: 1.45; overflow-x: auto;"><code id="toastCodeExample">Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'success',
  title: 'Payment entry posted successfully.',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});</code></pre>
                                </div>
                            </div>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Modal Alert Demos
            const btnSuccess = document.getElementById('btn-swal-success');
            if (btnSuccess) {
                btnSuccess.addEventListener('click', function () {
                    window.Swal.fire({
                        icon: 'success',
                        title: 'Account Settings Saved',
                        text: 'Your security preferences and organization profiles were updated successfully.',
                        confirmButtonText: 'Great, thanks'
                    });
                });
            }

            const btnDanger = document.getElementById('btn-swal-danger');
            if (btnDanger) {
                btnDanger.addEventListener('click', function () {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'Delete Financial Period?',
                        text: 'All associated journal entries and trial balance snapshots will be removed. This action cannot be reversed.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete period',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: 'var(--red)'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.Swal.fire({
                                icon: 'success',
                                title: 'Period Deleted',
                                text: 'The selected period has been removed from reporting view.',
                                confirmButtonText: 'Close'
                            });
                        }
                    });
                });
            }

            const btnWarning = document.getElementById('btn-swal-warning');
            if (btnWarning) {
                btnWarning.addEventListener('click', function () {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'Session Timeout Warning',
                        text: 'Your active ERP session will expire in 5 minutes due to inactivity. Would you like to keep working?',
                        showCancelButton: true,
                        confirmButtonText: 'Keep Working',
                        cancelButtonText: 'Sign Out'
                    });
                });
            }

            const btnInfo = document.getElementById('btn-swal-info');
            if (btnInfo) {
                btnInfo.addEventListener('click', function () {
                    window.Swal.fire({
                        icon: 'info',
                        title: 'Nightly Sync Notice',
                        text: 'SAP Business One database sync will run automatically at 02:00 AM UTC. Reporting remains available during read mode.',
                        confirmButtonText: 'Got it'
                    });
                });
            }

            const btnPrompt = document.getElementById('btn-swal-prompt');
            if (btnPrompt) {
                btnPrompt.addEventListener('click', function () {
                    window.Swal.fire({
                        title: 'Clone Report Template',
                        text: 'Please enter a name for the duplicated report view:',
                        input: 'text',
                        inputPlaceholder: 'E.g. Profit & Loss Q2 - Operations',
                        showCancelButton: true,
                        confirmButtonText: 'Create Clone',
                        cancelButtonText: 'Cancel',
                        inputValidator: (value) => {
                            if (!value || !value.trim()) {
                                return 'Please provide a valid template name!';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            window.Swal.fire({
                                toast: true,
                                position: currentDemoToastPosition,
                                icon: 'success',
                                title: `Created template "${result.value.trim()}"`,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    });
                });
            }

            const btnLoading = document.getElementById('btn-swal-loading');
            if (btnLoading) {
                btnLoading.addEventListener('click', function () {
                    window.Swal.fire({
                        title: 'Generating Excel Workbook...',
                        text: 'Extracting 4,820 balance sheet line items. Please wait.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            window.Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        window.Swal.fire({
                            icon: 'success',
                            title: 'Workbook Ready',
                            text: 'TrialBalance_2026_Q1.xlsx has been compiled and is ready for download.',
                            confirmButtonText: 'Download File'
                        });
                    }, 1800);
                });
            }

            // Corner Toast Demos with Dynamic Position Option
            let currentDemoToastPosition = localStorage.getItem('tc_toast_position') || 'top-end';
            const demoToastBadge = document.getElementById('demoToastPosBadge');
            const toastCodeExample = document.getElementById('toastCodeExample');
            const demoPosButtons = document.querySelectorAll('[data-demo-toast-pos]');

            const updateDemoToastPosition = (pos, triggerNotification = false) => {
                currentDemoToastPosition = pos;
                if (demoToastBadge) demoToastBadge.textContent = pos;
                demoPosButtons.forEach(btn => {
                    const isActive = btn.dataset.demoToastPos === pos;
                    btn.classList.toggle('active', isActive);
                    btn.classList.toggle('btn-primary', isActive);
                    btn.classList.toggle('text-white', isActive);
                    btn.classList.toggle('btn-outline-secondary', !isActive);
                });
                if (toastCodeExample) {
                    toastCodeExample.textContent = `Swal.fire({
  toast: true,
  position: '${pos}',
  icon: 'success',
  title: 'Payment entry posted successfully.',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});`;
                }
                if (triggerNotification) {
                    window.Swal.fire({
                        toast: true,
                        position: pos,
                        icon: 'info',
                        title: `Toast position updated to: ${pos}`,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            };

            demoPosButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const pos = this.dataset.demoToastPos;
                    localStorage.setItem('tc_toast_position', pos);
                    if (typeof applyThemePreferences === 'function') applyThemePreferences();
                    if (typeof syncThemeControls === 'function') syncThemeControls();
                    updateDemoToastPosition(pos, false);
                });
            });

            document.addEventListener('twa:toast-position-changed', function (e) {
                if (e.detail && e.detail.position) {
                    updateDemoToastPosition(e.detail.position, false);
                }
            });

            updateDemoToastPosition(currentDemoToastPosition, false);

            const btnToastSuccess = document.getElementById('btn-toast-success');
            if (btnToastSuccess) {
                btnToastSuccess.addEventListener('click', function () {
                    window.Swal.fire({
                        toast: true,
                        position: currentDemoToastPosition,
                        icon: 'success',
                        title: 'Payment entry posted successfully.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            }

            const btnToastError = document.getElementById('btn-toast-error');
            if (btnToastError) {
                btnToastError.addEventListener('click', function () {
                    window.Swal.fire({
                        toast: true,
                        position: currentDemoToastPosition,
                        icon: 'error',
                        title: 'Connection timeout to SAP gateway.',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    });
                });
            }

            const btnToastWarning = document.getElementById('btn-toast-warning');
            if (btnToastWarning) {
                btnToastWarning.addEventListener('click', function () {
                    window.Swal.fire({
                        toast: true,
                        position: currentDemoToastPosition,
                        icon: 'warning',
                        title: 'Debit and credit balance mismatch ($42.50).',
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    });
                });
            }

            const btnToastInfo = document.getElementById('btn-toast-info');
            if (btnToastInfo) {
                btnToastInfo.addEventListener('click', function () {
                    window.Swal.fire({
                        toast: true,
                        position: currentDemoToastPosition,
                        icon: 'info',
                        title: 'New tax tables imported for FY2026.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            }

            const btnToastBootstrap = document.getElementById('btn-toast-bootstrap');
            if (btnToastBootstrap) {
                btnToastBootstrap.addEventListener('click', function () {
                    if (typeof window.showToast === 'function') {
                        window.showToast(`Bootstrap toast triggered at ${currentDemoToastPosition}`, 'primary');
                    }
                });
            }
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>
    @endpush
</x-app-layout>
