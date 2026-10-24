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
        <section class="theme-setting"><p>Primary color</p><div class="theme-colors">@foreach (['blue', 'sky', 'purple', 'emerald', 'royal', 'orange', 'cyan', 'mint', 'coral'] as $color)<button type="button" class="theme-color theme-color-{{ $color }}" data-primary-color="{{ $color }}" aria-label="Use {{ $color }} primary color" aria-pressed="false"></button>@endforeach</div><label class="theme-field-label mt-3" for="themePrimary">Custom hexadecimal color</label><div class="d-flex gap-2"><input id="themePrimary" class="form-control form-control-color" type="color" value="#1b4fe0" data-primary-color-input><input class="form-control" type="text" value="#1b4fe0" maxlength="7" pattern="#[0-9A-Fa-f]{6}" data-primary-color-hex aria-label="Custom hexadecimal color"></div></section>
        <section class="theme-setting">
            <p>Page background</p>
            <div class="theme-bg-presets">
                @php
                    $bgPresets = [
                        'indigo' => ['color' => '#eeefff', 'name' => 'Soft Indigo (Default)'],
                        'slate' => ['color' => '#f4f6fb', 'name' => 'Cool Slate'],
                        'zinc' => ['color' => '#f8fafc', 'name' => 'Clean Zinc'],
                        'pearl' => ['color' => '#fdfbf7', 'name' => 'Warm Pearl'],
                        'sky' => ['color' => '#f0f7ff', 'name' => 'Soft Sky'],
                        'mint' => ['color' => '#f0fdf4', 'name' => 'Soft Mint'],
                        'violet' => ['color' => '#f5f3ff', 'name' => 'Soft Violet'],
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
                <input id="themeBgColor" class="form-control form-control-color" type="color" value="#eeefff" data-bg-color-input aria-label="Color picker for custom background">
                <input class="form-control" type="text" value="#eeefff" maxlength="7" pattern="#[0-9A-Fa-f]{6}" data-bg-color-hex aria-label="Custom background hexadecimal color">
            </div>
        </section>
        <section class="theme-setting"><label class="theme-field-label" for="themeFont">Typography</label><select id="themeFont" class="form-select js-select2" data-theme-font><option value="Inter" data-icon="type">Inter</option><option value="Roboto" data-icon="case-sensitive">Roboto</option><option value="Poppins" data-icon="heading">Poppins</option><option value="Public Sans" data-icon="baseline">Public Sans</option></select><small class="theme-help">Khmer content uses Nokora, Google Sans, Noto Sans, sans-serif.</small></section>
        <section class="theme-setting"><p>Font size & table density</p><div class="theme-choice-grid" style="grid-template-columns: repeat(3, 1fr);"><button type="button" data-font-size="small" aria-pressed="false"><i data-lucide="minimize-2"></i>Small</button><button type="button" data-font-size="normal" aria-pressed="false"><i data-lucide="type"></i>Normal</button><button type="button" data-font-size="large" aria-pressed="false"><i data-lucide="maximize-2"></i>Large</button></div></section>
        <section class="theme-setting">
            <p>Corner rounding</p>
            <div class="theme-choice-grid mb-2" style="grid-template-columns: repeat(4, 1fr);">
                <button type="button" data-theme-radius="0px" aria-pressed="false">
                    <span class="d-block mx-auto mb-1 border border-2 rounded-0" style="width: 20px; height: 16px; border-color: currentColor;"></span>
                    0px
                </button>
                <button type="button" data-theme-radius="4px" aria-pressed="false">
                    <span class="d-block mx-auto mb-1 border border-2 rounded-1" style="width: 20px; height: 16px; border-color: currentColor;"></span>
                    4px
                </button>
                <button type="button" data-theme-radius="8px" aria-pressed="false">
                    <span class="d-block mx-auto mb-1 border border-2 rounded-2" style="width: 20px; height: 16px; border-color: currentColor;"></span>
                    8px
                </button>
                <button type="button" data-theme-radius="12px" aria-pressed="false">
                    <span class="d-block mx-auto mb-1 border border-2 rounded-3" style="width: 20px; height: 16px; border-color: currentColor;"></span>
                    12px
                </button>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2">
                <input type="range" class="form-range flex-grow-1" min="0" max="20" step="2" id="themeRadiusRange" data-theme-radius-range value="8" aria-label="Corner rounding in pixels">
                <span class="badge bg-light text-secondary border px-2 py-1 font-monospace" id="themeRadiusBadge" style="min-width: 42px;">8px</span>
            </div>
        </section>
        <button class="btn btn-outline-secondary w-100 mt-3 mb-2" type="button" data-theme-reset><i data-lucide="rotate-ccw"></i>Restore default settings</button>
    </div>
</aside>
<div class="theme-drawer-backdrop" data-theme-drawer-backdrop></div>
