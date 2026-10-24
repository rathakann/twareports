@props([
    'src' => null,
    'alt' => 'Avatar',
    'initials' => null,
    'icon' => null,
    'size' => 'md',
    'shape' => 'circle',
    'status' => null,
    'tone' => null,
    'badge' => null,
])
@php
    $sizeClass = 'size-' . $size;
    $shapeClass = 'shape-' . $shape;
    
    // Choose deterministic tone if not explicitly provided and initials are given
    if (!$tone && $initials) {
        $tones = ['primary', 'success', 'warning', 'info', 'purple'];
        $tone = $tones[crc32($initials) % count($tones)];
    }
    $toneClass = $tone ? 'mantis-avatar-' . $tone : 'mantis-avatar-primary';
@endphp

<div {{ $attributes->merge(['class' => "mantis-avatar {$shapeClass} {$sizeClass} {$toneClass}"]) }} title="{{ $alt }}">
    @if ($src)
        <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy">
    @elseif ($initials)
        <span>{{ strtoupper(substr($initials, 0, 2)) }}</span>
    @elseif ($icon)
        <i data-lucide="{{ $icon }}"></i>
    @else
        <i data-lucide="user"></i>
    @endif

    @if ($status)
        <span class="mantis-avatar-status status-{{ $status }}" aria-label="Status: {{ $status }}"></span>
    @endif

    @if ($badge !== null)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem;">
            {{ $badge }}
        </span>
    @endif
</div>
