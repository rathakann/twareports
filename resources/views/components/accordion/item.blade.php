@props([
    'id' => 'acc-item-' . uniqid(),
    'title',
    'icon' => null,
    'open' => false,
    'badge' => null,
    'parentId' => null,
])

<div class="mantis-accordion-item {{ $open ? 'is-open' : '' }}">
    <h3 class="mantis-accordion-header" id="heading-{{ $id }}">
        <button class="mantis-accordion-button {{ $open ? '' : 'collapsed' }}"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse-{{ $id }}"
                aria-expanded="{{ $open ? 'true' : 'false' }}"
                aria-controls="collapse-{{ $id }}">
            <div class="d-inline-flex align-items-center gap-2">
                @if ($icon)
                    <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
                @endif
                <span>{{ $title }}</span>
                @if ($badge)
                    <span class="badge bg-primary-subtle text-primary ms-2" style="font-size: 0.75rem;">{{ $badge }}</span>
                @endif
            </div>
            <i data-lucide="chevron-down" class="chevron-icon"></i>
        </button>
    </h3>
    <div id="collapse-{{ $id }}"
         class="collapse {{ $open ? 'show' : '' }}"
         aria-labelledby="heading-{{ $id }}"
         @if($parentId) data-bs-parent="#{{ $parentId }}" @endif>
        <div class="mantis-accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>
