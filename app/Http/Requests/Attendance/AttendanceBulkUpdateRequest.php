<?php
declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceBulkUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_session_id' => ['required', 'integer', 'exists:attendance_sessions,id'],
            'attendance_status' => ['required', 'string', Rule::enum(AttendanceStatus::class)],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_status.enum' => 'The attendance status must be one of: P (Present), A (Absent), L (Late), LV (Leave).',
            'student_ids.required' => 'At least one student must be selected.',
            'student_ids.min' => 'At least one student must be selected.',
        ];
    }
}
