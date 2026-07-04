<?php
declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class NoGradeRangeOverlap implements ValidationRule
{
    public function __construct(
        private readonly float $minMark,
        private readonly float $maxMark,
        private readonly ?int $excludeId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table('grades')
            ->where('max_mark', '>=', $this->minMark)
            ->where('min_mark', '<=', $this->maxMark);

        if ($this->excludeId !== null) {
            $query->where('id', '!=', $this->excludeId);
        }

        if ($query->exists()) {
            $fail('The grade range overlaps with an existing grade range.');
        }
    }
}
