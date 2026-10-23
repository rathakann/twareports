@if (! empty($item) && \App\Support\Navigation::userCanAccess($item))
    @php
        $hasChildren = ! empty($item['children']);
        $isChildActive = false;
        if ($hasChildren) {
            $isChildActive = collect($item['children'])->contains(function ($child) {
                if (isset($child['route']) && request()->routeIs($child['route'])) {
                    return true;
                }
                if (! empty($child['children'])) {
                    return collect($child['children'])->contains(fn ($sub) => isset($sub['route']) && request()->routeIs($sub['route']));
                }
                return false;
            });
        }
        $isActive = (isset($item['route']) && request()->routeIs($item['route'])) || $isChildActive;
        $submenuId = 'nav-submenu-'.$depth.'-'.\Illuminate\Support\Str::slug($item['label']);
    @endphp
    <div class="sidebar-item {{ $hasChildren ? 'has-children' : '' }} {{ $isActive ? 'is-active is-expanded' : '' }}" data-nav-item>
        @if ($hasChildren)
            <button class="sidebar-link" type="button" data-nav-toggle aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="{{ $submenuId }}">
                <i data-lucide="{{ $item['icon'] ?? 'folder' }}"></i><span>{{ $item['label'] }}</span><i class="sidebar-chevron" data-lucide="chevron-right"></i>
            </button>
            <div class="nav-submenu {{ $isActive ? 'is-open' : '' }}" id="{{ $submenuId }}">
                <div class="nav-submenu-inner">
                    @foreach ($item['children'] as $child)
                        @include('components.layout.nav-item', ['item' => $child, 'depth' => $depth + 1])
                    @endforeach
                </div>
            </div>
        @else
            <a class="sidebar-link" href="{{ isset($item['route']) ? route($item['route']) : '#' }}" @if ($isActive) aria-current="page" @endif>
                <i data-lucide="{{ $item['icon'] ?? 'circle' }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endif
    </div>
@endif
