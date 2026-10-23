@props(['variant' => 'primary', 'icon' => null, 'loading' => false, 'type' => 'button'])
<button type="{{ $type }}" @disabled($loading) {{ $attributes->merge(['class' => 'btn btn-'.$variant.($loading ? ' is-loading' : '')]) }}>
    @if ($loading)<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Loading…</span>@else @if ($icon)<i data-lucide="{{ $icon }}"></i>@endif <span>{{ $slot }}</span>@endif
</button>
