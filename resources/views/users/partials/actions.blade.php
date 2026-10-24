<div class="btn-group btn-group-sm" role="group" aria-label="User Actions">
    @can('users.edit')
        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 btn-action" title="Edit User">
            <i data-lucide="file-pen-line"></i>
            <span>Edit</span>
        </a>
    @endcan

    @can('users.delete')
        @if ($user->id !== auth()->id() && (! $user->hasRole('Super Admin') || $user->id !== 1))
            <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1 btn-delete-user btn-action"
                data-user-id="{{ $user->uuid }}"
                data-user-name="{{ $user->name }}"
                data-delete-url="{{ route('users.destroy', $user) }}"
                title="Delete User">
                <i data-lucide="trash-2"></i>
                <span>Delete</span>
            </button>
        @else
            <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 btn-action" disabled title="Protected account cannot be deleted">
                <i data-lucide="lock"></i>
                <span>Protected</span>
            </button>
        @endif
    @endcan
</div>
