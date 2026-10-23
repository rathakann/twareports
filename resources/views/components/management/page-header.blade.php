@props(['title', 'description' => null, 'icon' => null, 'section' => null])
<header {{ $attributes->merge(['class' => 'page-header mb-2']) }}>
    <div class="page-header-main">
        @if ($icon)<span class="page-header-icon"><i data-lucide="{{ $icon }}"></i></span>@endif
        <div>
            <h1>{{ $title }}</h1>
            @if ($description)<p>{{ $description }}</p>@endif
        </div>
    </div>
    @if (isset($actions))<div class="page-header-actions">{{ $actions }}</div>@endif
</header>
