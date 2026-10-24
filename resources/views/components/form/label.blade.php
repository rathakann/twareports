@props(['for' => null, 'required' => false])
<label {{ $attributes->merge(['class' => 'form-label']) }} @if($for) for="{{ $for }}" @endif>{{ $slot }}@if ($required)<span class="text-danger" aria-hidden="true"> *</span>@endif</label>
