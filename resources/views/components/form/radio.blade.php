@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'size' => 'md',
    'tone' => 'primary',
    'description' => null,
    'inline' => false,
    'card' => false,
    'icon' => null,
    'error' => null,
])
@php
    $fieldId = $id ?? ($name . '_' . \Illuminate\Support\Str::slug((string) $value));
    $isChecked = old($name, $checked) == $value;
    $sizeClass = match($size) {
        'sm' => 'mantis-radio-sm',
        'lg' => 'mantis-radio-lg',
        default => '',
    };
    $toneClass = 'mantis-radio-' . $tone;
@endphp

@if ($card)
    <label class="mantis-card-radio {{ $isChecked ? 'is-selected' : '' }} {{ $disabled ? 'is-disabled' : '' }} {{ $error ? 'is-invalid' : '' }}" for="{{ $fieldId }}">
        <input type="radio"
               id="{{ $fieldId }}"
               name="{{ $name }}"
               value="{{ $value }}"
               class="form-check-input mantis-card-radio-input mantis-radio-input {{ $toneClass }}"
               @checked($isChecked)
               @disabled($disabled)
               {{ $attributes }}>
        <div class="mantis-card-radio-content">
            @if ($icon)
                <div class="mantis-card-radio-icon">
                    <i data-lucide="{{ $icon }}"></i>
                </div>
            @endif
            <div class="mantis-card-radio-body">
                <span class="mantis-card-radio-title">{{ $label ?? $slot }}</span>
                @if ($description)
                    <span class="mantis-card-radio-desc">{{ $description }}</span>
                @endif
            </div>
        </div>
    </label>
@else
    <div class="mantis-radio-group {{ $sizeClass }} {{ $toneClass }} {{ $inline ? 'form-check-inline' : '' }} {{ $error ? 'has-error' : '' }}">
        <div class="mantis-radio-wrapper">
            <input type="radio"
                   id="{{ $fieldId }}"
                   name="{{ $name }}"
                   value="{{ $value }}"
                   class="form-check-input mantis-radio-input {{ $error ? 'is-invalid' : '' }}"
                   @checked($isChecked)
                   @disabled($disabled)
                   {{ $attributes }}>
            @if ($label || !$slot->isEmpty() || $description)
                <div class="mantis-radio-label-wrap">
                    <label class="form-check-label mantis-radio-label" for="{{ $fieldId }}">
                        {{ $label ?? $slot }}
                    </label>
                    @if ($description)
                        <span class="mantis-radio-description">{{ $description }}</span>
                    @endif
                </div>
            @endif
        </div>
        @if ($error)
            <x-form.error :message="$error" />
        @endif
    </div>
@endif
