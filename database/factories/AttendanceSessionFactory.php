<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\AttendanceSession;
use App\Models\Department;
use App\Models\Group;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    protected $model = AttendanceSession::class;

    public function definition(): array
    {
        $total = fake()->numberBetween(10, 60);
        $present = fake()->numberBetween(0, $total);
        $remaining = $total - $present;
        $absent = fake()->numberBetween(0, $remaining);
        $remaining2 = $remaining - $absent;
        $late = fake()->numberBetween(0, $remaining2);
        $leave = $remaining2 - $late;

        return [
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => function () {
                $number = fake()->numberBetween(1, 8);
                return Semester::firstOrCreate(
                    ['semester_number' => $number],
                    ['name' => "Semester {$number}", 'description' => '']
                )->id;
            },
            'department_id' => Department::factory(),
            'program_id' => Program::factory(),
            'shift_id' => Shift::factory(),
            'group_id' => fake()->boolean(70) ? Group::factory() : null,
            'section_id' => Section::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => User::factory(),
            'attendance_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'total_students' => $total,
            'present_count' => $present,
            'absent_count' => $absent,
            'late_count' => $late,
            'leave_count' => $leave,
            'remarks' => fake()->optional(0.3)->sentence(),
            'status' => 'open',
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attr) => ['status' => 'open']);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attr) => ['status' => 'completed']);
    }
}
