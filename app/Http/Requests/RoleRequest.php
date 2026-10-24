<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Support\Navigation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('POST')) {
            return $this->user()?->can('roles.create') ?? false;
        }

        return $this->user()?->can('roles.edit') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Role|null $role */
        $role = $this->route('role');
        $roleId = $role instanceof Role ? $role->id : (int) $role;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'landing_page' => ['nullable', 'string', 'max:100'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $landingPage = $this->input('landing_page');
            if (empty($landingPage)) {
                return;
            }

            /** @var Role|null $role */
            $role = $this->route('role');
            $roleName = $this->input('name') ?? ($role instanceof Role ? $role->name : null);

            // Super Admin has universal access
            if ($roleName === 'Super Admin') {
                return;
            }

            $permissions = (array) $this->input('permissions', []);

            if (! Navigation::permissionsAllowRoute($permissions, $landingPage)) {
                $required = Navigation::getRequiredPermissionForRoute($landingPage);
                $reqStr = is_array($required) ? implode(' or ', $required) : str_replace('|', ' or ', (string) $required);
                $validator->errors()->add(
                    'landing_page',
                    "The selected default landing page requires the '{$reqStr}' permission, which is not assigned to this role."
                );
            }
        });
    }
}
