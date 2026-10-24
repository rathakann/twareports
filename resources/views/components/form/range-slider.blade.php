@props([
    'name',
    'id' => null,
    'label' => null,
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => 50,
    'unit' => '',
    'tone' => 'primary',
    'disabled' => false,
])
@php
    $fieldId = $id ?? $name;
    $initialValue = (float) old($name, $value);
@endphp

<div class="mantis-slider-container" x-data="{ val: {{ $initialValue }} }">
    @if ($label)
        <div class="mantis-slider-header">
            <label for="{{ $fieldId }}" class="mantis-slider-label">{{ $label }}</label>
            <span class="mantis-slider-value" x-text="val + '{{ $unit }}'">{{ $initialValue }}{{ $unit }}</span>
        </div>
    @endif
    <input type="range"
           id="{{ $fieldId }}"
           name="{{ $name }}"
           min="{{ $min }}"
           max="{{ $max }}"
           step="{{ $step }}"
           x-model="val"
           @disabled($disabled)
           {{ $attributes->merge(['class' => 'mantis-range-input']) }}>
</div>
