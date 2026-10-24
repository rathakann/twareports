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

    /**
     * Determine if a route is currently active, including its resource child routes (e.g. create, edit, show).
     */
    public static function isRouteActive(?string $route): bool
    {
        if (empty($route)) {
            return false;
        }

        if (request()->routeIs($route) || request()->routeIs($route.'.*')) {
            return true;
        }

        if (str_ends_with($route, '.index')) {
            $prefix = substr($route, 0, -6);
            if (request()->routeIs($prefix.'.*')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a navigation item or any of its children/active patterns are currently active.
     *
     * @param  array<string, mixed>  $item
     */
    public static function isItemActive(array $item): bool
    {
        if (! empty($item['active'])) {
            $patterns = (array) $item['active'];
            foreach ($patterns as $pattern) {
                if (request()->routeIs($pattern)) {
                    return true;
                }
            }
        }

        if (! empty($item['route']) && self::isRouteActive($item['route'])) {
            return true;
        }

        if (! empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if (self::isItemActive($child)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine if any child of a navigation item is currently active.
     *
     * @param  array<string, mixed>  $item
     */
    public static function isChildActive(array $item): bool
    {
        if (empty($item['children'])) {
            return false;
        }

        foreach ($item['children'] as $child) {
            if (self::isItemActive($child)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve the required permission for a given route name.
     *
     * @return string|array<int, string>|null
     */
    public static function getRequiredPermissionForRoute(?string $route): string|array|null
    {
        if (empty($route)) {
            return null;
        }

        if (isset(self::$routePermissions[$route])) {
            return self::$routePermissions[$route];
        }

        $navigation = config('navigation', []);

        return self::findPermissionInNavigation($navigation, $route);
    }

    /**
     * Search navigation tree recursively for route permission.
     *
     * @param  array<int, mixed>  $items
     * @return string|array<int, string>|null
     */
    protected static function findPermissionInNavigation(array $items, string $route): string|array|null
    {
        foreach ($items as $item) {
            if (isset($item['route']) && $item['route'] === $route && isset($item['permission'])) {
                return $item['permission'];
            }

            if (! empty($item['children'])) {
                $found = self::findPermissionInNavigation($item['children'], $route);
                if ($found !== null) {
                    return $found;
                }
            }

            if (! empty($item['items'])) {
                $found = self::findPermissionInNavigation($item['items'], $route);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * Check if a list of granted permission names satisfies the permission required by a route.
     *
     * @param  array<int, string>  $grantedPermissions
     */
    public static function permissionsAllowRoute(array $grantedPermissions, ?string $route): bool
    {
        if (empty($route) || $route === 'dashboard') {
            return true;
        }

        if (in_array('*', $grantedPermissions, true)) {
            return true;
        }

        $required = self::getRequiredPermissionForRoute($route);
        if (empty($required)) {
            return true;
        }

        if (is_array($required)) {
            return ! empty(array_intersect($required, $grantedPermissions));
        }

        if (is_string($required) && str_contains($required, '|')) {
            $options = explode('|', $required);
            foreach ($options as $opt) {
                if (in_array(trim($opt), $grantedPermissions, true)) {
                    return true;
                }
            }

            return false;
        }

        return in_array($required, $grantedPermissions, true);
    }

    /**
     * Check if a specific user can access the target route based on their roles and permissions.
     */
    public static function userCanAccessRoute(User $user, ?string $route): bool
    {
        if (empty($route) || $route === 'dashboard') {
            return true;
        }

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $required = self::getRequiredPermissionForRoute($route);
        if (empty($required)) {
            return true;
        }

        if (is_array($required)) {
            foreach ($required as $perm) {
                if ($user->can($perm) || Gate::forUser($user)->allows($perm)) {
                    return true;
                }
            }

            return false;
        }

        if (is_string($required) && str_contains($required, '|')) {
            foreach (explode('|', $required) as $perm) {
                $perm = trim($perm);
                if ($perm !== '' && ($user->can($perm) || Gate::forUser($user)->allows($perm))) {
                    return true;
                }
            }

            return false;
        }

        return $user->can($required) || Gate::forUser($user)->allows($required);
    }

    /**
     * Get a list of selectable landing pages grouped by category for user profile configuration.
     *
     * @return array<string, array<string, string>>
     */
    public static function getLandingPageOptions(): array
    {
        return [
            'General' => [
                'dashboard' => 'Dashboard',
            ],
            'Reports - Purchase' => [
                'reports.purchase.purchase-orders.index' => 'Purchase Orders',
                'reports.purchase.ap-invoices.index' => 'AP Invoices',
                'reports.purchase.outgoing-payments.index' => 'Outgoing Payments',
            ],
            'Reports - Sales' => [
                'reports.sales.purchase-orders.index' => 'Sales Purchase Orders',
                'reports.sales.ar-invoices.index' => 'AR Invoices',
                'reports.sales.incoming-payments.index' => 'Incoming Payments',
                'reports.sales.deposits.index' => 'Deposits',
            ],
            'Reports - Financial' => [
                'reports.financial.balance-sheet.index' => 'Balance Sheet',
                'reports.financial.profit-loss.index' => 'Profit & Loss',
                'reports.financial.cash-flow.index' => 'Cash Flow',
                'reports.financial.trial-balance.index' => 'Trial Balance',
                'reports.financial.general-ledger.index' => 'General Ledger',
            ],
            'Components' => [
                'theme.components' => 'UI Components',
                'theme.listing' => 'Example Listing',
                'theme.form' => 'Example Form',
            ],
            'Administration' => [
                'users.index' => 'User Management',
                'roles.index' => 'Role Management',
                'permissions.index' => 'Permission Matrix',
            ],
        ];
    }
}
