@props(['name', 'id' => null, 'options' => [], 'selected' => null, 'error' => null, 'searchable' => true, 'placeholder' => null, 'icon' => null])
<div @class(['input-icon-left' => $icon])>
    @if ($icon)<i class="input-icon-addon" data-lucide="{{ $icon }}"></i>@endif
    <select {{ $attributes->merge(['class' => 'form-select js-select2'.($error ? ' is-invalid' : '')]) }} id="{{ $id ?? $name }}" name="{{ $name }}" @if ($placeholder) data-placeholder="{{ $placeholder }}" @endif>
        @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $value => $label)<option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>{{ $label }}</option>@endforeach
    </select>
    @if ($error)<x-form.error :message="$error" />@endif
</div>
