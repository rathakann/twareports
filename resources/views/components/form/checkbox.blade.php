@props(['name', 'id' => null, 'checked' => false, 'value' => '1', 'disabled' => false, 'error' => null])
@php($fieldId = $id ?? $name)
<div class="switch-field">
    <input type="hidden" name="{{ $name }}" value="0">
    <input {{ $attributes->merge(['class' => 'switch-field-input'.($error ? ' is-invalid' : '')]) }} id="{{ $fieldId }}" name="{{ $name }}" type="checkbox" value="{{ $value }}" role="switch" @checked(old($name, $checked)) @disabled($disabled)>
    <label class="switch-field-control" for="{{ $fieldId }}"><span class="switch-field-thumb"></span></label>
    <label class="switch-field-label" for="{{ $fieldId }}">{{ $slot }}</label>
    @if ($error)<x-form.error :message="$error" />@endif
</div>
