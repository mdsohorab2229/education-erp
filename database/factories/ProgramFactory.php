<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->unique()->randomElement([
                'Bachelor of Computer Science',
                'Bachelor of Mathematics',
                'Bachelor of Physics',
                'Bachelor of Chemistry',
                'Bachelor of Biology',
                'Bachelor of English',
                'Bachelor of History',
                'Bachelor of Business Administration',
            ]),
            'code' => fake()->unique()->regexify('[A-Z]{3,5}'),
            'duration_years' => fake()->randomElement([2, 3, 4]),
            'description' => fake()->sentence(),
        ];
    }
}
