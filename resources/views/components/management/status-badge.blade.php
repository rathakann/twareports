@props(['status', 'tone' => 'success'])
<span {{ $attributes->merge(['class' => 'badge status-badge badge-soft-'.$tone]) }}>{{ $status }}</span>
