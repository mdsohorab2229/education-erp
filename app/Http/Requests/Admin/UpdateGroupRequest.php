<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('group');

        return [
            'program_id' => ['required', 'exists:programs,id'],
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('groups', 'name')
                    ->where('program_id', $this->input('program_id'))
                    ->ignore($id),
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:99999'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
