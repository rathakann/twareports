@props([
    'value' => 0,
    'max' => 100,
    'tone' => 'primary',
    'size' => 'md',
    'striped' => false,
    'animated' => false,
    'label' => null,
    'showValue' => false,
])
@php
    $percentage = min(100, max(0, ($value / ($max ?: 100)) * 100));
    $sizeClass = 'size-' . $size;
    $barClass = 'mantis-progress-bar bg-' . $tone
        . ($striped ? ' progress-bar-striped' : '')
        . ($animated ? ' progress-bar-animated' : '');
@endphp

<div class="mantis-progress-wrap">
    @if ($label || $showValue)
        <div class="mantis-progress-label">
            @if ($label)<span>{{ $label }}</span>@endif
            @if ($showValue)<span>{{ round($percentage) }}%</span>@endif
        </div>
    @endif
    <div {{ $attributes->merge(['class' => 'mantis-progress ' . $sizeClass]) }} role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="{{ $max }}">
        <div class="{{ $barClass }}" style="width: {{ $percentage }}%">
            @if ($size === 'lg' || $size === 'xl')
                @if ($showValue && !$label) {{ round($percentage) }}% @endif
            @endif
        </div>
    </div>
</div>
