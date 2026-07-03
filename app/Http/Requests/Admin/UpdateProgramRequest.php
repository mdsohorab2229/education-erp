<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('program');

        return [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:150', Rule::unique('programs', 'name')->ignore($id)],
            'code' => ['required', 'string', 'max:20', Rule::unique('programs', 'code')->ignore($id)],
            'duration_years' => ['required', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
