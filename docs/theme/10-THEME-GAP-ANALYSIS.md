# Theme Gap Analysis

This document distinguishes observed shortcomings from the verified theme specification.

| Priority | Observed gap / inconsistency | Evidence | Recommendation (not current behavior) |
|---|---|---|---|
| High | Header organization, notifications, language and profile destinations include static/demo content or dashboard links. | `layouts/topbar.blade.php` | Connect to authorized tenant/user/notification/locale services; remove placeholders only with product approval. |
| High | Global localization is incomplete; topbar UI does not prove locale persistence. | `lang/en|km/pos.php`, topbar, many English templates | Centralize translation keys and locale middleware; load/verify Khmer webfont. |
| High | Old settings pages duplicate DataTables/filter/SweetAlert code and styling while newer pages use management components. | `setting/*`, `components/management/*`, `public/custom.js` | Incrementally migrate pages to a documented toolbar/table API without changing behavior. |
| Medium | Theme value sources conflict: static `--pri:#1b4fe0` versus drawer blue `#2563EB`; radius base is 12px but control override defaults to 4px. | theme CSS + early app-layout script | Define precedence and token names; retain old values during migration. |
| Medium | Toast stack, Bootstrap alerts and SweetAlert2 coexist, with no single feedback contract. | shell, flash component, `custom.js`, settings scripts | Adopt one notification API and map severity/accessibility consistently. |
| Medium | PDF export code references global `pdfMake`, not verified as loaded. | `list-toolbar.blade.php`, package/layout audit | Install/declare it or remove/disable that format. |
| Medium | No shared empty/error-page/skeleton convention. | broad frontend search; POS has a local empty state | Add accessible reusable empty/error/loading components. |
| Medium | Responsive behavior is shell-solid but form/table/page-local layouts vary. | CSS media queries and individual views | Establish responsive form/table acceptance examples and test all page families. |
| Low | Bootstrap, Alpine, Tailwind-related tooling and public CSS coexist; CSS has many `!important` overrides. | `package.json`, Vite, theme CSS | Decide the supported layer order and reduce overrides in a scheduled refactor. |
| Low | Breadcrumb/header markup is often duplicated. | multiple forms/show/index pages | Use management page-header/breadcrumb component. |
| Low | Footer, charts, FAB and skeleton UI were not found. | layouts/frontend audit | Do not add them under the banner of theme replication; decide them as new product features. |

## Reusability recommendation

Preserve source visuals while making a portable layer in this order: tokens → app shell/navigation → primitives/forms/feedback → management header/toolbar/table/KPI components → module pages → specialized POS. Keep legacy selectors supported until every page is migrated. Do not let UI visibility substitute for route/policy authorization.
