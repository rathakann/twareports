<div class="btn-group btn-group-sm" role="group" aria-label="Role Actions">
    @can('roles.edit')
        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" title="Edit Role">
            <i data-lucide="file-pen-line" style="width: 14px; height: 14px;"></i>
            <span>Edit</span>
        </a>
    @endcan

    @can('roles.delete')
        @if ($role->name !== 'Super Admin' && $role->users_count === 0)
            <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1 btn-delete-role"
                data-role-id="{{ $role->id }}"
                data-role-name="{{ $role->name }}"
                data-delete-url="{{ route('roles.destroy', $role) }}"
                title="Delete Role">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                <span>Delete</span>
            </button>
        @else
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" disabled
                title="{{ $role->name === 'Super Admin' ? 'Super Admin cannot be deleted' : 'Role is in use by active users' }}">
                <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                <span>Protected</span>
            </button>
        @endif
    @endcan
</div>
