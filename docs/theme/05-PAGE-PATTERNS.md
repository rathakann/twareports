# Page Patterns

| Pattern | Status | Existing evidence and structure |
|---|---|---|
| Dashboard | IMPLEMENTED | `dashboard/index.blade.php`; page title/breadcrumb, KPI cards, activity/summary surfaces and responsive card grid. No chart implementation verified. |
| Management index | IMPLEMENTED | `__modules/index.blade.php`, `administration/setup/modules/index.blade.php`, products/categories/units/UoM groups and settings indexes; page header, action button, optional stats accordion, list toolbar, `.t-card` DataTable, row action controls. |
| Create/edit form | IMPLEMENTED | products/categories/units/UoM groups/customers/branches/currencies/exchange-rates/document sequences/inventory adjustments/opening forms; page breadcrumb, white card/form sections, labels, required inputs, validation, Cancel + primary Save. |
| Detail/read-only | IMPLEMENTED | products, module, currency, exchange-rate, inventory adjustment/opening `show.blade.php`; breadcrumb, card sections, status badge/value pairs, back/edit actions. |
| Settings | IMPLEMENTED but inconsistent | settings menu plus individual management/list pages; some use current toolbar and others older DataTables/inline scripts. |
| Auth/guest | IMPLEMENTED | `layouts/guest.blade.php`, `auth/*.blade.php`; Laravel Breeze component set and custom app assets. |
| POS | IMPLEMENTED | `sales/pos/index.blade.php` + partials; dense operational two-pane/catalog-cart layout and mobile drawer. |
| Empty/loading/error | PARTIAL | POS has empty catalog; tables use processing GIF; common blank/error page design is not found. |

## Reusable listing composition

1. Use `<x-management.page-header>` or the repeated equivalent with breadcrumb.
2. Optional `<x-management.statistics>` for count summaries.
3. Put filters/search/actions in `<x-management.list-toolbar>`.
4. Wrap a responsive Bootstrap/Yajra DataTable in `.t-card > .t-table-container`.
5. Render row badges/actions with Bootstrap + Lucide; re-run `lucide.createIcons()` after DataTables redraw.

## Reusable form composition

Use a main page slot with `container-fluid px-4 py-3/4`; use card sections with header/body, Bootstrap grid (`row g-3`), label, themed input/select, help/validation feedback, and a trailing action row. Use Select2 only where a searchable/enhanced selection is required, and Flatpickr for `.datepicker` fields. Match source behavior rather than adding unsupported autosave, wizard steps, floating action controls or custom loading skeletons.

## Status and variance

The current theme mixes new `management` components with legacy page-local markup. Within a new project, standardize on the management family while retaining the visual token values. Do not report a component as a reusable source component merely because a one-off page contains similar HTML.
