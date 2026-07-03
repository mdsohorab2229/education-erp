<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('subject');

        return [
            'program_id' => ['required', 'exists:programs,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')
                    ->where('program_id', $this->input('program_id'))
                    ->ignore($id),
            ],
            'credits' => ['required', 'numeric', 'min:0.5', 'max:99.99'],
            'type' => ['required', 'string', 'in:theory,lab,practical'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
