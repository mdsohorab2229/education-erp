<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

class MarksApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isReject = $this->route() && str_contains($this->route()->getName(), 'reject');

        return [
            'remark' => [$isReject ? 'required' : 'nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'remark.required' => 'A remark is required when rejecting marks.',
        ];
    }
}
