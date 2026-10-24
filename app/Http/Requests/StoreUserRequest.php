<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Support\Navigation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('users.create') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:active,inactive'],
            'landing_page' => ['nullable', 'string', 'max:100'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
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

            $roles = (array) $this->input('roles', []);
            if (in_array('Super Admin', $roles, true)) {
                return;
            }

            $rolePermissions = [];
            if (! empty($roles)) {
                $rolePermissions = Role::whereIn('name', $roles)
                    ->with('permissions')
                    ->get()
                    ->pluck('permissions')
                    ->flatten()
                    ->pluck('name')
                    ->unique()
                    ->all();
            }

            if (! Navigation::permissionsAllowRoute($rolePermissions, $landingPage)) {
                $required = Navigation::getRequiredPermissionForRoute($landingPage);
                $reqStr = is_array($required) ? implode(' or ', $required) : str_replace('|', ' or ', (string) $required);
                $validator->errors()->add(
                    'landing_page',
                    "The selected default landing page requires the '{$reqStr}' permission, which is not granted to this user."
                );
            }
        });
    }
}
