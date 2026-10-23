@props(['label'])
<span {{ $attributes->merge(['class' => 'af-chip']) }}>{{ $label }}<button type="button" aria-label="Remove {{ $label }} filter" data-filter-chip-remove><i data-lucide="x"></i></button></span>
