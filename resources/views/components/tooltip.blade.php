@props(['text'])
<span {{ $attributes->merge(['data-bs-toggle' => 'tooltip', 'data-bs-title' => $text]) }}>{{ $slot }}</span>
