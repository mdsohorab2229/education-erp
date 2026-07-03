<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Guardian;
use App\Models\Section;
use App\Models\Shift;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_current', true)->first();
        $shifts = Shift::all();
        /** @var Collection<int, Section> */
        $sections = Section::with('program')->get();

        foreach (range(1, 50) as $i) {
            /** @var Section $section */
            $section = $sections->random();
            $shift = $shifts->random();
            $program = $section->program;
            $group = $program->groups()->inRandomOrder()->first();

            $firstName = fake()->firstName();
            $lastName = fake()->lastName();

            $student = Student::create([
                'admission_no' => 'STU-' . date('Y') . '-' . str_pad((string) ($i + 100), 6, '0', STR_PAD_LEFT),
                'roll_no' => (string) fake()->unique()->numberBetween(1, 9999),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => fake()->dateTimeBetween('-20 years', '-16 years')->format('Y-m-d'),
                'gender' => fake()->randomElement(['male', 'female']),
                'phone' => fake()->unique()->phoneNumber(),
                'email' => strtolower("{$firstName}.{$lastName}@school.edu"),
                'address' => fake()->address(),
                'photo' => null,
                'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'status' => fake()->randomElement(['active', 'active', 'active', 'active', 'suspended', 'graduated']),
                'academic_year_id' => $academicYear->id,
                'program_id' => $program->id,
                'section_id' => $section->id,
                'shift_id' => $shift->id,
                'group_id' => $group?->id,
            ]);

            Guardian::create([
                'student_id' => $student->id,
                'name' => fake()->name(),
                'relation' => fake()->randomElement(['father', 'mother', 'guardian']),
                'phone' => fake()->phoneNumber(),
                'email' => fake()->safeEmail(),
                'occupation' => fake()->jobTitle(),
                'address' => fake()->address(),
            ]);

            $docCount = fake()->numberBetween(0, 3);

            foreach (range(1, $docCount) as $d) {
                StudentDocument::create([
                    'student_id' => $student->id,
                    'document_type' => fake()->randomElement(['birth_certificate', 'transcript', 'id_card', 'fee_receipt', 'transfer_certificate', 'medical_record']),
                    'file_name' => "{$student->admission_no}_doc_{$d}." . fake()->randomElement(['pdf', 'jpg', 'png']),
                    'file_path' => "students/documents/{$student->admission_no}/doc_{$d}." . fake()->randomElement(['pdf', 'jpg', 'png']),
                    'mime_type' => fake()->randomElement(['application/pdf', 'image/jpeg', 'image/png']),
                    'size' => fake()->numberBetween(50000, 2000000),
                ]);
            }
        }
    }
}
