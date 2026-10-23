<x-app-layout title="Edit Permission {{ $permission->name }} · TWA Reports" :navigation="$navigation">
    <x-slot:breadcrumb>
        <a href="{{ route('dashboard') }}"><i data-lucide="house"></i> Home</a>
        <span>/</span>
        <span class="text-secondary">Access Control</span>
        <span>/</span>
        <a href="{{ route('permissions.index') }}" class="text-secondary text-decoration-none">Permission Management</a>
        <span>/</span>
        <span class="fw-semibold text-dark" aria-current="page">Edit Permission</span>
    </x-slot:breadcrumb>

    <x-management.page-header
        title="Edit Permission: {{ $permission->name }}"
        section="Access Control"
        icon="key"
        description="Update permission identifier name.">
        <x-slot:actions>
            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                <span>Back to Permissions</span>
            </a>
        </x-slot:actions>
    </x-management.page-header>

    @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 rounded-3 mb-4">
            <div class="fw-semibold mb-1">Please correct the errors below:</div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('permissions.update', $permission) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm rounded-3 mb-4" style="max-width: 600px;">
            <div class="card-header bg-transparent border-bottom py-3">
                <h2 class="h6 fw-bold mb-0">Permission Details</h2>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label for="name" class="form-label small fw-semibold text-secondary">Permission Name</label>
                    <div class="input-icon-left">
                        <i class="input-icon-addon" data-lucide="key"></i>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $permission->name) }}" required>
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-end gap-2">
                <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                    <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>
    </form>
</x-app-layout>
