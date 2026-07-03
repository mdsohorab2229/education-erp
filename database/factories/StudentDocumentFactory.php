<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentDocument>
 */
class StudentDocumentFactory extends Factory
{
    protected $model = StudentDocument::class;

    public function definition(): array
    {
        $type = fake()->randomElement([
            'birth_certificate',
            'transcript',
            'id_card',
            'fee_receipt',
            'transfer_certificate',
            'medical_record',
        ]);

        $ext = fake()->randomElement(['pdf', 'jpg', 'png', 'docx']);

        return [
            'student_id' => Student::factory(),
            'document_type' => $type,
            'file_name' => fake()->uuid() . '.' . $ext,
            'file_path' => 'students/documents/' . fake()->uuid() . '.' . $ext,
            'mime_type' => fake()->randomElement(['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
            'size' => fake()->numberBetween(10000, 5000000),
        ];
    }
}
