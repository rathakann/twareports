# Responsive, Localization, and Accessibility

## Responsive behavior — IMPLEMENTED

| Width | Verified adaptation |
|---|---|
| Desktop (>991px) | fixed 250px vertical sidebar or 56px collapsed rail; optional horizontal nav; fixed topbar; main offsets appropriately. |
| `<=991px` | sidebar becomes off-canvas drawer controlled by hamburger + overlay; main is full width. Several toolbars/grid layouts wrap. |
| `<=767px` | narrower table search (`220px` in management CSS), KPI grid changes and compact toolbars; table wrappers retain horizontal scroll. |
| `<=575px` / `<=479px` | additional KPI/card and header reductions in `rtk-app-theme.css`; individual page markup determines exact form stacking. |
| `<=1199px` | dashboard KPI grid adjustment. |

The concrete CSS media queries are at `public/css/rtk-app-theme.css:1072,1760,1980,2349,2979,3572,4466,4472,4530`. The theme respects `prefers-reduced-motion` in CSS and sidebar/horizontal-nav JS. Mobile tables are scrollable rather than converted to cards. Modal mobile-specific full-screen behavior is **NOT FOUND**.

## Localization — PARTIAL

The Laravel application identity is Khmer + English. `lang/en/pos.php` and `lang/km/pos.php` are verified translations for POS. POS chooses Khmer product/unit content when `<html lang="km">` and source Khmer fields are present. The topbar includes English/Khmer controls and many `data-i18n` markers, but its switcher is client UI rather than verified server locale persistence/complete catalog translation. Numerous template strings remain English. Therefore global locale switching, localized date/number presentation and complete translation coverage are **PARTIAL**.

`--font-kh: 'Noto Sans Khmer', sans-serif` is declared and Khmer-specific classes/brand styles reference it. The audited base app layout does not verify a webfont import for Noto Sans Khmer; transfer/load that font explicitly in a new project. Ensure Khmer controls can grow vertically and do not truncate; no broad RTL implementation was found.

## Accessibility evidence and limits

**IMPLEMENTED:** semantic `<nav>` and `aria-label`, `aria-current`, sidebar accordion `aria-controls`/`aria-expanded`, button labels/titles, modal/Bootstrap keyboard semantics, focus styling on inputs/switches, `role=switch`, `aria-live` toast stack, and Escape/outside-click dismissal for theme/navigation panels.

**PARTIAL / recommended validation:** some controls are static/demo; focus trapping/return focus in custom panels, sufficient contrast across all runtime primary colors, complete form label associations, language semantics, and DataTables keyboard paths were not conclusively audited. Verify real browser keyboard behavior, zoom/reflow, screen reader announcements and Khmer typography before release.
