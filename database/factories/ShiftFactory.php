<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Morning', 'Afternoon', 'Evening']),
            'start_time' => fake()->randomElement(['07:00', '12:00', '16:00']),
            'end_time' => fake()->randomElement(['12:00', '16:00', '20:00']),
            'description' => fake()->sentence(),
        ];
    }
}
