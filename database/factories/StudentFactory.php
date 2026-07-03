<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\Program;
use App\Models\Section;
use App\Models\Shift;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'admission_no' => 'STU-' . date('Y') . '-' . str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'roll_no' => (string) fake()->unique()->numberBetween(1, 9999),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->date(max: '2010-01-01'),
            'gender' => fake()->randomElement(['male', 'female']),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'photo' => null,
            'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'status' => 'active',
            'academic_year_id' => AcademicYear::factory(),
            'program_id' => Program::factory(),
            'section_id' => Section::factory(),
            'shift_id' => Shift::factory(),
            'group_id' => Group::factory(),
        ];
    }
}
