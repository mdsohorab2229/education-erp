<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'name' => fake()->unique()->randomElement([
                'Data Structures',
                'Algorithms',
                'Calculus I',
                'Linear Algebra',
                'Quantum Mechanics',
                'Organic Chemistry',
                'Cell Biology',
                'British Poetry',
                'World History',
                'Microeconomics',
            ]),
            'code' => fake()->unique()->regexify('[A-Z]{3,5}'),
            'credits' => fake()->randomFloat(2, 1, 4),
            'type' => fake()->randomElement(['theory', 'lab', 'practical']),
            'description' => fake()->sentence(),
        ];
    }
}
