@props(['title' => null, 'subtitle' => null, 'flush' => false])
<section {{ $attributes->merge(['class' => 't-card content-card']) }}>
    @if ($title || isset($actions))<header class="content-card-header"><div>@if ($title)<h2>{{ $title }}</h2>@endif @if ($subtitle)<p>{{ $subtitle }}</p>@endif</div>@if (isset($actions))<div class="content-card-actions">{{ $actions }}</div>@endif</header>@endif
    <div @class(['content-card-body', 'p-0' => $flush])>{{ $slot }}</div>
</section>
