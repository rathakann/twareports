@props(['tone' => 'success', 'dismissible' => true])
<div {{ $attributes->merge(['class' => 'alert alert-'.$tone.($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">{{ $slot }}@if ($dismissible)<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>@endif</div>
