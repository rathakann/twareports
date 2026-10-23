@props(['searchPlaceholder' => 'Search', 'filterCount' => 0])

<div {{ $attributes->merge(['class' => 't-hdr']) }}>
    <div class="t-hdr-search input-icon-left"><i class="input-icon-addon" data-lucide="search"></i><input class="form-control" type="search" data-list-search placeholder="{{ $searchPlaceholder }}"></div>
    <div class="t-hdr-actions">
        @if (isset($filters))
            <button class="btn btn-outline-secondary" type="button" data-filter-panel-toggle><i data-lucide="filter"></i> Filters @if ($filterCount)<span class="filter-count">{{ $filterCount }}</span>@endif</button>
        @endif
        {{ $actions ?? '' }}
    </div>
</div>
@if (isset($filters))
    <div class="filter-panel" data-filter-panel hidden>{{ $filters }}</div>
@endif
@if (isset($chips))
    <div class="filter-chips">{{ $chips }}</div>
@endif
