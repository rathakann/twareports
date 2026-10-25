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
            const radius = localStorage.getItem('tc_rounding') || '8px';
            const fontSize = localStorage.getItem('tc_font_size') || 'normal';
            const layout = localStorage.getItem('tc_layout') === 'horizontal' ? 'horizontal' : 'vertical';
            const container = localStorage.getItem('tc_container') === 'container' ? 'container' : 'fluid';
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
            root.dataset.container = container;
            root.setAttribute('data-container', container);
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
            const tableStriped = localStorage.getItem('tc_table_striped') || 'striped';
            const tableHover = localStorage.getItem('tc_table_hover') || 'hover';
            const tableBorder = localStorage.getItem('tc_table_border') || 'horizontal';
            const tableHead = localStorage.getItem('tc_table_head') || 'subtle';
            root.dataset.tableStriped = tableStriped;
            root.setAttribute('data-table-striped', tableStriped);
            root.dataset.tableHover = tableHover;
            root.setAttribute('data-table-hover', tableHover);
            root.dataset.tableBorder = tableBorder;
            root.setAttribute('data-table-border', tableBorder);
            root.dataset.tableHead = tableHead;
            root.setAttribute('data-table-head', tableHead);

            root.dataset.radius = radius;
            root.setAttribute('data-radius', radius);
            if (/^\d+(\.\d+)?px$/.test(radius)) {
                const radNum = parseFloat(radius) || 0;
                const radSm = `${Math.round(radNum * 0.75)}px`;
                const radLg = `${Math.max(radNum, Math.round(radNum * 1.5 + 2))}px`;
                const radXl = `${Math.max(radNum, Math.round(radNum * 2 + 4))}px`;
                root.style.setProperty('--rad-s', radNum === 0 ? '0px' : radSm);
                root.style.setProperty('--rad', radNum === 0 ? '0px' : radLg);
                root.style.setProperty('--bs-border-radius', radius);
                root.style.setProperty('--bs-border-radius-sm', radNum === 0 ? '0px' : radSm);
                root.style.setProperty('--bs-border-radius-lg', radNum === 0 ? '0px' : radLg);
                root.style.setProperty('--bs-border-radius-xl', radNum === 0 ? '0px' : radXl);
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
    @if (session('success') || session('status') || session('error') || session('danger'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toast = window.Toast || (window.Swal ? {
                    fire: (opts) => window.Swal.fire({
                        toast: true,
                        position: localStorage.getItem('tc_toast_position') || 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        ...opts
                    })
                } : null);

                if (toast) {
                    @if (session('success'))
                        toast.fire({ icon: 'success', title: @json(session('success')) });
                    @elseif (session('status'))
                        toast.fire({ icon: 'info', title: @json(session('status')) });
                    @elseif (session('error') || session('danger'))
                        toast.fire({ icon: 'error', title: @json(session('error') ?? session('danger')) });
                    @endif
                }
            });
        </script>
    @endif
    @stack('scripts')
</body>
</html>
