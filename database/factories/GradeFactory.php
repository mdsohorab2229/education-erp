<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'grade_name' => fake()->unique()->randomElement(['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D', 'F']),
            'grade_letter' => function (array $attrs) {
                $map = [
                    'A+' => 'A+', 'A' => 'A', 'A-' => 'A-',
                    'B+' => 'B+', 'B' => 'B', 'B-' => 'B-',
                    'C+' => 'C+', 'C' => 'C', 'C-' => 'C-',
                    'D' => 'D', 'F' => 'F',
                ];
                return $map[$attrs['grade_name']] ?? 'F';
            },
            'min_mark' => function (array $attrs) {
                $map = [
                    'A+' => 80, 'A' => 75, 'A-' => 70,
                    'B+' => 65, 'B' => 60, 'B-' => 55,
                    'C+' => 50, 'C' => 45, 'C-' => 40,
                    'D' => 40, 'F' => 0,
                ];
                return $map[$attrs['grade_name']] ?? 0;
            },
            'max_mark' => function (array $attrs) {
                $map = [
                    'A+' => 100, 'A' => 89, 'A-' => 84,
                    'B+' => 79, 'B' => 74, 'B-' => 69,
                    'C+' => 64, 'C' => 59, 'C-' => 54,
                    'D' => 49, 'F' => 44,
                ];
                return $map[$attrs['grade_name']] ?? 44;
            },
            'gpa_point' => function (array $attrs) {
                $map = [
                    'A+' => 4.00, 'A' => 3.75, 'A-' => 3.50,
                    'B+' => 3.25, 'B' => 3.00, 'B-' => 2.75,
                    'C+' => 2.50, 'C' => 2.25, 'C-' => 2.00,
                    'D' => 1.50, 'F' => 0.00,
                ];
                return $map[$attrs['grade_name']] ?? 0.00;
            },
            'remarks' => function (array $attrs) {
                $map = [
                    'A+' => 'Outstanding', 'A' => 'Excellent', 'A-' => 'Very Good',
                    'B+' => 'Good', 'B' => 'Satisfactory', 'B-' => 'Above Average',
                    'C+' => 'Average', 'C' => 'Below Average', 'C-' => 'Poor',
                    'D' => 'Very Poor', 'F' => 'Fail',
                ];
                return $map[$attrs['grade_name']] ?? '';
            },
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'inactive']);
    }
}
