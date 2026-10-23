<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class ExportPermission
{
    /**
     * Determine whether the given user has permission to export data.
     *
     * @param  string|null  $module  The resource/table module (e.g. 'users', 'roles', 'permissions')
     * @param  string|null  $format  The export format (e.g. 'excel', 'csv', 'copy', 'print', 'pdf')
     * @param  User|null  $user  The user to check, or null for currently authenticated user
     */
    public static function canExport(?string $module = null, ?string $format = null, ?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user) {
            return false;
        }

        // Super Admin or global export.all permission
        if ($user->hasRole('Super Admin') || Gate::forUser($user)->allows('export.all')) {
            return true;
        }

        $format = $format ? strtolower(trim($format)) : null;
        $module = $module ? strtolower(trim($module)) : null;

        // 1. Check exact module-specific format permission (e.g., users.export.excel)
        if ($module && $format && Gate::forUser($user)->allows("{$module}.export.{$format}")) {
            return true;
        }

        // 2. Check general format permission (e.g., export.excel)
        $hasGlobalFormat = $format ? Gate::forUser($user)->allows("export.{$format}") : true;

        // 3. Check module-level export permission (e.g., users.export)
        $hasModuleExport = $module ? Gate::forUser($user)->allows("{$module}.export") : false;

        // If user has both format permission and module-level permission
        if ($hasGlobalFormat && $hasModuleExport) {
            return true;
        }

        $allPermissions = $user->getAllPermissions()->pluck('name');

        // Check if user has ANY module-scoped export permissions (e.g., users.export, roles.export)
        $hasAnyModuleExport = $allPermissions->contains(fn (string $name) => str_ends_with($name, '.export'));

        // If user has the requested format permission (e.g. export.excel):
        // Allow if there are no module-level restrictions assigned to the user, OR if user has this module's export
        if ($hasGlobalFormat && (! $hasAnyModuleExport || $hasModuleExport)) {
            return true;
        }

        // Check if user has ANY specific format permissions assigned (e.g., export.excel, export.csv)
        $hasAnyFormatExport = $allPermissions->contains(fn (string $name) => str_starts_with($name, 'export.') && $name !== 'export.all');

        // If user has module-level permission (e.g. users.export) and has no specific format restrictions:
        if ($hasModuleExport && ! $hasAnyFormatExport) {
            return true;
        }

        return false;
    }

    /**
     * Authorize an export action or abort with a 403 Forbidden response.
     */
    public static function authorize(?string $module = null, ?string $format = null, ?User $user = null): void
    {
        abort_unless(self::canExport($module, $format, $user), 403, 'You do not have permission to export this data.');
    }
}
