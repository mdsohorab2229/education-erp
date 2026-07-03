<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentSubmission>
 */
class AssignmentSubmissionFactory extends Factory
{
    protected $model = AssignmentSubmission::class;

    public function definition(): array
    {
        $submittedAt = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => Student::factory(),
            'submission_file' => 'submissions/' . fake()->uuid() . '.pdf',
            'submitted_at' => $submittedAt,
            'marks' => null,
            'feedback' => null,
            'status' => 'submitted',
        ];
    }

    public function graded(): static
    {
        return $this->state(fn (array $attrs) => [
            'marks' => fake()->randomFloat(2, 0, 100),
            'feedback' => fake()->boolean(60) ? fake()->paragraph() : null,
            'status' => 'graded',
        ]);
    }

    public function late(): static
    {
        $assignment = Assignment::find($this->attributes['assignment_id'] ?? null);

        return $this->state(fn (array $attrs) => [
            'submitted_at' => $assignment && $assignment->due_date
                ? fake()->dateTimeBetween($assignment->due_date, $assignment->due_date->addDays(3))
                : fake()->dateTimeBetween('-3 days', 'now'),
            'status' => 'submitted',
        ]);
    }

    public function forStudent(Student $student): static
    {
        return $this->state(fn (array $attrs) => ['student_id' => $student->id]);
    }

    public function forAssignment(Assignment $assignment): static
    {
        return $this->state(fn (array $attrs) => ['assignment_id' => $assignment->id]);
    }
}
