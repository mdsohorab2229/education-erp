<?php
declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_session_id' => ['required', 'integer', 'exists:attendance_sessions,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'attendance_status' => ['required', 'string', Rule::enum(AttendanceStatus::class)],
            'remark' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_status.enum' => 'The attendance status must be one of: P (Present), A (Absent), L (Late), LV (Leave).',
        ];
    }
}
