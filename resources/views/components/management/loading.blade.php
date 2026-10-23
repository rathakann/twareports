@props(['label' => 'Loading'])
<div {{ $attributes->merge(['class' => 'loading-state']) }} role="status"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>{{ $label }}…</span></div>
