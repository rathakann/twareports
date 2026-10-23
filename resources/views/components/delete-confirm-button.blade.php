@props(['label' => 'Delete', 'title' => 'Delete this item?', 'message' => 'This action cannot be undone.'])
<button type="button" {{ $attributes->merge(['class' => 'btn btn-danger']) }} data-delete-confirm data-delete-title="{{ $title }}" data-delete-message="{{ $message }}"><i data-lucide="trash-2"></i><span>{{ $label }}</span></button>
