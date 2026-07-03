<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Computer Science',
                'Mathematics',
                'Physics',
                'Chemistry',
                'Biology',
                'English Literature',
                'History',
                'Business Studies',
                'Art & Design',
                'Physical Education',
            ]),
            'code' => fake()->unique()->regexify('[A-Z]{3,5}'),
            'description' => fake()->sentence(),
        ];
    }
}
