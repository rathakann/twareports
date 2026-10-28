<?php

namespace App\DataTables;

use App\Models\Module;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ModuleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder<Module>  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function (Module $module): string {
                $showUrl = route('admin.modules.show', $module);
                $editUrl = route('admin.modules.edit', $module);
                $deleteUrl = route('admin.modules.destroy', $module);

                return '<div class="d-flex align-items-center justify-content-center gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-icon btn-view-module" data-id="'.$module->uuid.'" data-url="'.$showUrl.'" title="View Details">
                        <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                    </button>
                    <a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary btn-icon" title="Edit Module">
                        <i data-lucide="pencil" style="width: 15px; height: 15px;"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-icon btn-delete-module" data-id="'.$module->uuid.'" data-url="'.$deleteUrl.'" data-label="'.e($module->label).'" title="Delete Module">
                        <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                    </button>
                </div>';
            })
            ->editColumn('label', function (Module $module): string {
                $icon = e($module->icon ?: 'circle');
                $parentHint = $module->parent
                    ? '<div class="small text-muted d-flex align-items-center gap-1 mt-0.5"><i data-lucide="corner-down-right" style="width: 12px; height: 12px;"></i> '.e($module->parent->label).'</div>'
                    : '';
                $badgeHint = $module->badge
                    ? ' <span class="badge bg-danger text-white rounded-pill px-1.5 py-0.5 ms-1" style="font-size: 10px;">'.e($module->badge).'</span>'
                    : '';

                return '<div class="d-flex align-items-center gap-2">
                    <div class="module-icon-box bg-light border rounded d-inline-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 32px; height: 32px;">
                        <i data-lucide="'.$icon.'" style="width: 16px; height: 16px;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-dark">'.e($module->label).$badgeHint.'</div>
                        '.$parentHint.'
                    </div>
                </div>';
            })
            ->editColumn('group_label', function (Module $module): string {
                $group = e($module->group_label ?: 'Workspace');
                $palette = [
                    'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                    'bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-25',
                    'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                    'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25',
                    'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                    'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                ];
                $fixed = [
                    'Workspace' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
                    'Master Data' => 'bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-25',
                    'Report' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                    'Reports' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                    'Access Control' => 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25',
                    'Administrator' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                ];
                $color = $fixed[$module->group_label] ?? $palette[abs(crc32($module->group_label)) % count($palette)];

                return '<span class="badge '.$color.' px-2 py-1">'.$group.'</span>';
            })
            ->editColumn('route', function (Module $module): string {
                if ($module->route) {
                    return '<code class="text-primary small font-monospace">'.e($module->route).'</code>';
                }
                if ($module->url) {
                    return '<a href="'.e($module->url).'" target="_blank" class="small text-decoration-none font-monospace text-truncate d-inline-block" style="max-width: 180px;">'.e($module->url).'</a>';
                }

                return '<span class="text-muted small">—</span>';
            })
            ->editColumn('permission', function (Module $module): string {
                if ($module->permission) {
                    return '<span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 font-monospace small d-inline-flex align-items-center gap-1">
                        <i data-lucide="shield-check" style="width: 12px; height: 12px;"></i> '.e($module->permission).'
                    </span>';
                }

                return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small d-inline-flex align-items-center gap-1">
                    <i data-lucide="globe" style="width: 12px; height: 12px;"></i> Public
                </span>';
            })
            ->editColumn('sort_order', function (Module $module): string {
                return '<span class="badge bg-light text-dark border font-monospace px-2.5 py-1">'.(int) $module->sort_order.'</span>';
            })
            ->editColumn('is_active', function (Module $module): string {
                $isActive = (bool) $module->is_active;
                $toggleUrl = route('admin.modules.status', $module);
                $checked = $isActive ? 'checked' : '';
                $badgeClass = $isActive
                    ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'
                    : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                $badgeText = $isActive ? 'Active' : 'Inactive';
                $canManage = auth()->user()?->can('admin.modules.manage') ?? false;

                if ($canManage) {
                    return '<div class="d-inline-flex align-items-center gap-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input module-status-toggle" type="checkbox" role="switch"
                                data-toggle-url="'.$toggleUrl.'"
                                data-module-id="'.$module->uuid.'"
                                '.$checked.'
                                title="'.($isActive ? 'Click to deactivate' : 'Click to activate').'"
                                style="cursor: pointer; width: 2.25rem; height: 1.15rem;">
                        </div>
                        <span class="badge '.$badgeClass.' px-2 py-1">'.$badgeText.'</span>
                    </div>';
                }

                return '<span class="badge '.$badgeClass.' d-inline-flex align-items-center gap-1 px-2 py-1">
                    <span class="badge-dot '.($isActive ? 'bg-success' : 'bg-secondary').'"></span> '.$badgeText.'
                </span>';
            })
            ->rawColumns(['action', 'label', 'group_label', 'route', 'permission', 'sort_order', 'is_active'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Module>
     */
    public function query(Module $model): QueryBuilder
    {
        $query = $model->newQuery()->with('parent');

        if ($this->request()->filled('group') && $this->request()->get('group') !== 'all') {
            $query->where('group_label', $this->request()->get('group'));
        }

        if ($this->request()->filled('status') && $this->request()->get('status') !== 'all') {
            $query->where('is_active', $this->request()->get('status') === 'active');
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('modules-table')
            ->columns($this->getColumns())
            ->minifiedAjax('', 'data.group = $("#filter-group").val(); data.status = $("#filter-status").val();')
            ->orderBy(3, 'asc')
            ->parameters([
                'dom' => "<'row p-2'<'col-xs-6 col-sm-6 col-md-6 float-start'f><'col-xs-6 col-sm-6 col-md-6'Br>>t<'row p-2 mb-0 align-items-center justify-content-between gy-2'<'col-12 col-md-auto d-flex align-items-center flex-wrap gap-3'li><'col-12 col-md-auto ms-md-auto d-flex align-items-center justify-content-md-end justify-content-start'p>>",
                'drawCallback' => 'function() { if (window.lucide) { window.lucide.createIcons(); } }',
                'responsive' => true,
                'autoWidth' => false,
                'pageLength' => 25,
                'lengthMenu' => [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                'language' => [
                    'search' => '_INPUT_',
                    'searchPlaceholder' => 'Search modules...',
                    'paginate' => [
                        'first' => 'First',
                        'previous' => 'Previous',
                        'next' => 'Next',
                        'last' => 'Last',
                    ],
                ],
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->searchable(false)->orderable(false)->width(45)->addClass('text-center'),
            Column::computed('action')->title('Action')->exportable(false)->printable(false)->searchable(false)->orderable(false)->width(110)->addClass('text-center'),
            Column::make('label')->title('Module / Menu'),
            Column::make('group_label')->title('Nav Group'),
            Column::make('route')->title('Route / URL'),
            Column::make('permission')->title('Permission'),
            Column::make('sort_order')->title('Sort Order')->width(85)->addClass('text-center'),
            Column::make('is_active')->title('Status')->width(140)->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Modules_'.date('YmdHis');
    }
}
