<?php
declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class MarkNotExceedFullMark implements ValidationRule
{
    public function __construct(
        private readonly int $examSubjectId,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fullMark = DB::table('exam_subjects')
            ->where('id', $this->examSubjectId)
            ->value('full_mark');

        if ($fullMark !== null && (float) $value > (float) $fullMark) {
            $fail('The mark cannot exceed the subject full mark.');
        }
    }
}
