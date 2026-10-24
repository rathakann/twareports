<?php

namespace App\Http\Requests;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $permission = $this->isMethod('POST') ? 'permissions.create' : 'permissions.edit';

        return ($this->user()?->can($permission) ?? false) || ($this->user()?->can('permissions.manage') ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Permission|null $permission */
        $permission = $this->route('permission');
        $permissionId = $permission instanceof Permission ? $permission->id : (int) $permission;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permissionId),
            ],
        ];
    }
}
