@props(['message' => null])
@if ($message)<div {{ $attributes->merge(['class' => 'invalid-feedback d-block']) }}>{{ $message }}</div>@endif
