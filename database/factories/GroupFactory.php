<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'name' => fake()->unique()->randomElement(['Group A', 'Group B', 'Group C']),
            'capacity' => fake()->numberBetween(25, 50),
            'description' => fake()->sentence(),
        ];
    }
}
