# Applying the Theme to a New Laravel Blade Project

1. Install the verified frontend stack: Bootstrap 5.3, jQuery 3.7, Alpine, Lucide, Flatpickr, SweetAlert2, Select2 + Bootstrap 5 theme, and the Laravel DataTables integration selected by the application. Configure Vite entries for app CSS/JS. Do not add `pdfMake` unless PDF export is intentionally enabled.
2. Copy the token layer from `portable/bizlite-tokens.css`, then import it after Bootstrap/custom app CSS. Load Inter and Noto Sans Khmer through an authorized font source or self-hosted fonts.
3. Implement the `<x-app-layout>` shell from `portable/app-layout.blade.php`: early settings bootstrap in `<head>`, loader, overlays, topbar, nav rail, main slot and drawer. Keep server navigation data authoritative.
4. Add topbar/sidebar/horizontal-nav Blade partials. Use the documented IDs/classes so the JS interaction module can target them. Implement mobile drawer at 991px and register Escape/outside-click closing.
5. Add the management page-header, list-toolbar, KPI/statistics, status badge, buttons, form helpers and switch. Avoid duplicating these inside individual module views.
6. Build a dashboard from `portable/dashboard.blade.php`, then validate each KPI palette in light/dark. Do not add charts unless the new product requires them.
7. Build a listing from `portable/listing.blade.php`; connect its table to real server-side DataTables. Add only enabled export formats. Test search debounce, filters, columns, redraw icon replacement, zero records and table overflow.
8. Build a create/edit form from `portable/form.blade.php`. Wire real Laravel validation/errors; add Select2/Flatpickr only to relevant fields. Test disabled/read-only/error states.
9. Add `portable/theme-interactions.js` after Bootstrap/Lucide. Persist appearance preferences, handle drawer/panel behavior, create icons after dynamic DOM changes, and initialize date/select controls. Do not expose header demo data as production functionality.
10. Test at desktop/991/767/575/479 widths; light/dark; vertical/collapsed/horizontal nav; English/Khmer; mouse/keyboard/Escape; at 200% zoom; and a current browser console/network run. Check server authorization separately from hidden menu state.

The portable files intentionally give references rather than copying proprietary project pages. Use `08-THEME-REPLICATION-PROMPT.md` where a full standalone handoff is more useful.
