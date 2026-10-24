<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\User;
use App\Support\Navigation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('users.edit') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : (int) $user;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8'],
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

            /** @var User|null $user */
            $user = $this->route('user');
            $user = $user instanceof User ? $user : User::find($user);

            $roles = $this->has('roles') ? (array) $this->input('roles', []) : ($user?->roles->pluck('name')->all() ?? []);
            if (in_array('Super Admin', $roles, true) || $user?->hasRole('Super Admin')) {
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
