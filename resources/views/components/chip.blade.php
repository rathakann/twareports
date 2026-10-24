@props([
    'label' => null,
    'avatar' => null,
    'icon' => null,
    'tone' => null,
    'dismissible' => false,
    'clickable' => false,
])
@php
    $chipClass = 'mantis-chip'
        . ($clickable ? ' is-clickable' : '')
        . ($tone ? ' mantis-chip-' . $tone : '');
@endphp

<div {{ $attributes->merge(['class' => $chipClass]) }}
     @if ($dismissible) x-data="{ dismissed: false }" x-show="!dismissed" x-transition @endif>
    @if ($avatar)
        <x-avatar :initials="$avatar" size="xs" />
    @elseif ($icon)
        <i data-lucide="{{ $icon }}" style="width: 14px; height: 14px;"></i>
    @endif

    <span>{{ $label ?? $slot }}</span>

    @if ($dismissible)
        <button type="button" class="mantis-chip-close" @click.stop="dismissed = true" aria-label="Remove">
            <i data-lucide="x"></i>
        </button>
    @endif
</div>
