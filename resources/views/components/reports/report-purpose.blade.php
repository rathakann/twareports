@props([
    'title',
    'subtitle' => null,
    'icon' => 'info',
    'standard' => 'IFRS / US GAAP Standard',
    'definition',
    'purpose',
    'equation' => null,
    'indicators' => [],
    'cardId' => null,
])

@php
    $uniqueId = $cardId ?? 'purpose-' . \Illuminate\Support\Str::slug($title);
@endphp

<div id="{{ $uniqueId }}" class="card border-0 shadow-sm rounded-3 mb-4 report-purpose-card definition-section d-none" style="display: none;">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom border-secondary border-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <span class="report-purpose-icon">
                    <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6 d-flex align-items-center gap-2 flex-wrap">
                        <span>Report Definition & Purpose</span>
                        @if ($standard)
                            <span class="badge report-purpose-badge">{{ $standard }}</span>
                        @endif
                    </h5>
                    @if ($subtitle)
                        <span class="small text-secondary">{{ $subtitle }}</span>
                    @endif
                </div>
            </div>
            <button type="button" class="btn-close btn-close-definition small ms-auto" aria-label="Close" title="Hide Definition"></button>
        </div>

        <div class="row g-3">
            <div class="col-lg-6 col-12">
                <div class="p-3 rounded-3 h-100 report-purpose-block report-purpose-def">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="book-open" class="text-primary" style="width: 16px; height: 16px;"></i>
                        <h6 class="fw-bold text-dark mb-0 small text-uppercase" style="letter-spacing: 0.5px;">Definition</h6>
                    </div>
                    <p class="text-secondary small mb-0 lh-base">
                        {{ $definition }}
                    </p>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="p-3 rounded-3 h-100 report-purpose-block report-purpose-aim">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="target" class="text-primary" style="width: 16px; height: 16px;"></i>
                        <h6 class="fw-bold text-dark mb-0 small text-uppercase" style="letter-spacing: 0.5px;">Core Purpose & Objectives</h6>
                    </div>
                    <p class="text-secondary small mb-0 lh-base">
                        {{ $purpose }}
                    </p>
                </div>
            </div>

            @if ($equation || count($indicators) > 0)
                <div class="col-12">
                    <div class="p-2 px-3 rounded-3 report-purpose-formula d-flex flex-wrap align-items-center justify-content-between gap-3">
                        @if ($equation)
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold small text-uppercase" style="font-size: 11px;">
                                    Governing Principle
                                </span>
                                <code class="text-dark fw-bold small report-formula-code">{{ $equation }}</code>
                            </div>
                        @endif

                        @if (count($indicators) > 0)
                            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                                <span class="small text-secondary fw-medium">Key Focus:</span>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($indicators as $ind)
                                        <span class="badge report-indicator-pill">{{ $ind }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
