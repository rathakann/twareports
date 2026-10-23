@props(['for', 'required' => false])
<label {{ $attributes->merge(['class' => 'form-label']) }} for="{{ $for }}">{{ $slot }}@if ($required)<span class="text-danger" aria-hidden="true"> *</span>@endif</label>
