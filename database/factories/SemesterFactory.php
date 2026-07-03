<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Semester>
 */
class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(1, 8);

        return [
            'name' => "Semester {$number}",
            'semester_number' => $number,
            'description' => fake()->sentence(),
        ];
    }
}
