<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Program;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'name' => fake()->unique()->randomElement(['Section A', 'Section B', 'Section C']),
            'capacity' => fake()->numberBetween(30, 60),
        ];
    }
}
