<nav class="horizontal-nav" aria-label="Primary navigation">
    @foreach ($navigation as $group)
        @php
            $groupLabel = $group['label'] ?? '';
            $items = $group['items'] ?? [];
        @endphp

        @if ($groupLabel === 'Report')
            @php
                $reportCategories = collect($items)->map(function ($cat) {
                    $accessibleChildren = ! empty($cat['children'])
                        ? collect($cat['children'])->filter(fn ($child) => \App\Support\Navigation::userCanAccess($child))
                        : collect();

                    return [
                        'label' => $cat['label'] ?? '',
                        'icon' => $cat['icon'] ?? 'file-bar-chart',
                        'children' => $accessibleChildren,
                    ];
                })->filter(fn ($cat) => $cat['children']->isNotEmpty())->values();

                $hasReports = $reportCategories->isNotEmpty();
                $isReportActive = request()->routeIs('reports.*');
                $count = $reportCategories->count();
                $colClass = match ($count) {
                    1 => 'col-12',
                    2 => 'col-md-6',
                    3 => 'col-md-4',
                    default => 'col-md-3',
                };
            @endphp

            @if ($hasReports)
                <div class="horizontal-nav-item dropdown">
                    <button class="horizontal-nav-link {{ $isReportActive ? 'active' : '' }}"
                            type="button"
                            id="horizontal-report-dropdown"
                            data-bs-toggle="dropdown"
                            data-bs-display="static"
                            data-bs-auto-close="true"
                            aria-expanded="false">
                        <i data-lucide="file-bar-chart"></i>
                        <span>Report</span>
                        <i data-lucide="chevron-down" class="horizontal-nav-chevron"></i>
                    </button>
                    <div class="dropdown-menu horizontal-dropdown-menu shadow-lg border p-3" aria-labelledby="horizontal-report-dropdown">
                        <div class="row g-3">
                            @foreach ($reportCategories as $index => $category)
                                <div class="{{ $colClass }} {{ $index > 0 ? 'border-start ps-md-3' : '' }}">
                                    <div class="dropdown-header text-uppercase text-secondary fw-bold px-2 mb-1 d-flex align-items-center gap-2">
                                        <i data-lucide="{{ $category['icon'] }}" style="width: 14px; height: 14px;"></i>
                                        <span>{{ $category['label'] }}</span>
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach ($category['children'] as $child)
                                            @php
                                                $childActive = isset($child['route']) && \App\Support\Navigation::isRouteActive($child['route']);
                                            @endphp
                                            <a class="dropdown-item py-1.5 px-2 rounded-2 d-flex align-items-center gap-2 {{ $childActive ? 'active' : '' }}"
                                               href="{{ isset($child['route']) ? route($child['route']) : '#' }}">
                                                <i data-lucide="{{ $child['icon'] ?? 'file-text' }}" style="width: 14px; height: 14px;"></i>
                                                <span>{{ $child['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @else
            @foreach ($items as $item)
                @if (\App\Support\Navigation::userCanAccess($item))
                    @if (! empty($item['children']))
                        @php
                            $visibleSub = collect($item['children'])->filter(fn ($c) => \App\Support\Navigation::userCanAccess($c));
                            $isItemActive = $visibleSub->contains(fn ($c) => \App\Support\Navigation::isItemActive($c));
                        @endphp
                        @if ($visibleSub->isNotEmpty())
                            <div class="horizontal-nav-item dropdown">
                                <button class="horizontal-nav-link {{ $isItemActive ? 'active' : '' }}"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        data-bs-display="static"
                                        aria-expanded="false">
                                    <i data-lucide="{{ $item['icon'] ?? 'folder' }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                    <i data-lucide="chevron-down" class="horizontal-nav-chevron"></i>
                                </button>
                                <div class="dropdown-menu horizontal-dropdown-single-menu shadow border p-2">
                                    @foreach ($visibleSub as $c)
                                        <a class="dropdown-item py-1.5 px-2 rounded-2 d-flex align-items-center gap-2 {{ isset($c['route']) && \App\Support\Navigation::isRouteActive($c['route']) ? 'active' : '' }}"
                                           href="{{ isset($c['route']) ? route($c['route']) : '#' }}">
                                            <i data-lucide="{{ $c['icon'] ?? 'circle' }}" style="width: 14px; height: 14px;"></i>
                                            <span>{{ $c['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @elseif (isset($item['route']))
                        <a href="{{ route($item['route']) }}" class="horizontal-nav-link {{ \App\Support\Navigation::isRouteActive($item['route']) ? 'active' : '' }}">
                            <i data-lucide="{{ $item['icon'] ?? 'circle' }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endif
            @endforeach
        @endif
    @endforeach
</nav>
