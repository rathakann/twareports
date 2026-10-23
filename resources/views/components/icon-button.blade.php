@props(['icon', 'label', 'variant' => 'outline-secondary'])
<button type="button" {{ $attributes->merge(['class' => 'btn btn-ic btn-'.$variant]) }} aria-label="{{ $label }}" title="{{ $label }}"><i data-lucide="{{ $icon }}"></i></button>
