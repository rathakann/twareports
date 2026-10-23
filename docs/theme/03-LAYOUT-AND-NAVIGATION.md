# Layout and Navigation

## Application shell — IMPLEMENTED

`resources/views/layouts/app.blade.php` sets the locale-derived `<html lang>`, restores visual settings before paint, loads assets, displays a global AJAX loader/overlay/toast stack, and composes:

```text
.app#app
├─ fixed .topbar
├─ .hnav (visible only in horizontal mode)
├─ .body-area
│  ├─ .sidebar#sidebar
│  └─ main.main > optional fluid header + $slot
└─ .theme-drawer + .theme-overlay
```

The topbar is fixed at 55px; `.body-area` and main compensate for it. The vertical rail is a fixed 250px desktop surface, reducing to 56px when `.app.col` is set. The main region moves with that width; horizontal navigation hides the normal sidebar on desktop. Shell style references: `rtk-app-theme.css:232-246`, `1027-1091`, `1745-1808`.

## Sidebar — IMPLEMENTED

`layouts/sidebar.blade.php` builds menu groups from `$navModules`; it does not hardcode module links. A root may be a group header, a direct link, a split link/toggle, or an accordion parent. `layouts/nav-children.blade.php` recursively renders nested descendants. Classes are `.s-label`, `.nl`, `.nav-split`, `.nav-route`, `.nav-toggle`, `.ns`, `.nav-submenu`, `.nl-pop-wrap`, `.nl-popover`, `.pop-link`; active links use `.active`, current page gets `aria-current`, and toggles maintain `aria-expanded`.

Expanded desktop children open below the parent; collapsed rail children use a hover/click popover. The lower sidebar has an upward user profile menu. Menu visibility depends on the backend-provided `$navModules`; this is **permission-aware presentation**, not proof of authorization.

`rtk-app-theme.js` animates nested menus, has a 140ms popover close delay, honors `prefers-reduced-motion`, closes on outside click/Escape, and restores active route state. It repositions overlays to remain inside the viewport.

## Header and toolbar

`layouts/topbar.blade.php` contains a logo (200px max/42px visual), mobile hamburger, desktop collapse control, organization selector, max-450px search with Ctrl+K hint, notifications, language chooser, fullscreen, appearance flyout, theme-drawer trigger, and profile/logout dropdown. All icon controls are Lucide and 44px inside the topbar. The header controls have labels or titles, but not every expanded panel has full keyboard roving/focus management.

**PARTIAL backend functionality:** organization names/plan, notification feed/count, language selection, profile routes and some settings links are static/demo or route to Dashboard. `rtk-app-theme.js` supplies panel open/close interactions, Ctrl/Cmd+K focus, fullscreen, notification read state, and theme controls; this does not establish APIs for those panels.

## Page headers, breadcrumbs, footer

`components/management/page-header.blade.php` provides a flex-wrapped title/icon/actions layout with `Home › optional section › current page` breadcrumb and muted description. Numerous pages duplicate a very similar breadcrumb inline. No application footer implementation was found in the layouts. Breadcrumbs are **IMPLEMENTED**; a shared footer is **NOT FOUND**.

## Responsive navigation

At `max-width:991px` the normal sidebar becomes an off-canvas left drawer. `.open` plus the global `.overlay` opens it; the hamburger replaces desktop collapse. Main content uses full width. Horizontal desktop navigation also invokes the drawer at small widths. At 767/575/479 CSS reduces specific grids, headers and controls; overflow scroll/wrapping is used where necessary. Refer to `06-RESPONSIVE-AND-LOCALIZATION.md` for exact behavior and caveats.
