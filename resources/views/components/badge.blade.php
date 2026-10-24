@props([
    'variant' => 'soft',
    'tone' => 'primary',
    'pill' => false,
    'size' => 'md',
    'icon' => null,
])
@php
    if ($variant === 'dot') {
        $badgeClass = "mantis-badge-dot dot-{$tone}";
    } else {
        $badgeClass = "mantis-badge mantis-badge-{$variant}-{$tone}"
            . ($pill ? ' badge-pill' : '')
            . ($size !== 'md' ? ' badge-' . $size : '');
    }
@endphp

<span {{ $attributes->merge(['class' => $badgeClass]) }}>
    @if ($icon)
        <i data-lucide="{{ $icon }}" style="width: 12px; height: 12px;"></i>
    @endif
    <span>{{ $slot }}</span>
</span>
