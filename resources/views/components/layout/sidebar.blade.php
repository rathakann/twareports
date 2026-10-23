<aside class="app-sidebar" id="sidebar" data-app-sidebar aria-label="Primary navigation">
    <nav class="sidebar-nav">
        @foreach ($navigation as $group)
            @if (! empty($group['items']))
                <section class="sidebar-group">
                    @if (! empty($group['label']))<p class="s-label">{{ $group['label'] }}</p>@endif
                    @foreach ($group['items'] as $item)
                        @include('components.layout.nav-item', ['item' => $item, 'depth' => 0])
                    @endforeach
                </section>
            @endif
        @endforeach
    </nav>
</aside>
