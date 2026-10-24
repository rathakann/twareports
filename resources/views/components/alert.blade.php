@props([
    'tone' => 'success',
    'title' => null,
    'icon' => null,
    'dismissible' => true,
    'variant' => 'soft',
])
@php
    $defaultIcons = [
        'success' => 'circle-check',
        'danger' => 'circle-alert',
        'warning' => 'triangle-alert',
        'info' => 'info',
        'primary' => 'bell',
        'secondary' => 'help-circle',
    ];
    $iconName = $icon ?? ($defaultIcons[$tone] ?? 'info');
    
    $variantClass = match($variant) {
        'outline' => "alert-outline-{$tone}",
        'solid' => "bg-{$tone} text-white border-0",
        'accent-left' => "border-0 border-start border-4 border-{$tone} bg-{$tone}-subtle",
        default => "alert-{$tone}",
    };
@endphp

<div {{ $attributes->merge(['class' => "alert {$variantClass}" . ($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
    <div class="d-flex align-items-start gap-2.5">
        @if ($iconName)
            <div class="alert-icon flex-shrink-0 mt-0.5">
                <i data-lucide="{{ $iconName }}" style="width: 18px; height: 18px;"></i>
            </div>
        @endif
        <div class="alert-content flex-grow-1">
            @if ($title)
                <h5 class="alert-heading fs-6 fw-bold mb-1">{{ $title }}</h5>
            @endif
            <div>{{ $slot }}</div>
            @if (isset($action))
                <div class="mt-2">{{ $action }}</div>
            @endif
        </div>
        @if ($dismissible)
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @endif
    </div>
</div>
