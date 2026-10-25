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

    @auth
        @php
            $sideUser = auth()->user();
            $sideName = $sideUser->name ?? 'System Administrator';
            $sideEmail = $sideUser->email ?? 'admin@bizlite.local';
            $sideParts = explode(' ', trim($sideName));
            $sideInitials = count($sideParts) >= 2
                ? strtoupper(substr($sideParts[0], 0, 1) . substr(end($sideParts), 0, 1))
                : strtoupper(substr($sideName, 0, 2));
            $sideAvatarUrl = $sideUser->avatar_url;
        @endphp

        <div class="sidebar-footer">
            <div class="dropup sidebar-user-dropup w-100">
                <button class="sidebar-user-card btn w-100 d-flex align-items-center justify-content-between p-2"
                        type="button"
                        id="sidebarUserMenu"
                        data-bs-toggle="dropdown"
                        data-bs-display="static"
                        data-bs-auto-close="true"
                        aria-expanded="false"
                        title="{{ $sideName }}">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <span class="sidebar-user-avatar flex-shrink-0 overflow-hidden d-inline-flex align-items-center justify-content-center">
                            @if ($sideAvatarUrl)
                                <img src="{{ $sideAvatarUrl }}" alt="{{ $sideName }}" class="w-100 h-100 object-fit-cover rounded-circle user-avatar-preview">
                            @else
                                <span class="user-initials-text">{{ $sideInitials }}</span>
                            @endif
                        </span>
                        <span class="sidebar-user-name fw-semibold text-truncate text-start">
                            {{ $sideName }}
                        </span>
                    </div>
                    <i data-lucide="chevrons-up-down" class="sidebar-user-chevrons flex-shrink-0"></i>
                </button>

                <div class="dropdown-menu sidebar-user-menu shadow-lg border rounded-3 p-3 mb-2 w-100"
                     aria-labelledby="sidebarUserMenu"
                     style="min-width: 220px;">
                    <!-- User Header -->
                    <div class="d-flex align-items-center gap-2.5 mb-2.5">
                        <span class="sidebar-menu-avatar flex-shrink-0 overflow-hidden d-inline-flex align-items-center justify-content-center">
                            @if ($sideAvatarUrl)
                                <img src="{{ $sideAvatarUrl }}" alt="{{ $sideName }}" class="w-100 h-100 object-fit-cover rounded-circle user-avatar-preview">
                            @else
                                <span class="user-initials-text">{{ $sideInitials }}</span>
                            @endif
                        </span>
                        <div class="overflow-hidden">
                            <h6 class="fw-bold mb-0 text-truncate text-dark fs-6">{{ $sideName }}</h6>
                            <small class="text-secondary text-truncate d-block" style="font-size: 12px;">{{ $sideEmail }}</small>
                        </div>
                    </div>

                    <hr class="my-2 border-secondary-subtle">

                    <!-- Links -->
                    <div class="d-flex flex-column gap-1">
                        <a class="dropdown-item rounded-2 py-2 px-2.5 d-flex align-items-center gap-2.5 text-secondary"
                           href="{{ route('profile.index') }}">
                            <i data-lucide="user-round" style="width: 17px; height: 17px;"></i>
                            <span class="fw-medium text-dark">My Profile</span>
                        </a>
                        <a class="dropdown-item rounded-2 py-2 px-2.5 d-flex align-items-center gap-2.5 text-secondary"
                           href="{{ route('dashboard') }}">
                            <i data-lucide="layout-dashboard" style="width: 17px; height: 17px;"></i>
                            <span class="fw-medium text-dark">Dashboard</span>
                        </a>
                    </div>

                    <hr class="my-2 border-secondary-subtle">

                    <!-- Log Out -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <button type="submit"
                                class="dropdown-item rounded-2 py-2 px-2.5 d-flex align-items-center gap-2.5 text-danger w-100 border-0 bg-transparent text-start">
                            <i data-lucide="log-out" style="width: 17px; height: 17px;"></i>
                            <span class="fw-medium">Log out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endauth
</aside>
