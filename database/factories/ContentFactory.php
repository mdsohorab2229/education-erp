<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Content;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['pdf', 'video', 'notes']);

        $file = match ($type) {
            'pdf' => [
                'file_name' => fake()->slug() . '-notes.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => fake()->numberBetween(50000, 5000000),
            ],
            'video' => [
                'file_name' => fake()->slug() . '-lecture.mp4',
                'mime_type' => 'video/mp4',
                'file_size' => fake()->numberBetween(10000000, 200000000),
            ],
            'notes' => [
                'file_name' => fake()->slug() . '-handout.docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'file_size' => fake()->numberBetween(20000, 500000),
            ],
        };

        return [
            'teacher_id' => User::factory(),
            'subject_id' => Subject::factory(),
            'section_id' => Section::factory(),
            'title' => ucfirst(fake()->words(4, true)),
            'type' => $type,
            'file_name' => $file['file_name'],
            'file_path' => 'contents/' . $file['file_name'],
            'file_size' => $file['file_size'],
            'mime_type' => $file['mime_type'],
            'description' => fake()->boolean(70) ? fake()->paragraph() : null,
            'status' => fake()->randomElement(['active', 'active', 'active', 'processing', 'active', 'failed']),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    public function processing(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'processing']);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'failed']);
    }

    public function ofType(string $type): static
    {
        $mimeMap = [
            'pdf' => ['application/pdf', '.pdf'],
            'video' => ['video/mp4', '.mp4'],
            'notes' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', '.docx'],
        ];

        [$mime, $ext] = $mimeMap[$type];
        $name = fake()->slug() . "-$type$ext";
        $size = $type === 'video'
            ? fake()->numberBetween(10000000, 200000000)
            : fake()->numberBetween(50000, 5000000);

        return $this->state(fn (array $attrs) => [
            'type' => $type,
            'file_name' => $name,
            'file_path' => "contents/$name",
            'file_size' => $size,
            'mime_type' => $mime,
        ]);
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
