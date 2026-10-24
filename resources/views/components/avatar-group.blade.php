@props([
    'max' => null,
    'size' => 'md',
    'excess' => null,
])

<div {{ $attributes->merge(['class' => 'mantis-avatar-group size-' . $size]) }}>
    {{ $slot }}
    @if ($excess)
        <div class="mantis-avatar shape-circle size-{{ $size }} mantis-avatar-more" title="+{{ $excess }} more">
            <span>+{{ $excess }}</span>
        </div>
    @endif
</div>
