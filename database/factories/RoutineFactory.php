<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Group;
use App\Models\Program;
use App\Models\Routine;
use App\Models\Room;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Routine>
 */
class RoutineFactory extends Factory
{
    protected $model = Routine::class;

    public function definition(): array
    {
        $startHour = fake()->numberBetween(7, 16);
        $startTime = sprintf('%02d:00', $startHour);
        $endTime = sprintf('%02d:00', $startHour + 1);

        return [
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'department_id' => Department::factory(),
            'program_id' => fake()->boolean(80) ? Program::factory() : null,
            'shift_id' => Shift::factory(),
            'group_id' => fake()->boolean(60) ? Group::factory() : null,
            'section_id' => Section::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory(),
            'room_id' => Room::factory(),
            'day_of_week' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'active',
        ];
    }
}
