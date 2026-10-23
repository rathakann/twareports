@props([
    'id' => 'filter-created-date',
    'label' => 'Date Range',
    'placeholder' => 'YYYY-MM-DD to YYYY-MM-DD',
])

<div>
    <label for="{{ $id }}" class="form-label small fw-semibold text-secondary mb-1">{{ $label }}</label>
    <div class="input-group flex-nowrap date-range-group">
        <div class="input-icon-left flex-grow-1">
            <i class="input-icon-addon" data-lucide="calendar"></i>
            <input type="text" id="{{ $id }}" class="form-control" placeholder="{{ $placeholder }}" autocomplete="off">
        </div>
        <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-1 date-preset-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="Quick date options">
            <i data-lucide="calendar-clock" style="width: 14px; height: 14px;"></i>
            <span class="small fw-medium d-none d-sm-inline">Presets</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm date-preset-dropdown">
            <li><h6 class="dropdown-header">Current Period</h6></li>
            <li>
                <button class="dropdown-item js-date-preset-btn" type="button" data-preset="today" data-target="#{{ $id }}">
                    <i data-lucide="calendar-check" style="width: 14px; height: 14px;"></i>
                    <span>Today</span>
                </button>
            </li>
            <li>
                <button class="dropdown-item js-date-preset-btn" type="button" data-preset="this_week" data-target="#{{ $id }}">
                    <i data-lucide="calendar-days" style="width: 14px; height: 14px;"></i>
                    <span>This week</span>
                </button>
            </li>
            <li>
                <button class="dropdown-item js-date-preset-btn" type="button" data-preset="this_month" data-target="#{{ $id }}">
                    <i data-lucide="calendar-range" style="width: 14px; height: 14px;"></i>
                    <span>This month</span>
                </button>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><h6 class="dropdown-header">Past Periods</h6></li>
            <li>
                <button class="dropdown-item js-date-preset-btn" type="button" data-preset="last_month" data-target="#{{ $id }}">
                    <i data-lucide="calendar-minus" style="width: 14px; height: 14px;"></i>
                    <span>Last month</span>
                </button>
            </li>
            <li>
                <button class="dropdown-item js-date-preset-btn" type="button" data-preset="last_3_months" data-target="#{{ $id }}">
                    <i data-lucide="history" style="width: 14px; height: 14px;"></i>
                    <span>Last 3 month</span>
                </button>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <button class="dropdown-item text-danger js-date-preset-btn" type="button" data-preset="clear" data-target="#{{ $id }}">
                    <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                    <span>Clear date filter</span>
                </button>
            </li>
        </ul>
    </div>
</div>
