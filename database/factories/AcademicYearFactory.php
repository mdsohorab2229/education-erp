<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2022, 2028);

        return [
            'name' => "{$year}-" . ($year + 1),
            'start_date' => "{$year}-04-01",
            'end_date' => ($year + 1) . "-03-31",
            'is_current' => false,
        ];
    }

    public function current(): static
    {
        return $this->state(fn (array $attr) => ['is_current' => true]);
    }
}
