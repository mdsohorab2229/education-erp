<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guardian>
 */
class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'name' => fake()->name(),
            'relation' => fake()->randomElement(['father', 'mother', 'guardian']),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'occupation' => fake()->jobTitle(),
            'address' => fake()->address(),
        ];
    }
}
