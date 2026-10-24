<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PermissionHelper
{
    /**
     * Map of known permission prefixes to human-readable module definitions.
     *
     * @var array<string, array{name: string, icon: string, badge: string, color: string}>
     */
    protected static array $moduleDefinitions = [
        'reports.financial' => [
            'name' => 'Financial Reports',
            'icon' => 'bar-chart-3',
            'badge' => 'Financials',
            'color' => 'primary',
        ],
        'reports.sales' => [
            'name' => 'Sales Reports',
            'icon' => 'shopping-cart',
            'badge' => 'Sales',
            'color' => 'success',
        ],
        'reports.purchase' => [
            'name' => 'Purchase Reports',
            'icon' => 'shopping-bag',
            'badge' => 'Purchasing',
            'color' => 'warning',
        ],
        'users' => [
            'name' => 'User Management',
            'icon' => 'users',
            'badge' => 'Users',
            'color' => 'info',
        ],
        'roles' => [
            'name' => 'Access Control (Roles)',
            'icon' => 'shield',
            'badge' => 'RBAC',
            'color' => 'danger',
        ],
        'permissions' => [
            'name' => 'Access Control (Permissions)',
            'icon' => 'key',
            'badge' => 'RBAC',
            'color' => 'secondary',
        ],
        'export' => [
            'name' => 'Global Export Tools',
            'icon' => 'file-output',
            'badge' => 'Export',
            'color' => 'purple',
        ],
    ];

    /**
     * Map of known submodule slugs to human-readable labels and icons.
     *
     * @var array<string, array{label: string, icon: string}>
     */
    protected static array $submoduleLabels = [
        'balance-sheet' => ['label' => 'Balance Sheet', 'icon' => 'scale'],
        'profit-loss' => ['label' => 'Profit & Loss (P&L)', 'icon' => 'trending-up'],
        'cash-flow' => ['label' => 'Cash Flow Statement', 'icon' => 'banknote'],
        'trial-balance' => ['label' => 'Trial Balance', 'icon' => 'table-2'],
        'general-ledger' => ['label' => 'General Ledger', 'icon' => 'book-open'],
        'purchase-orders' => ['label' => 'Purchase Orders', 'icon' => 'file-text'],
        'ap-invoices' => ['label' => 'A/P Invoices', 'icon' => 'receipt'],
        'outgoing-payments' => ['label' => 'Outgoing Payments', 'icon' => 'send'],
        'ar-invoices' => ['label' => 'A/R Invoices', 'icon' => 'receipt'],
        'incoming-payments' => ['label' => 'Incoming Payments', 'icon' => 'arrow-down-left'],
        'deposits' => ['label' => 'Customer Deposits', 'icon' => 'wallet'],
        'users' => ['label' => 'User Accounts', 'icon' => 'user'],
        'roles' => ['label' => 'Role Definitions', 'icon' => 'shield'],
        'permissions' => ['label' => 'System Permissions', 'icon' => 'key'],
        'export' => ['label' => 'Export Utilities', 'icon' => 'file-down'],
    ];

    /**
     * Map of action suffixes to human labels, icons, and styling colors.
     *
     * @var array<string, array{label: string, icon: string, type: string, class: string}>
     */
    protected static array $actionDefinitions = [
        'view' => ['label' => 'View', 'icon' => 'eye', 'type' => 'view', 'class' => 'bg-info-subtle text-info border-info-subtle'],
        'create' => ['label' => 'Create', 'icon' => 'plus', 'type' => 'create', 'class' => 'bg-success-subtle text-success border-success-subtle'],
        'edit' => ['label' => 'Edit', 'icon' => 'pencil', 'type' => 'edit', 'class' => 'bg-warning-subtle text-warning border-warning-subtle'],
        'delete' => ['label' => 'Delete', 'icon' => 'trash-2', 'type' => 'delete', 'class' => 'bg-danger-subtle text-danger border-danger-subtle'],
        'status' => ['label' => 'Status', 'icon' => 'toggle-left', 'type' => 'status', 'class' => 'bg-primary-subtle text-primary border-primary-subtle'],
        'manage' => ['label' => 'Manage', 'icon' => 'shield-check', 'type' => 'manage', 'class' => 'bg-danger-subtle text-danger border-danger-subtle'],
        'export' => ['label' => 'Export', 'icon' => 'download', 'type' => 'export', 'class' => 'bg-secondary-subtle text-secondary border-secondary-subtle'],
        'excel' => ['label' => 'Excel (.xlsx)', 'icon' => 'file-spreadsheet', 'type' => 'export', 'class' => 'bg-success-subtle text-success border-success-subtle'],
        'csv' => ['label' => 'CSV (.csv)', 'icon' => 'file-text', 'type' => 'export', 'class' => 'bg-info-subtle text-info border-info-subtle'],
        'copy' => ['label' => 'Copy Clipboard', 'icon' => 'copy', 'type' => 'export', 'class' => 'bg-secondary-subtle text-secondary border-secondary-subtle'],
        'print' => ['label' => 'Print / PDF', 'icon' => 'printer', 'type' => 'export', 'class' => 'bg-secondary-subtle text-secondary border-secondary-subtle'],
        'all' => ['label' => 'Full Clearance (*)', 'icon' => 'shield-alert', 'type' => 'manage', 'class' => 'bg-danger-subtle text-danger border-danger-subtle'],
    ];

    /**
     * Humanize a permission into structured module, submodule, and action details.
     *
     * @return array{
     *     name: string,
     *     module: string,
     *     module_slug: string,
     *     module_icon: string,
     *     module_color: string,
     *     submodule: string,
     *     submodule_slug: string,
     *     submodule_icon: string,
     *     action: string,
     *     action_type: string,
     *     action_icon: string,
     *     badge_class: string
     * }
     */
    public static function humanize(string|object $permission): array
    {
        $name = is_string($permission) ? $permission : ($permission->name ?? '');
        $parts = explode('.', $name);

        $moduleName = 'General Capabilities';
        $moduleSlug = 'general';
        $moduleIcon = 'shield';
        $moduleColor = 'secondary';

        // Check matched module prefix
        foreach (self::$moduleDefinitions as $prefix => $def) {
            if ($name === $prefix || str_starts_with($name, $prefix.'.')) {
                $moduleName = $def['name'];
                $moduleSlug = Str::slug($prefix);
                $moduleIcon = $def['icon'];
                $moduleColor = $def['color'];
                break;
            }
        }

        // Submodule & Action resolution
        $submoduleSlug = 'general';
        $submoduleLabel = 'General';
        $submoduleIcon = 'layers';

        $actionKey = 'view';
        if (count($parts) >= 2) {
            $actionKey = end($parts);
        }

        if (str_starts_with($name, 'reports.')) {
            // e.g. reports.financial.balance-sheet.view
            $submoduleSlug = $parts[2] ?? ($parts[1] ?? 'general');
        } elseif (count($parts) >= 2) {
            // e.g. users.view, roles.create, export.excel
            $submoduleSlug = $parts[0];
        }

        if (isset(self::$submoduleLabels[$submoduleSlug])) {
            $submoduleLabel = self::$submoduleLabels[$submoduleSlug]['label'];
            $submoduleIcon = self::$submoduleLabels[$submoduleSlug]['icon'];
        } else {
            $submoduleLabel = ucwords(str_replace(['-', '_'], ' ', $submoduleSlug));
        }

        $actionDef = self::$actionDefinitions[$actionKey] ?? [
            'label' => ucwords(str_replace(['-', '_'], ' ', $actionKey)),
            'icon' => 'check',
            'type' => 'other',
            'class' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        ];

        return [
            'name' => $name,
            'module' => $moduleName,
            'module_slug' => $moduleSlug,
            'module_icon' => $moduleIcon,
            'module_color' => $moduleColor,
            'submodule' => $submoduleLabel,
            'submodule_slug' => Str::slug($submoduleSlug),
            'submodule_icon' => $submoduleIcon,
            'action' => $actionDef['label'],
            'action_type' => $actionDef['type'],
            'action_icon' => $actionDef['icon'],
            'badge_class' => $actionDef['class'],
        ];
    }

    /**
     * Group a collection or array of permissions by Module and Submodule.
     *
     * @param  iterable<int, mixed>  $permissions
     * @return array<string, array{
     *     name: string,
     *     slug: string,
     *     icon: string,
     *     color: string,
     *     total_permissions: int,
     *     submodules: array<string, array{
     *         name: string,
     *         slug: string,
     *         icon: string,
     *         actions: array<int, array{
     *             name: string,
     *             action: string,
     *             type: string,
     *             icon: string,
     *             badge_class: string
     *         }>
     *     }>
     * }>
     */
    public static function groupPermissions(iterable $permissions): array
    {
        $grouped = [];

        foreach ($permissions as $permission) {
            $meta = self::humanize($permission);
            $modKey = $meta['module_slug'];
            $subKey = $meta['submodule_slug'];

            if (! isset($grouped[$modKey])) {
                $grouped[$modKey] = [
                    'name' => $meta['module'],
                    'slug' => $meta['module_slug'],
                    'icon' => $meta['module_icon'],
                    'color' => $meta['module_color'],
                    'total_permissions' => 0,
                    'submodules' => [],
                ];
            }

            $grouped[$modKey]['total_permissions']++;

            if (! isset($grouped[$modKey]['submodules'][$subKey])) {
                $grouped[$modKey]['submodules'][$subKey] = [
                    'name' => $meta['submodule'],
                    'slug' => $meta['submodule_slug'],
                    'icon' => $meta['submodule_icon'],
                    'actions' => [],
                ];
            }

            $grouped[$modKey]['submodules'][$subKey]['actions'][] = [
                'name' => $meta['name'],
                'action' => $meta['action'],
                'type' => $meta['action_type'],
                'icon' => $meta['action_icon'],
                'badge_class' => $meta['badge_class'],
            ];
        }

        return $grouped;
    }

    /**
     * Resolve a readable landing page name for a route.
     */
    public static function getLandingPageLabel(?string $route): ?string
    {
        if (empty($route)) {
            return null;
        }

        $options = Navigation::getLandingPageOptions();
        foreach ($options as $group => $routes) {
            if (isset($routes[$route])) {
                return $routes[$route];
            }
        }

        return ucwords(str_replace(['.', '-', '_'], ' ', $route));
    }
}
