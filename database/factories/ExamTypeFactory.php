<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\ExamType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamType>
 */
class ExamTypeFactory extends Factory
{
    protected $model = ExamType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Midterm', 'Final', 'Quiz', 'Assignment', 'Practical', 'Viva', 'Class Test', 'Model Test']),
            'code' => fake()->unique()->regexify('[A-Z]{2,5}'),
            'description' => fake()->optional()->sentence(),
            'status' => 'active',
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'inactive']);
    }
}
