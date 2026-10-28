<aside class="theme-drawer" id="themeDrawer" data-theme-drawer aria-label="Theme settings" aria-hidden="true">
    <header class="theme-drawer-header"><div><p class="mb-0 fw-semibold">Theme settings</p><small class="text-muted">Preferences are saved in this browser.</small></div><button class="btn-close" type="button" data-theme-drawer-close aria-label="Close theme settings"></button></header>
    <div class="theme-drawer-body">
        <section class="theme-setting"><p>Appearance</p><div class="theme-choice-grid"><button type="button" data-theme-mode="light" aria-pressed="false"><i data-lucide="sun"></i>Light</button><button type="button" data-theme-mode="dark" aria-pressed="false"><i data-lucide="moon"></i>Dark</button></div></section>
        <section class="theme-setting">
            <p>Navigation layout</p>
            <div class="theme-choice-grid">
                <button type="button" data-layout="vertical" aria-pressed="false"><i data-lucide="panel-left"></i>Vertical</button>
                <button type="button" data-layout="horizontal" aria-pressed="false"><i data-lucide="menu"></i>Horizontal</button>
            </div>
        </section>
        <section class="theme-setting">
            <p>Layout width</p>
            <div class="theme-choice-grid">
                <button type="button" data-container="fluid" aria-pressed="false">
                    <i data-lucide="maximize-2"></i>Fluid
                </button>
                <button type="button" data-container="container" aria-pressed="false">
                    <i data-lucide="minimize-2"></i>Container
                </button>
            </div>
        </section>
        <section class="theme-setting"><p>Primary color</p><div class="theme-colors">@foreach (['blue', 'sky', 'purple', 'emerald', 'royal', 'orange', 'cyan', 'mint', 'coral'] as $color)<button type="button" class="theme-color theme-color-{{ $color }}" data-primary-color="{{ $color }}" aria-label="Use {{ $color }} primary color" aria-pressed="false"></button>@endforeach</div><label class="theme-field-label mt-3" for="themePrimary">Custom hexadecimal color</label><div class="d-flex gap-2"><input id="themePrimary" class="form-control form-control-color" type="color" value="#1ea754" data-primary-color-input><input class="form-control" type="text" value="#1ea754" maxlength="7" pattern="#[0-9A-Fa-f]{6}" data-primary-color-hex aria-label="Custom hexadecimal color"></div></section>
        <section class="theme-setting">
            <p>Page background</p>
            <div class="theme-bg-presets">
                @php
                    $bgPresets = [
                        'gray' => ['color' => '#f3f4f6', 'name' => 'Gray 100 (Default)'],
                        'zinc' => ['color' => '#f8fafc', 'name' => 'Clean Zinc'],
                        'slate' => ['color' => '#f4f6fb', 'name' => 'Cool Slate'],
                        'sky' => ['color' => '#f0f7ff', 'name' => 'Soft Sky'],
                        'mint' => ['color' => '#f0fdf4', 'name' => 'Soft Mint'],
                        'pearl' => ['color' => '#fdfbf7', 'name' => 'Warm Pearl'],
                        'indigo' => ['color' => '#eeefff', 'name' => 'Soft Indigo'],
                        'white' => ['color' => '#ffffff', 'name' => 'Pure White'],
                    ];
                @endphp
                @foreach ($bgPresets as $preset)
                    <button type="button"
                            class="theme-bg-preset"
                            style="background-color: {{ $preset['color'] }}; border: 1.5px solid var(--bdr2, #cbd5e1);"
                            data-bg-preset="{{ $preset['color'] }}"
                            title="{{ $preset['name'] }}"
                            aria-label="{{ $preset['name'] }}"
                            aria-pressed="false">
                    </button>
                @endforeach
            </div>
            <label class="theme-field-label mt-3" for="themeBgColor">Custom background color</label>
            <div class="d-flex gap-2">
                <input id="themeBgColor" class="form-control form-control-color" type="color" value="#f3f4f6" data-bg-color-input aria-label="Color picker for custom background">
                <input class="form-control" type="text" value="#f3f4f6" maxlength="7" pattern="#[0-9A-Fa-f]{6}" data-bg-color-hex aria-label="Custom background hexadecimal color">
            </div>
        </section>
        <section class="theme-setting"><label class="theme-field-label" for="themeFont">Typography</label><select id="themeFont" class="form-select js-select2" data-theme-font><option value="Inter" data-icon="type">Inter</option><option value="Roboto" data-icon="case-sensitive">Roboto</option><option value="Poppins" data-icon="heading">Poppins</option><option value="Public Sans" data-icon="baseline">Public Sans</option></select><small class="theme-help">Khmer content uses Nokora, Google Sans, Noto Sans, sans-serif.</small></section>
        <section class="theme-setting"><p>Font size & table density</p><div class="theme-choice-grid" style="grid-template-columns: repeat(3, 1fr);"><button type="button" data-font-size="small" aria-pressed="false"><i data-lucide="minimize-2"></i>Small</button><button type="button" data-font-size="normal" aria-pressed="false"><i data-lucide="type"></i>Normal</button><button type="button" data-font-size="large" aria-pressed="false"><i data-lucide="maximize-2"></i>Large</button></div></section>
        <section class="theme-setting">
            <p>DataTable & table style</p>

            <label class="theme-field-label mt-1 mb-2" style="font-size: 11px;">Table style</label>
            <div class="theme-choice-grid mb-3" style="grid-template-columns: repeat(2, 1fr);">
                <button type="button" data-table-style="nowrap-grid" data-table-preset="nowrap-bordered-hover" aria-pressed="true" title="nowrap table-bordered table table-hover">
                    <i data-lucide="table-properties"></i>Nowrap Grid
                    <small class="d-block text-muted" style="font-size: 9px; margin-top: 1px;">nowrap bordered hover</small>
                </button>
                <button type="button" data-table-style="standard" data-table-preset="standard" aria-pressed="false" title="Standard divided rows with hover">
                    <i data-lucide="minus"></i>Standard
                    <small class="d-block text-muted" style="font-size: 9px; margin-top: 1px;">divided hover</small>
                </button>
                <button type="button" data-table-style="striped" aria-pressed="false" title="Alternating zebra striped rows">
                    <i data-lucide="rows-3"></i>Zebra Striped
                    <small class="d-block text-muted" style="font-size: 9px; margin-top: 1px;">striped hover</small>
                </button>
                <button type="button" data-table-style="borderless" aria-pressed="false" title="Borderless minimalist table">
                    <i data-lucide="align-justify"></i>Borderless
                    <small class="d-block text-muted" style="font-size: 9px; margin-top: 1px;">clean minimal</small>
                </button>
            </div>

            <label class="theme-field-label mb-2" style="font-size: 11px;">Header style</label>
            <div class="theme-choice-grid" style="grid-template-columns: repeat(3, 1fr);">
                <button type="button" data-table-head="subtle" aria-pressed="true" title="Soft neutral header background">
                    <i data-lucide="panel-top"></i>Subtle
                </button>
                <button type="button" data-table-head="primary" aria-pressed="false" title="Theme primary color tinted header">
                    <i data-lucide="sparkles"></i>Primary
                </button>
                <button type="button" data-table-head="clean" aria-pressed="false" title="Clean background matching card surface">
                    <i data-lucide="table"></i>Clean
                </button>
            </div>
            <small class="theme-help">Customizes table rows, borders, hover, and header appearance across all modules.</small>
        </section>
        <section class="theme-setting">
            <p>Corner rounding</p>
            <div class="theme-choice-grid mb-2" style="grid-template-columns: repeat(4, 1fr);">
                <button type="button" data-theme-radius="0px" aria-pressed="true">
                    <span class="d-block mx-auto mb-1 border border-2" style="width: 20px; height: 16px; border-color: currentColor; border-radius: 0px !important;"></span>
                    0px
                </button>
                <button type="button" data-theme-radius="4px" aria-pressed="false">
                    <span class="d-block mx-auto mb-1 border border-2" style="width: 20px; height: 16px; border-color: currentColor; border-radius: 4px !important;"></span>
                    4px
                </button>
                <button type="button" data-theme-radius="8px" aria-pressed="false">
                    <span class="d-block mx-auto mb-1 border border-2" style="width: 20px; height: 16px; border-color: currentColor; border-radius: 8px !important;"></span>
                    8px
                </button>
                <button type="button" data-theme-radius="12px" aria-pressed="false">
                    <span class="d-block mx-auto mb-1 border border-2" style="width: 20px; height: 16px; border-color: currentColor; border-radius: 12px !important;"></span>
                    12px
                </button>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2">
                <input type="range" class="form-range flex-grow-1" min="0" max="20" step="2" id="themeRadiusRange" data-theme-radius-range value="0" aria-label="Corner rounding in pixels">
                <span class="badge bg-light text-secondary border px-2 py-1 font-monospace" id="themeRadiusBadge" style="min-width: 42px;">0px</span>
            </div>
        </section>
        <section class="theme-setting">
            <p>Toast position</p>
            <div class="theme-choice-grid" style="grid-template-columns: repeat(3, 1fr);">
                <button type="button" data-toast-position="top-start" aria-pressed="false" title="Top Left">
                    <i data-lucide="arrow-up-left"></i>Top Left
                </button>
                <button type="button" data-toast-position="top" aria-pressed="false" title="Top Center">
                    <i data-lucide="arrow-up"></i>Top Center
                </button>
                <button type="button" data-toast-position="top-end" aria-pressed="false" title="Top Right">
                    <i data-lucide="arrow-up-right"></i>Top Right
                </button>
                <button type="button" data-toast-position="bottom-start" aria-pressed="false" title="Bottom Left">
                    <i data-lucide="arrow-down-left"></i>Bottom Left
                </button>
                <button type="button" data-toast-position="bottom" aria-pressed="false" title="Bottom Center">
                    <i data-lucide="arrow-down"></i>Bottom Center
                </button>
                <button type="button" data-toast-position="bottom-end" aria-pressed="false" title="Bottom Right">
                    <i data-lucide="arrow-down-right"></i>Bottom Right
                </button>
            </div>
            <small class="theme-help">Alert notifications and toasts will appear at this screen location.</small>
        </section>
        <button class="btn btn-outline-secondary w-100 mt-3 mb-3" type="button" data-theme-reset><i data-lucide="rotate-ccw"></i>Restore default settings</button>
    </div>
</aside>
<div class="theme-drawer-backdrop" data-theme-drawer-backdrop></div>
