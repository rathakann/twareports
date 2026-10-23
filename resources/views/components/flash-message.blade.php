@props(['tone' => 'success', 'dismissible' => true])
@if (session('success'))
    <div {{ $attributes->merge(['class' => 'alert alert-success'.($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
        {{ session('success') }}
        @if ($dismissible)<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>@endif
    </div>
@elseif (session('error') || session('danger'))
    <div {{ $attributes->merge(['class' => 'alert alert-danger'.($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
        {{ session('error') ?? session('danger') }}
        @if ($dismissible)<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>@endif
    </div>
@elseif ($slot->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'alert alert-'.$tone.($dismissible ? ' alert-dismissible fade show' : '')]) }} role="alert">
        {{ $slot }}
        @if ($dismissible)<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>@endif
    </div>
@endif
