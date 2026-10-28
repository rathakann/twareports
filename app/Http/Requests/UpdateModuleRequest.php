<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('admin.modules.manage') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $moduleId = $this->route('module')?->id ?? $this->input('id');

        return [
            'group_label' => ['required', 'string', 'max:100'],
            'group_order' => ['nullable', 'integer', 'min:0'],
            'label' => ['required', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:100'],
            'route' => ['nullable', 'string', 'max:200'],
            'url' => ['nullable', 'string', 'max:500'],
            'permission' => ['nullable', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:modules,id', 'not_in:'.$moduleId],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'badge' => ['nullable', 'string', 'max:50'],
            'target' => ['nullable', 'string', 'in:_self,_blank'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
