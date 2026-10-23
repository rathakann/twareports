# Theme Feature Inventory

Statuses are source-evidence classifications, not product promises. `NOT FOUND` means no shared implementation was discovered in the inspected frontend.

| Category / feature | Status | Evidence / classes | Dependencies / behavior |
|---|---|---|---|
| Color tokens and light/dark | IMPLEMENTED | `rtk-app-theme.css:45-195`, `data-theme` | early localStorage restore; CSS variables |
| Primary/font/radius customization | IMPLEMENTED | `layouts/app.blade.php`, `theme-drawer.blade.php`, `rtk-app-theme.js` | localStorage `tc_*`; custom hex validation |
| Fixed shell/topbar/rail | IMPLEMENTED | `layouts/app.blade.php`, CSS shell refs | collapse/mobile overlay |
| Horizontal navigation | IMPLEMENTED | `layouts/hnav*.blade.php` | hover/click dropdown script |
| Nested/permission-supplied navigation | IMPLEMENTED | sidebar/nav-children | `$navModules`, accordion/popover |
| Footer | NOT FOUND | layouts audit | — |
| Logo variants | IMPLEMENTED | `public/assets/logo_small.png`, `logo_square.png` | topbar fallback graphic |
| Header search/fullscreen/profile | IMPLEMENTED | `layouts/topbar.blade.php` | Ctrl/Cmd+K, Fullscreen API, profile dropdown |
| Organization / notifications / language | PARTIAL | `layouts/topbar.blade.php` | UI interactions; static/demo data and incomplete locale integration |
| KPI cards/statistics accordion | IMPLEMENTED | `.kpi-*`, `components/management/statistics.blade.php` | Bootstrap collapse + localStorage |
| Charts | NOT FOUND | frontend search | — |
| DataTable list design | IMPLEMENTED | `.t-card`, list toolbar, index pages | Yajra/DataTables, jQuery |
| Table filter chips/search/columns | IMPLEMENTED | `list-toolbar.blade.php` | debounce, localStorage, redraw hooks |
| CSV/XLS/PDF exports | PARTIAL | list toolbar + vendor views | DataTables/export configuration varies; PDF expects unverified `pdfMake` |
| Responsive table | IMPLEMENTED | `.table-responsive`, wrappers | horizontal scrolling |
| Standard empty state | PARTIAL | POS/catalog only | table-wide shared empty state not found |
| Inputs/selects/validation | IMPLEMENTED | Blade components/forms/CSS | Bootstrap 5, Select2 |
| Searchable dropdown | IMPLEMENTED | `public/custom.js` | Select2 Bootstrap 5 theme |
| Date picker with clear/today | IMPLEMENTED | `public/custom.js`, theme CSS | Flatpickr |
| Checkbox/radio | IMPLEMENTED | Bootstrap + module checkbox CSS | custom visual sizing for modules |
| Toggle switch | IMPLEMENTED | `components/form/switch-field.blade.php` | native checkbox, ARIA switch |
| Primary/outline/icon actions | IMPLEMENTED | Bootstrap overrides / `.btn-ic` | Lucide |
| Floating action button | NOT FOUND | frontend search | — |
| Bootstrap modal/dropdown/tooltip/offcanvas | IMPLEMENTED | components/pages/CSS | Bootstrap 5 |
| SweetAlert confirmation | IMPLEMENTED | settings pages, `app.js` | SweetAlert2 |
| Toast stack | PARTIAL | shell + CSS/theme JS | producer coverage not standardized |
| AJAX/DataTables loading | IMPLEMENTED | `#ajax-loading`, `public/custom.js` | GIF, DataTables events |
| Skeleton loading | NOT FOUND | frontend search | — |
| Auth pages | IMPLEMENTED | guest/auth views | Breeze/Alpine components |
| POS catalog/cart/mobile cart/scanner | IMPLEMENTED | POS views | fetch, Bootstrap modal, Lucide |
| Khmer/English POS translations | IMPLEMENTED | `lang/en|km/pos.php`, POS view | locale-conditioned labels |
| Global Khmer/English translation | PARTIAL | topbar + limited lang files | manual/static strings remain |

Dependency versions are in `package.json`: Bootstrap `^5.3.3`, jQuery `^3.7.1`, Alpine `^3.4.2`, Lucide `^1.47.0`, Flatpickr `^4.6.13`, Select2 Bootstrap theme `^1.3.0` (Select2 CDN `4.1.0-rc.0`), SweetAlert2 `^11.12.1`, DataTables Editor packages and `laravel-datatables-vite ^0.5.2`; PHP packages include Yajra Laravel DataTables/Buttons `^13.0`.
