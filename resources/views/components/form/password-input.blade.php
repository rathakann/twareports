@props([
    'name',
    'id' => null,
    'placeholder' => 'Enter password',
    'value' => null,
    'disabled' => false,
    'required' => false,
    'error' => null,
    'icon' => 'lock',
])
@php
    $fieldId = $id ?? $name;
@endphp

<div class="mantis-password-wrap input-icon-left" x-data="{ show: false }">
    @if ($icon)
        <i class="input-icon-addon" data-lucide="{{ $icon }}"></i>
    @endif
    <input :type="show ? 'text' : 'password'"
           type="password"
           id="{{ $fieldId }}"
           name="{{ $name }}"
           value="{{ old($name, $value) }}"
           placeholder="{{ $placeholder }}"
           @disabled($disabled)
           @required($required)
           {{ $attributes->merge(['class' => 'form-control'.($error ? ' is-invalid' : '')]) }}>
    <button type="button"
            class="mantis-password-toggle"
            @click="show = !show"
            aria-label="Toggle password visibility"
            tabindex="-1">
        <span x-show="!show"><i data-lucide="eye"></i></span>
        <span x-show="show" style="display: none;"><i data-lucide="eye-off"></i></span>
    </button>
    @if ($error)
        <x-form.error :message="$error" />
    @endif
</div>
