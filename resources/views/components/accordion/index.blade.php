@props(['id' => 'accordion-' . uniqid()])

<div {{ $attributes->merge(['class' => 'mantis-accordion', 'id' => $id]) }}>
    {{ $slot }}
</div>
