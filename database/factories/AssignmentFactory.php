<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'teacher_id' => User::factory(),
            'subject_id' => Subject::factory(),
            'section_id' => Section::factory(),
            'title' => ucfirst(fake()->words(4, true)),
            'description' => fake()->boolean(80) ? fake()->paragraph() : null,
            'attachment' => fake()->boolean(40) ? 'attachments/' . fake()->slug() . '.pdf' : null,
            'due_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'total_marks' => fake()->randomElement([10, 20, 30, 50, 100]),
            'status' => 'active',
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'inactive']);
    }

    public function dueToday(): static
    {
        return $this->state(fn (array $attrs) => ['due_date' => now()->format('Y-m-d')]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attrs) => ['due_date' => now()->subDays(fake()->numberBetween(1, 14))->format('Y-m-d')]);
    }

    public function forSection(Section $section): static
    {
        return $this->state(fn (array $attrs) => ['section_id' => $section->id]);
    }

    public function forSubject(Subject $subject): static
    {
        return $this->state(fn (array $attrs) => ['subject_id' => $subject->id]);
    }
}
