@props(['label' => 'More actions', 'icon' => 'ellipsis'])
<div class="dropdown"><button {{ $attributes->merge(['class' => 'btn btn-ic btn-outline-secondary']) }} type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ $label }}" title="{{ $label }}"><i data-lucide="{{ $icon }}"></i></button><div class="dropdown-menu dropdown-menu-end">{{ $slot }}</div></div>
