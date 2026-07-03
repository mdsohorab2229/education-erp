<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->where('program_id', $this->input('program_id')),
            ],
            'credits' => ['required', 'numeric', 'min:0.5', 'max:99.99'],
            'type' => ['required', 'string', 'in:theory,lab,practical'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
