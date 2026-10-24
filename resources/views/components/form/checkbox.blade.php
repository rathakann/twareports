@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false,
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
    $isChecked = (bool) old($name, $checked);
    $sizeClass = match($size) {
        'sm' => 'mantis-check-sm',
        'lg' => 'mantis-check-lg',
        default => '',
    };
    $toneClass = 'mantis-check-' . $tone;
@endphp

@if ($card)
    <label class="mantis-card-check {{ $isChecked ? 'is-selected' : '' }} {{ $disabled ? 'is-disabled' : '' }} {{ $error ? 'is-invalid' : '' }}" for="{{ $fieldId }}">
        <input type="checkbox"
               id="{{ $fieldId }}"
               name="{{ $name }}"
               value="{{ $value }}"
               class="form-check-input mantis-card-check-input mantis-check-input {{ $toneClass }}"
               @checked($isChecked)
               @disabled($disabled)
               @if($indeterminate) data-indeterminate="true" @endif
               {{ $attributes }}>
        <div class="mantis-card-check-content">
            @if ($icon)
                <div class="mantis-card-check-icon">
                    <i data-lucide="{{ $icon }}"></i>
                </div>
            @endif
            <div class="mantis-card-check-body">
                <span class="mantis-card-check-title">{{ $label ?? $slot }}</span>
                @if ($description)
                    <span class="mantis-card-check-desc">{{ $description }}</span>
                @endif
            </div>
        </div>
    </label>
@else
    <div class="mantis-check-group {{ $sizeClass }} {{ $toneClass }} {{ $inline ? 'form-check-inline' : '' }} {{ $error ? 'has-error' : '' }}">
        <div class="mantis-check-wrapper">
            <input type="checkbox"
                   id="{{ $fieldId }}"
                   name="{{ $name }}"
                   value="{{ $value }}"
                   class="form-check-input mantis-check-input {{ $error ? 'is-invalid' : '' }}"
                   @checked($isChecked)
                   @disabled($disabled)
                   @if($indeterminate) data-indeterminate="true" @endif
                   {{ $attributes }}>
            @if ($label || !$slot->isEmpty() || $description)
                <div class="mantis-check-label-wrap">
                    <label class="form-check-label mantis-check-label" for="{{ $fieldId }}">
                        {{ $label ?? $slot }}
                    </label>
                    @if ($description)
                        <span class="mantis-check-description">{{ $description }}</span>
                    @endif
                </div>
            @endif
        </div>
        @if ($error)
            <x-form.error :message="$error" />
        @endif
    </div>
@endif
