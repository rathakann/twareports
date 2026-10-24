@props([
    'type' => 'text',
    'width' => null,
    'height' => null,
    'lines' => 1,
])
@php
    $style = '';
    if ($width) $style .= "width: {$width}; ";
    if ($height) $style .= "height: {$height}; ";
@endphp

@if ($type === 'card')
    <div class="card p-3 border-0 shadow-sm" style="width: 100%;">
        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="mantis-skeleton skeleton-avatar"></span>
            <div class="w-100">
                <span class="mantis-skeleton skeleton-text" style="width: 40%;"></span>
                <span class="mantis-skeleton skeleton-text" style="width: 70%;"></span>
            </div>
        </div>
        <span class="mantis-skeleton skeleton-text"></span>
        <span class="mantis-skeleton skeleton-text"></span>
        <span class="mantis-skeleton skeleton-text" style="width: 80%;"></span>
    </div>
@else
    @for ($i = 0; $i < $lines; $i++)
        <span {{ $attributes->merge(['class' => 'mantis-skeleton skeleton-' . $type]) }}
              style="{{ $style }} {{ $lines > 1 && $i === $lines - 1 ? 'width: 70%;' : '' }}"></span>
    @endfor
@endif
