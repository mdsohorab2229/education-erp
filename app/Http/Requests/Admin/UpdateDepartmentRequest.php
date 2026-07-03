<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('department');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('departments', 'name')->ignore($id)],
            'code' => ['required', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
