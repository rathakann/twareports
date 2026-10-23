# BizLite Theme Overview

## Scope and evidence

This is a source-audited description of the current Blade UI in this repository, not a redesign. It covers 113 Blade files under `resources/views`, layouts, public theme assets, Vite entries, language files, and package manifests inspected on 2026-10-16. Source references in this documentation are repository-relative. `IMPLEMENTED` means the feature was verified in source; runtime placeholders are called out explicitly.

## Identity

BizLite is a compact desktop-first business application theme: a fixed white/dark top bar, a left navigation rail, pale blue-gray page canvas, bright-blue primary affordances, soft cards, 14px Inter body type, and Lucide line icons. The visual system is token-driven in `public/css/rtk-app-theme.css:45-195`; it supports light/dark mode, a user-selected primary color, control radius, font, and vertical/horizontal navigation.

**IMPLEMENTED foundations:** default blue `#1b4fe0`, calm neutral surfaces, card elevation, 12px card radius, 4px small-control radius, and 0.22s material-style transitions. The app uses CSS custom properties rather than a compiled Bootstrap theme override.

## Frontend architecture

| Layer | Verified implementation |
|---|---|
| Application shell | `<x-app-layout>` resolves to `resources/views/layouts/app.blade.php`; it includes topbar, horizontal nav, vertical sidebar, main slot, overlay, toast stack, and theme drawer. |
| Style cascade | Vite CSS (`app.css`, `app.scss`, `custom.css`) then public `css/rtk-app-theme.css`; the latter is the canonical visual layer. |
| Behaviour | Vite `resources/js/app.js` exposes Bootstrap, Flatpickr, Alpine, SweetAlert2 and Lucide; `public/js/rtk-app-theme.js` owns shell/theme interactions; `public/custom.js` owns legacy Select2, date and DataTables hooks. |
| Navigation | Data-driven `$navModules`, with active route checks and nested children in `layouts/sidebar.blade.php`, `nav-children.blade.php`, `hnav*.blade.php`. |
| Page primitives | Bootstrap 5 grid/card/form/modal/accordion/pagination plus custom `kpi-*`, `t-*`, filter, navigation and settings classes. |

`resources/views/components/management/*` are the strongest reusable page-level component family. Many older settings views also carry local styles/scripts; therefore the real system is shared-core plus legacy/page-local extensions.

## Verified modes and assets

- **IMPLEMENTED:** light and dark (`data-theme` and `data-bs-theme`), persisted in `localStorage` as `tc_mode`/legacy `ks_theme`.
- **IMPLEMENTED:** vertical desktop sidebar (default), collapsed sidebar, mobile drawer, and desktop horizontal navigation; `tc_layout` and `ks_col` persist the choice.
- **IMPLEMENTED:** custom primary palette, arbitrary six-digit custom color, Inter/Roboto/Poppins/Public Sans choice, and adjustable rounding via `layouts/theme-drawer.blade.php` + `rtk-app-theme.js`.
- **IMPLEMENTED assets:** `public/assets/logo_small.png`, `logo_square.png`, favicon/OG asset reference `assets/bizlite-icon-900x900.png`, `loading.gif`, and npm Lucide icons. Transfer only assets you own; source licensing of third-party libraries remains applicable.
- **PARTIAL:** header organization, notifications, profile destination, language UI and several messages use demo/hardcoded data or dashboard routes in `layouts/topbar.blade.php`; they are not complete backend integrations.

## Major page families inspected

Dashboard (`dashboard/index.blade.php`), management/modules (`__modules/*` and `administration/setup/modules/*`), products, categories, units/UoM groups, customers, inventory adjustments/opening/balances/movements/valuation, settings/currencies/exchange rates/document sequences/branches/business settings, roles/audit log, auth, and POS (catalog/cart/mobile cart partials) were inspected by path and shared-pattern search. This confirms summaries, table/list, forms, read-only details, settings, auth, and POS are existing patterns; charts were not found.

See `08-THEME-REPLICATION-PROMPT.md` for the portable build brief. Recommendations are deliberately isolated in `10-THEME-GAP-ANALYSIS.md`.
