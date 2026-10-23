@props(['name', 'id' => null, 'value' => null, 'icon' => null, 'error' => null])
<div @class(['input-icon-left' => $icon])>
    @if ($icon)<i class="input-icon-addon" data-lucide="{{ $icon }}"></i>@endif
    <textarea {{ $attributes->merge(['class' => 'form-control'.($error ? ' is-invalid' : '')]) }} id="{{ $id ?? $name }}" name="{{ $name }}">{{ old($name, $value) }}</textarea>
    @if ($error)<x-form.error :message="$error" />@endif
</div>
