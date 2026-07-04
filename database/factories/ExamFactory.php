<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition(): array
    {
        return [
            'exam_type_id' => ExamType::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'department_id' => Department::factory(),
            'program_id' => Program::factory(),
            'shift_id' => Shift::factory(),
            'section_id' => Section::factory(),
            'title' => ucfirst(fake()->words(4, true)),
            'start_date' => fake()->dateTimeBetween('+1 week', '+2 weeks')->format('Y-m-d'),
            'end_date' => function (array $attrs) {
                return fake()->dateTimeBetween($attrs['start_date'] . ' +1 day', $attrs['start_date'] . ' +1 week')->format('Y-m-d');
            },
            'status' => 'draft',
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'published']);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'completed']);
    }
}
