<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class Navigation
{
    /**
     * Map of known route names to required permissions when not explicitly defined in config.
     *
     * @var array<string, string|array<int, string>>
     */
    protected static array $routePermissions = [
        'users.index' => 'users.view',
        'roles.index' => 'roles.view',
        'permissions.index' => 'permissions.view|permissions.manage',
        'reports.purchase.purchase-orders.index' => 'reports.purchase.purchase-orders.view',
        'reports.purchase.ap-invoices.index' => 'reports.purchase.ap-invoices.view',
        'reports.purchase.outgoing-payments.index' => 'reports.purchase.outgoing-payments.view',
        'reports.sales.purchase-orders.index' => 'reports.sales.purchase-orders.view',
        'reports.sales.ar-invoices.index' => 'reports.sales.ar-invoices.view',
        'reports.sales.incoming-payments.index' => 'reports.sales.incoming-payments.view',
        'reports.sales.deposits.index' => 'reports.sales.deposits.view',
        'reports.financial.balance-sheet.index' => 'reports.financial.balance-sheet.view',
        'reports.financial.profit-loss.index' => 'reports.financial.profit-loss.view',
        'reports.financial.cash-flow.index' => 'reports.financial.cash-flow.view',
        'reports.financial.trial-balance.index' => 'reports.financial.trial-balance.view',
        'reports.financial.general-ledger.index' => 'reports.financial.general-ledger.view',
    ];

    /**
     * Filter navigation tree so that only items the given user has permission to see are returned.
     * Empty groups are stripped out.
     *
     * @param  array<int, array<string, mixed>>  $navigation
     * @return array<int, array<string, mixed>>
     */
    public static function filter(array $navigation, ?User $user = null): array
    {
        $user ??= auth()->user();

        $filtered = [];

        foreach ($navigation as $group) {
            $items = $group['items'] ?? [];
            $visibleItems = self::filterItems($items, $user);

            if (! empty($visibleItems)) {
                $groupCopy = $group;
                $groupCopy['items'] = $visibleItems;
                $filtered[] = $groupCopy;
            }
        }

        return $filtered;
    }

    /**
     * Filter a list of navigation items recursively based on user permissions.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function filterItems(array $items, ?User $user = null): array
    {
        $user ??= auth()->user();
        $visible = [];

        foreach ($items as $item) {
            if (! self::userCanAccess($item, $user)) {
                continue;
            }

            if (! empty($item['children'])) {
                $item['children'] = self::filterItems($item['children'], $user);
                // If an item has no route of its own and all its children are filtered out, skip it
                if (empty($item['children']) && empty($item['route'])) {
                    continue;
                }
            }

            $visible[] = $item;
        }

        return $visible;
    }

    /**
     * Determine if a user has permission to access a specific navigation item.
     *
     * @param  array<string, mixed>  $item
     */
    public static function userCanAccess(array $item, ?User $user = null): bool
    {
        $user ??= auth()->user();

        // If no user is logged in, hide protected items
        if (! $user) {
            return false;
        }

        // Super Admin always has full access
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $permission = $item['permission'] ?? null;

        // If no explicit permission is declared on the item, check route-based mapping
        if (! $permission && ! empty($item['route'])) {
            $permission = self::$routePermissions[$item['route']] ?? null;
        }

        // If no permission requirement is specified or mapped, item is visible to authenticated users
        if (! $permission) {
            return true;
        }

        // Handle array of permissions (OR logic: user must have at least one)
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                if (Gate::forUser($user)->allows($perm) || $user->can($perm)) {
                    return true;
                }
            }

            return false;
        }

        // Handle pipe-separated permissions, e.g. "permissions.view|permissions.manage"
        if (is_string($permission) && str_contains($permission, '|')) {
            $perms = explode('|', $permission);
            foreach ($perms as $perm) {
                $perm = trim($perm);
                if ($perm !== '' && (Gate::forUser($user)->allows($perm) || $user->can($perm))) {
                    return true;
                }
            }

            return false;
        }

        // Single permission check
        return Gate::forUser($user)->allows($permission) || $user->can($permission);
    }
}
