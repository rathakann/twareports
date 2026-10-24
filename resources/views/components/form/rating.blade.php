@props([
    'name' => 'rating',
    'id' => null,
    'value' => 0,
    'max' => 5,
    'readonly' => false,
    'showValue' => true,
])
@php
    $fieldId = $id ?? $name;
    $currentVal = (float) old($name, $value);
@endphp

<div class="mantis-rating {{ $readonly ? 'is-readonly' : '' }}"
     x-data="{ rating: {{ $currentVal }}, hoverRating: 0 }"
     @mouseleave="hoverRating = 0">
    @if (!$readonly)
        <input type="hidden" name="{{ $name }}" id="{{ $fieldId }}" :value="rating">
    @endif

    @for ($i = 1; $i <= $max; $i++)
        <button type="button"
                class="mantis-rating-star"
                :class="{ 'is-filled': (hoverRating ? hoverRating >= {{ $i }} : rating >= {{ $i }}) }"
                @if(!$readonly)
                    @mouseenter="hoverRating = {{ $i }}"
                    @click="rating = {{ $i }}"
                @endif
                aria-label="Rate {{ $i }} of {{ $max }}">
            <svg viewBox="0 0 24 24">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
        </button>
    @endfor

    @if ($showValue)
        <span class="mantis-rating-value" x-text="(hoverRating || rating) + ' / {{ $max }}'">{{ $currentVal }} / {{ $max }}</span>
    @endif
</div>
