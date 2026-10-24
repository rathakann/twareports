<div class="btn-group btn-group-sm" role="group" aria-label="Permission Actions">
    @canany(['permissions.edit', 'permissions.manage'])
        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 btn-action" title="Edit Permission">
            <i data-lucide="file-pen-line"></i>
            <span>Edit</span>
        </a>
    @endcanany

    @canany(['permissions.delete', 'permissions.manage'])
        <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1 btn-delete-permission btn-action"
            data-permission-id="{{ $permission->uuid }}"
            data-permission-name="{{ $permission->name }}"
            data-delete-url="{{ route('permissions.destroy', $permission) }}"
            title="Delete Permission">
            <i data-lucide="trash-2"></i>
            <span>Delete</span>
        </button>
    @endcanany
</div>
