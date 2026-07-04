<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamSubject>
 */
class ExamSubjectFactory extends Factory
{
    protected $model = ExamSubject::class;

    public function definition(): array
    {
        $fullMark = fake()->randomElement([50, 100]);

        return [
            'exam_id' => Exam::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory(),
            'full_mark' => $fullMark,
            'pass_mark' => round($fullMark * 0.4, 2),
            'practical_mark' => fake()->boolean(50) ? fake()->randomElement([25, 50]) : null,
            'viva_mark' => fake()->boolean(30) ? fake()->randomElement([10, 20]) : null,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
