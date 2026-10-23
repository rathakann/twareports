# Component Library

## Verified reusable Blade components

| Component | Status / implementation | Source |
|---|---|---|
| App layout | shell and asset loading described in layout doc | `layouts/app.blade.php` |
| Page header | title, Lucide icon, optional section/actions, breadcrumb, description | `components/management/page-header.blade.php` |
| List toolbar | search (350ms debounce), filter pane, chips/removal, filter count, column chooser persistence, export and DataTables hooks | `components/management/list-toolbar.blade.php` |
| Statistics | collapsed Bootstrap accordion of KPI summary pills/cards; expanded state persists | `components/management/statistics.blade.php` |
| Status badge | shared status presentation | `components/management/status-badge.blade.php` |
| Switch field | semantic switch, hidden false value, error/disabled states, `role=switch` | `components/form/switch-field.blade.php` |
| Input/label/error | Breeze-derived `text-input`, `input-label`, `input-error` | `components/*.blade.php` |
| Buttons | primary, secondary, danger components; Bootstrap button class based | `components/*-button.blade.php` |
| Modal/dropdown | Alpine/Breeze components exist alongside Bootstrap modals/dropdowns | `components/modal.blade.php`, `dropdown*.blade.php` |
| Flash | success/danger dismissible Bootstrap alerts | `components/flash-message.blade.php` |

## Cards and dashboard widgets — IMPLEMENTED

`.kpi-grid` supplies responsive grid cards; `.kpi-card` has a soft shadow/border hover rise, label, large value, delta/subtext and colored `.kpi-ic` cell (`.k-bl`, `.k-gr`, `.k-yw`, `.k-rd`, `.k-pu`, `.k-or`). `dashboard/index.blade.php` is the primary sample. `management/statistics` uses the same KPI language in an accordion. Chart containers/charts are **NOT FOUND** in the audited frontend.

## Tables and lists — IMPLEMENTED, with legacy variants

Modern list pages place a table in `.t-card`, use `.t-hdr` for toolbar layout, `.t-table-container`, `.t-hdr-actions`, custom `dt-search-inp`, `.af-chip`, `.col-panel`, `fil-btn` and `bulk-btn` classes. Tables use light/dark table tokens, striped/hover rows and responsive wrappers. `list-toolbar` bridges filters and DataTables: server requests receive search/filter fields, search is debounced 350ms, selected filters become removable chips, show/hide column state is localStorage-backed, and exports preserve applied query parameters. PDF export expects global `pdfMake`; this dependency is **UNCERTAIN/PARTIAL** because it is referenced but absent from `package.json` and the base layout.

Older settings pages use Yajra/Laravel DataTables with `.table-striped`/`.table-hover`, local JS and sometimes duplicated filter/confirmation code. `public/custom.js` customizes the processing display and adds a blur class while loading. Sorting, pagination and export depend on per-table DataTables configuration; no single universal table configuration exists.

## Forms — IMPLEMENTED

Bootstrap `.form-control`, `.form-select`, `.input-group`, `.is-invalid`, `invalid-feedback`, standard checkbox/radio and custom `.custom-switch`/`.switch-box` are theme-aware. Inputs commonly use 42px left padding with `.input-icon-left` and `.input-icon-addon`; focus colors the icon primary. Select2 is initialized globally on `.form-select` and `.form-select-filter` using the Bootstrap 5 theme. Flatpickr activates `.datepicker` in `Y-m-d` format, allows typing and adds Clear/Today footer actions plus an inline clear control. Product, category, unit, branch, currency and stock-adjustment forms are inspected concrete examples.

## Buttons, feedback and overlays

- **IMPLEMENTED:** Bootstrap `.btn-primary` is forced to `--pri`; outline-primary reverses on hover. Theme CSS forces button content into a nonwrapping inline-flex row. Standard success/warning/danger/secondary buttons remain Bootstrap-derived.
- **IMPLEMENTED:** 36px generic `.btn-ic`, 44px topbar icon controls, icon + label actions, Bootstrap button groups.
- **IMPLEMENTED:** Bootstrap modal/offcanvas/tooltip/accordion behavior (Bootstrap bundle); theme applies card surfaces/radii and dark overrides. POS includes a clear-cart confirmation modal.
- **IMPLEMENTED:** SweetAlert2 is exposed as `window.Swal`; settings pages use confirm/error/success flows. Session alerts and legacy `AlertMsg` are Bootstrap alerts. `#ksToastStack` and toast styles exist in the shell/theme engine.
- **PARTIAL:** multiple feedback mechanisms coexist; the exact global toast producer/standardized empty state is not shared. Skeleton loaders are **NOT FOUND**. Global GIF loader and DataTables processing loader are implemented.

## POS — specialized existing pattern

`sales/pos/index.blade.php` with `partials/catalog`, `cart`, and `mobile-cart-drawer` implements a searchable category-chip catalog, product cards, async paging/load-more, cart editing, quantity/unit/discount updates, clear confirmation, posting action, responsive cart drawer, and a keyboard-wedge scanner heuristic (rapid 6+ chars, 30ms gaps, Enter/Tab). It is an application-specific component—not a generic dashboard/list pattern—and needs its API endpoints to be recreated.
