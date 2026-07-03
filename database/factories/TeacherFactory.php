<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'employee_id' => 'EMP-' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female']),
            'address' => fake()->address(),
            'designation' => fake()->randomElement(['Senior Lecturer', 'Associate Professor', 'Professor', 'Assistant Professor', 'Lecturer']),
            'joining_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'status' => 'active',
        ];
    }
}
