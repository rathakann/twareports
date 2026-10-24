@if (! empty($item) && \App\Support\Navigation::userCanAccess($item))
    @php
        $hasChildren = ! empty($item['children']);
        $isChildActive = $hasChildren && \App\Support\Navigation::isChildActive($item);
        $isSelfActive = ! empty($item['route']) && \App\Support\Navigation::isRouteActive($item['route']);
        $isActive = $isSelfActive || $isChildActive;
        $submenuId = 'nav-submenu-'.$depth.'-'.\Illuminate\Support\Str::slug($item['label']);
    @endphp
    <div class="sidebar-item {{ $hasChildren ? 'has-children' : '' }} {{ $isActive ? 'is-active is-expanded' : '' }}" data-nav-item>
        @if ($hasChildren)
            <button class="sidebar-link" type="button" data-nav-toggle aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="{{ $submenuId }}">
                <i data-lucide="{{ $item['icon'] ?? 'folder' }}"></i><span>{{ $item['label'] }}</span><i class="sidebar-chevron" data-lucide="chevron-right"></i>
            </button>
            <div class="nav-submenu {{ $isActive ? 'is-open' : '' }}" id="{{ $submenuId }}">
                <div class="nav-submenu-inner">
                    @if ($depth === 0)
                        <div class="nav-submenu-header">
                            <i data-lucide="{{ $item['icon'] ?? 'folder' }}" style="width: 14px; height: 14px;"></i>
                            <span>{{ $item['label'] }}</span>
                        </div>
                    @endif
                    @foreach ($item['children'] as $child)
                        @include('components.layout.nav-item', ['item' => $child, 'depth' => $depth + 1])
                    @endforeach
                </div>
            </div>
        @else
            <a class="sidebar-link {{ $isActive ? 'active' : '' }}" href="{{ isset($item['route']) ? route($item['route']) : '#' }}" @if ($isActive) aria-current="page" @endif>
                <i data-lucide="{{ $item['icon'] ?? 'circle' }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endif
    </div>
@endif
