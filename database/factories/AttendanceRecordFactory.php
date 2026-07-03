<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'student_id' => Student::factory(),
            'attendance_status' => fake()->randomElement(['P', 'A', 'L', 'LV']),
            'remark' => fake()->optional(0.2)->sentence(),
            'checked_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ];
    }

    public function present(): static
    {
        return $this->state(fn (array $attr) => ['attendance_status' => 'P']);
    }

    public function absent(): static
    {
        return $this->state(fn (array $attr) => ['attendance_status' => 'A']);
    }

    public function late(): static
    {
        return $this->state(fn (array $attr) => ['attendance_status' => 'L']);
    }

    public function leave(): static
    {
        return $this->state(fn (array $attr) => ['attendance_status' => 'LV']);
    }
}
