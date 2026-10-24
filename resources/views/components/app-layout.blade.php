@props(['title' => null, 'navigation' => []])
@php
    $navigation = \App\Support\Navigation::filter(! empty($navigation) ? $navigation : config('navigation', []));
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <script>
        (() => {
            const root = document.documentElement;
            const validColor = /^#[0-9a-f]{6}$/i;
            const fonts = ['Inter', 'Roboto', 'Poppins', 'Public Sans'];
            const fontSizes = { small: '12px', normal: '14px', large: '16px' };
            const mode = localStorage.getItem('tc_mode') === 'dark' ? 'dark' : 'light';
            const primary = localStorage.getItem('tc_primary');
            const bgPage = localStorage.getItem('tc_bg_page') || '#eeefff';
            const radius = localStorage.getItem('tc_rounding') || '4px';
            const fontSize = localStorage.getItem('tc_font_size') || 'normal';
            const layout = localStorage.getItem('tc_layout') === 'horizontal' ? 'horizontal' : 'vertical';
            const font = localStorage.getItem('tc_font') || 'Inter';
            const lang = localStorage.getItem('twa_lang') || 'en';
            const darken = (hex) => `#${[0, 2, 4].map((offset) => Math.max(0, Math.round(parseInt(hex.slice(offset + 1, offset + 3), 16) * .7)).toString(16).padStart(2, '0')).join('')}`;

            root.lang = lang;
            root.dataset.lang = lang;
            root.dataset.theme = mode;
            root.dataset.bsTheme = mode;
            root.setAttribute('data-theme', mode);
            root.setAttribute('data-bs-theme', mode);
            root.style.colorScheme = mode;
            root.dataset.layout = layout;
            root.setAttribute('data-layout', layout);
            root.dataset.sidebar = localStorage.getItem('ks_col') === '1' ? 'collapsed' : 'expanded';

            root.dataset.fontSize = fontSize;
            root.setAttribute('data-font-size', fontSize);
            root.style.setProperty('--fs-base', fontSizes[fontSize] || '14px');

            if (mode === 'light' && validColor.test(bgPage)) {
                root.style.setProperty('--bg-page', bgPage);
                root.style.setProperty('--bs-body-bg', bgPage);
            }

            if (validColor.test(primary || '')) {
                root.style.setProperty('--pri', primary);
                root.style.setProperty('--pri-dark', darken(primary));
                root.style.setProperty('--pri-soft', `${primary}1a`);
                root.style.setProperty('--bs-primary-rgb', `${parseInt(primary.slice(1, 3), 16)}, ${parseInt(primary.slice(3, 5), 16)}, ${parseInt(primary.slice(5, 7), 16)}`);
            }
            if (/^\d+(\.\d+)?px$/.test(radius)) {
                const number = parseFloat(radius);
                root.style.setProperty('--rad-s', radius);
                root.style.setProperty('--rad', `${Math.max(5, number + 8)}px`);
            }
            if (lang === 'km') {
                root.style.setProperty('--font', "'Nokora', 'Noto Sans Khmer', sans-serif");
                root.style.setProperty('--font-kh', "'Nokora', 'Noto Sans Khmer', sans-serif");
                root.style.setProperty('--bs-body-font-family', "'Nokora', 'Noto Sans Khmer', sans-serif");
            } else if (fonts.includes(font)) {
                root.style.setProperty('--font', `'${font}', sans-serif`);
            }
        })();
    </script>
    @auth
        <script>
            window.AppUser = {
                id: {{ auth()->id() }},
                name: @json(auth()->user()->name),
                isSuperAdmin: {{ auth()->user()->hasRole('Super Admin') ? 'true' : 'false' }},
                permissions: @json(auth()->user()->hasRole('Super Admin') ? ['*'] : auth()->user()->getAllPermissions()->pluck('name')->values()->all())
            };
        </script>
    @else
        <script>
            window.AppUser = {
                id: null,
                isSuperAdmin: false,
                permissions: []
            };
        </script>
    @endauth
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
    @stack('styles')
</head>
<body>
    <div class="app-shell" id="app" data-app-shell>
        @include('components.layout.topbar')
        @include('components.layout.horizontal-nav', ['navigation' => $navigation])
        @include('components.layout.sidebar', ['navigation' => $navigation])
        <div class="app-sidebar-backdrop overlay" id="overlay" data-sidebar-backdrop></div>
        <main class="app-main">
            @if (isset($breadcrumb))
                <nav class="app-breadcrumb" aria-label="Breadcrumb">{{ $breadcrumb }}</nav>
            @endif
            {{ $slot }}
        </main>
        @include('components.layout.spotlight-modal')
        @include('components.layout.theme-drawer')
        <div id="ksToastStack" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1085;" aria-live="polite" aria-atomic="true"></div>
    </div>
    @stack('scripts')
</body>
</html>
