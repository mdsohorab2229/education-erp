<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mark>
 */
class MarkFactory extends Factory
{
    protected $model = Mark::class;

    public function definition(): array
    {
        $obtained = fake()->randomFloat(2, 0, 100);

        return [
            'exam_subject_id' => ExamSubject::factory(),
            'student_id' => Student::factory(),
            'obtained_mark' => $obtained,
            'practical_mark' => fake()->optional(0.5)->randomFloat(2, 0, 25),
            'viva_mark' => fake()->optional(0.3)->randomFloat(2, 0, 10),
            'total_mark' => function (array $attrs) {
                $total = $attrs['obtained_mark']
                    + ($attrs['practical_mark'] ?? 0)
                    + ($attrs['viva_mark'] ?? 0);
                return round($total, 2);
            },
            'approval_status' => 'pending',
            'remark' => fake()->optional(0.3)->sentence(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attrs) => [
            'approval_status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attrs) => ['approval_status' => 'rejected']);
    }
}
