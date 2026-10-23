@props(['title' => 'No results', 'message' => null, 'icon' => 'inbox'])
<div {{ $attributes->merge(['class' => 'empty-state']) }}><i data-lucide="{{ $icon }}"></i><h2>{{ $title }}</h2>@if ($message)<p>{{ $message }}</p>@endif {{ $slot }}</div>
