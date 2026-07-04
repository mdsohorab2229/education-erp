<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MarksApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'approval_status' => ['required', 'string', Rule::enum(ApprovalStatus::class)],
            'remark' => ['required_if:approval_status,rejected', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'approval_status.required' => 'The approval status is required.',
            'approval_status.enum' => 'Invalid approval status.',
            'remark.required_if' => 'A remark is required when rejecting marks.',
        ];
    }
}
