<nav class="horizontal-nav" aria-label="Primary navigation">
    <div class="horizontal-nav-container">
    @foreach ($navigation as $group)
        @php
            $groupLabel = $group['label'] ?? '';
            $groupIcon = $group['icon'] ?? null;
            $items = $group['items'] ?? [];

            // Filter items accessible by the current authenticated user
            $accessibleItems = collect($items)
                ->filter(fn ($item) => \App\Support\Navigation::userCanAccess($item))
                ->values();

            if ($accessibleItems->isEmpty()) {
                continue;
            }

            // Determine if any item has children (indicating a category / mega-dropdown layout)
            $hasSubcategories = $accessibleItems->contains(fn ($item) => ! empty($item['children']));

            // Check if any route in this group is currently active
            $isGroupActive = $accessibleItems->contains(function ($item) {
                if (! empty($item['children'])) {
                    return collect($item['children'])->contains(fn ($c) => ! empty($c['route']) && \App\Support\Navigation::isRouteActive($c['route']));
                }
                return ! empty($item['route']) && \App\Support\Navigation::isRouteActive($item['route']);
            });

            $groupSlug = \Illuminate\Support\Str::slug($groupLabel ?: 'group-'.$loop->index);
            $dropdownId = match ($groupSlug) {
                'report' => 'horizontal-report-dropdown',
                'master-data' => 'horizontal-master-data-dropdown',
                'administrator' => 'horizontal-admin-dropdown',
                default => 'horizontal-dropdown-'.$groupSlug,
            };
            $defaultIcon = $groupIcon ?: ($accessibleItems->first()['icon'] ?? 'folder');
        @endphp

        {{-- PATTERN 1: Single item with no subcategories (e.g. Workspace -> Dashboard) --}}
        @if ($accessibleItems->count() === 1 && empty($accessibleItems[0]['children']))
            @php
                $singleItem = $accessibleItems[0];
                $isSingleActive = ! empty($singleItem['route']) && \App\Support\Navigation::isRouteActive($singleItem['route']);
            @endphp
            <div class="horizontal-nav-item">
                <a class="horizontal-nav-link {{ $isSingleActive ? 'active' : '' }}"
                   href="{{ isset($singleItem['route']) ? route($singleItem['route']) : ($singleItem['url'] ?? '#') }}"
                   target="{{ $singleItem['target'] ?? '_self' }}">
                    <i data-lucide="{{ $singleItem['icon'] ?? $defaultIcon }}"></i>
                    <span>{{ $singleItem['label'] }}</span>
                </a>
            </div>

        {{-- PATTERN 2: Mega Dropdown with subcategories (e.g. Master Data -> SAP, Reports -> Purchase/Sales/Financial) --}}
        @elseif ($hasSubcategories)
            @php
                $categories = $accessibleItems->map(function ($cat) {
                    if (! empty($cat['children'])) {
                        $accessibleChildren = collect($cat['children'])
                            ->filter(fn ($child) => \App\Support\Navigation::userCanAccess($child))
                            ->values();

                        return [
                            'label' => $cat['label'] ?? '',
                            'icon' => $cat['icon'] ?? 'folder',
                            'children' => $accessibleChildren,
                        ];
                    }

                    return [
                        'label' => null,
                        'icon' => $cat['icon'] ?? 'circle',
                        'children' => collect([$cat]),
                    ];
                })->filter(fn ($cat) => $cat['children']->isNotEmpty())->values();

                $catCount = $categories->count();
                $colClass = match ($catCount) {
                    1 => 'col-12',
                    2 => 'col-md-6',
                    3 => 'col-md-4',
                    default => 'col-md-3',
                };
                $minWidth = match (true) {
                    $catCount >= 3 => 'min-width: 680px;',
                    $catCount === 2 => 'min-width: 520px;',
                    default => 'min-width: 480px;',
                };
            @endphp

            <div class="horizontal-nav-item dropdown">
                <button class="horizontal-nav-link {{ $isGroupActive ? 'active' : '' }}"
                        type="button"
                        id="{{ $dropdownId }}"
                        data-bs-toggle="dropdown"
                        data-bs-display="static"
                        data-bs-auto-close="true"
                        aria-expanded="false">
                    <i data-lucide="{{ $defaultIcon }}"></i>
                    <span>{{ $groupLabel }}</span>
                    <i data-lucide="chevron-down" class="horizontal-nav-chevron"></i>
                </button>
                <div class="dropdown-menu horizontal-dropdown-menu shadow-lg border p-3"
                     aria-labelledby="{{ $dropdownId }}"
                     style="{{ $minWidth }}">
                    @if ($catCount === 1)
                        {{-- Single subcategory container with items displayed in a responsive 2-column grid --}}
                        @php $singleCat = $categories->first(); @endphp
                        <div class="dropdown-header text-uppercase text-secondary fw-bold px-2 mb-2 d-flex align-items-center gap-2">
                            <i data-lucide="{{ $singleCat['icon'] }}" style="width: 14px; height: 14px;"></i>
                            <span>{{ $singleCat['label'] }}</span>
                        </div>
                        <div class="row g-2">
                            @foreach ($singleCat['children'] as $child)
                                @php
                                    $childActive = isset($child['route']) && \App\Support\Navigation::isRouteActive($child['route']);
                                @endphp
                                <div class="col-md-6">
                                    <a class="dropdown-item py-1.5 px-2 rounded-2 d-flex align-items-center gap-2 {{ $childActive ? 'active' : '' }}"
                                       href="{{ isset($child['route']) ? route($child['route']) : ($child['url'] ?? '#') }}"
                                       target="{{ $child['target'] ?? '_self' }}">
                                        <i data-lucide="{{ $child['icon'] ?? 'circle' }}" style="width: 14px; height: 14px;"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Multi-category multi-column layout --}}
                        <div class="row g-3">
                            @foreach ($categories as $index => $category)
                                <div class="{{ $colClass }} {{ $index > 0 ? 'border-start ps-md-3' : '' }}">
                                    @if (! empty($category['label']))
                                        <div class="dropdown-header text-uppercase text-secondary fw-bold px-2 mb-1 d-flex align-items-center gap-2">
                                            <i data-lucide="{{ $category['icon'] }}" style="width: 14px; height: 14px;"></i>
                                            <span>{{ $category['label'] }}</span>
                                        </div>
                                    @endif
                                    <div class="d-flex flex-column gap-1">
                                        @foreach ($category['children'] as $child)
                                            @php
                                                $childActive = isset($child['route']) && \App\Support\Navigation::isRouteActive($child['route']);
                                            @endphp
                                            <a class="dropdown-item py-1.5 px-2 rounded-2 d-flex align-items-center gap-2 {{ $childActive ? 'active' : '' }}"
                                               href="{{ isset($child['route']) ? route($child['route']) : ($child['url'] ?? '#') }}"
                                               target="{{ $child['target'] ?? '_self' }}">
                                                <i data-lucide="{{ $child['icon'] ?? 'file-text' }}" style="width: 14px; height: 14px;"></i>
                                                <span>{{ $child['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        {{-- PATTERN 3: Simple dropdown menu (e.g. Access Control, Administrator, Components, or any custom group) --}}
        @else
            <div class="horizontal-nav-item dropdown">
                <button class="horizontal-nav-link {{ $isGroupActive ? 'active' : '' }}"
                        type="button"
                        id="{{ $dropdownId }}"
                        data-bs-toggle="dropdown"
                        data-bs-display="static"
                        data-bs-auto-close="true"
                        aria-expanded="false">
                    <i data-lucide="{{ $defaultIcon }}"></i>
                    <span>{{ $groupLabel }}</span>
                    <i data-lucide="chevron-down" class="horizontal-nav-chevron"></i>
                </button>
                <div class="dropdown-menu horizontal-dropdown-single-menu shadow border p-2"
                     aria-labelledby="{{ $dropdownId }}">
                    @foreach ($accessibleItems as $item)
                        @php
                            $itemActive = isset($item['route']) && \App\Support\Navigation::isRouteActive($item['route']);
                        @endphp
                        <a class="dropdown-item py-1.5 px-2 rounded-2 d-flex align-items-center gap-2 {{ $itemActive ? 'active' : '' }}"
                           href="{{ isset($item['route']) ? route($item['route']) : ($item['url'] ?? '#') }}"
                           target="{{ $item['target'] ?? '_self' }}">
                            <i data-lucide="{{ $item['icon'] ?? 'circle' }}" style="width: 14px; height: 14px;"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
    </div>
</nav>
