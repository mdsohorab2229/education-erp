<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\TeacherQualification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeacherQualification>
 */
class TeacherQualificationFactory extends Factory
{
    protected $model = TeacherQualification::class;

    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'degree' => fake()->randomElement(['B.Sc.', 'M.Sc.', 'Ph.D.', 'B.Ed.', 'M.Ed.', 'MBA']),
            'institution' => fake()->university(),
            'year' => fake()->numberBetween(2000, 2025),
            'grade' => fake()->randomElement(['A+', 'A', 'B+', 'B', 'First Division', 'Distinction']),
        ];
    }
}
