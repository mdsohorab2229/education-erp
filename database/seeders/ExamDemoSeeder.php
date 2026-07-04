<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\ExamType;
use App\Models\Grade;
use App\Models\Mark;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamDemoSeeder extends Seeder
{
    public function run(): void
    {
        $academicYears = AcademicYear::all();

        if ($academicYears->isEmpty()) {
            $this->command?->warn('No academic years found. Skipping ExamDemoSeeder.');

            return;
        }

        $semesters = Semester::all();

        if ($semesters->isEmpty()) {
            $this->command?->warn('No semesters found. Skipping ExamDemoSeeder.');

            return;
        }

        $sections = Section::with('program')->get();

        if ($sections->isEmpty()) {
            $this->command?->warn('No sections found. Skipping ExamDemoSeeder.');

            return;
        }

        $teachers = User::role('Teacher')->get();

        if ($teachers->isEmpty()) {
            $this->command?->warn('No teacher users found. Skipping ExamDemoSeeder.');

            return;
        }

        $shifts = Shift::all();

        if ($shifts->isEmpty()) {
            $this->command?->warn('No shifts found. Skipping ExamDemoSeeder.');

            return;
        }

        $this->ensureExamTypes();
        $this->ensureGrades();

        $academicYear = $academicYears->firstWhere('is_current', true) ?? $academicYears->first();
        $semester = $semesters->first();

        $examCount = 0;
        $examSubjectCount = 0;
        $markCount = 0;

        $examTypes = ExamType::all();

        foreach ($sections as $section) {
            $sectionSubjects = Subject::where('program_id', $section->program_id)->get();

            if ($sectionSubjects->isEmpty()) {
                continue;
            }

            $shift = $shifts->random();
            $examType = $examTypes->random();
            $teacher = $teachers->random();

            $title = $examType->name . ' — ' . $section->name . ' (' . $academicYear->name . ')';

            $exam = Exam::firstOrCreate(
                ['title' => $title, 'section_id' => $section->id],
                [
                    'exam_type_id' => $examType->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'department_id' => $section->program->department_id,
                    'program_id' => $section->program_id,
                    'shift_id' => $shift->id,
                    'start_date' => now()->addDays(fake()->numberBetween(1, 14))->format('Y-m-d'),
                    'end_date' => now()->addDays(fake()->numberBetween(15, 30))->format('Y-m-d'),
                    'status' => fake()->randomElement(['draft', 'published', 'completed']),
                    'created_by' => $teacher->id,
                    'updated_by' => $teacher->id,
                ]
            );

            $examCount++;

            foreach ($sectionSubjects as $subject) {
                $subjectTeacher = $teachers->random();

                $examSubject = ExamSubject::firstOrCreate(
                    ['exam_id' => $exam->id, 'subject_id' => $subject->id],
                    [
                        'teacher_id' => $subjectTeacher->id,
                        'full_mark' => 100,
                        'pass_mark' => 40,
                        'practical_mark' => fake()->boolean(50) ? 25 : null,
                        'viva_mark' => fake()->boolean(30) ? 10 : null,
                        'created_by' => $teacher->id,
                        'updated_by' => $teacher->id,
                    ]
                );

                $examSubjectCount++;

                $students = Student::where('section_id', $section->id)->get();

                foreach ($students as $student) {
                    $obtained = round(fake()->randomFloat(2, 10, 95), 2);
                    $practical = $examSubject->practical_mark
                        ? round(fake()->randomFloat(2, 0, (float) $examSubject->practical_mark), 2)
                        : null;
                    $viva = $examSubject->viva_mark
                        ? round(fake()->randomFloat(2, 0, (float) $examSubject->viva_mark), 2)
                        : null;
                    $total = round($obtained + ($practical ?? 0) + ($viva ?? 0), 2);

                    $grade = Grade::where('min_mark', '<=', $total)
                        ->where('max_mark', '>=', $total)
                        ->first();

                    $markStatus = fake()->randomElement(['pending', 'pending', 'pending', 'approved', 'rejected']);

                    $data = [
                        'obtained_mark' => $obtained,
                        'practical_mark' => $practical,
                        'viva_mark' => $viva,
                        'total_mark' => $total,
                        'grade_id' => $grade?->id,
                        'approval_status' => $markStatus,
                        'remark' => fake()->optional(0.2)->sentence(),
                        'created_by' => $teacher->id,
                        'updated_by' => $teacher->id,
                    ];

                    if ($markStatus === 'approved') {
                        $data['approved_by'] = $subjectTeacher->id;
                        $data['approved_at'] = now();
                    }

                    Mark::firstOrCreate(
                        ['exam_subject_id' => $examSubject->id, 'student_id' => $student->id],
                        $data
                    );

                    $markCount++;
                }
            }

            $this->command?->info("Exam seeded for section: {$section->name}");
        }

        $this->command?->info("Exam types: " . ExamType::count());
        $this->command?->info("Grades: " . Grade::count());
        $this->command?->info("Exams created: {$examCount}");
        $this->command?->info("Exam subjects created: {$examSubjectCount}");
        $this->command?->info("Marks created: {$markCount}");
    }

    private function ensureExamTypes(): void
    {
        $types = [
            ['name' => 'Midterm', 'code' => 'MID'],
            ['name' => 'Final', 'code' => 'FIN'],
            ['name' => 'Quiz', 'code' => 'QUIZ'],
            ['name' => 'Class Test', 'code' => 'CT'],
            ['name' => 'Practical', 'code' => 'PRAC'],
            ['name' => 'Viva', 'code' => 'VIVA'],
            ['name' => 'Model Test', 'code' => 'MODEL'],
        ];

        foreach ($types as $type) {
            ExamType::firstOrCreate(
                ['name' => $type['name']],
                [
                    'code' => $type['code'],
                    'description' => $type['name'] . ' examination',
                    'status' => 'active',
                ]
            );
        }
    }

    private function ensureGrades(): void
    {
        $grades = [
            ['grade_name' => 'A+', 'grade_letter' => 'A+', 'min_mark' => 80, 'max_mark' => 100, 'gpa_point' => 4.00, 'remarks' => 'Outstanding'],
            ['grade_name' => 'A', 'grade_letter' => 'A', 'min_mark' => 75, 'max_mark' => 79, 'gpa_point' => 3.75, 'remarks' => 'Excellent'],
            ['grade_name' => 'A-', 'grade_letter' => 'A-', 'min_mark' => 70, 'max_mark' => 74, 'gpa_point' => 3.50, 'remarks' => 'Very Good'],
            ['grade_name' => 'B+', 'grade_letter' => 'B+', 'min_mark' => 65, 'max_mark' => 69, 'gpa_point' => 3.25, 'remarks' => 'Good'],
            ['grade_name' => 'B', 'grade_letter' => 'B', 'min_mark' => 60, 'max_mark' => 64, 'gpa_point' => 3.00, 'remarks' => 'Satisfactory'],
            ['grade_name' => 'B-', 'grade_letter' => 'B-', 'min_mark' => 55, 'max_mark' => 59, 'gpa_point' => 2.75, 'remarks' => 'Above Average'],
            ['grade_name' => 'C+', 'grade_letter' => 'C+', 'min_mark' => 50, 'max_mark' => 54, 'gpa_point' => 2.50, 'remarks' => 'Average'],
            ['grade_name' => 'C', 'grade_letter' => 'C', 'min_mark' => 45, 'max_mark' => 49, 'gpa_point' => 2.25, 'remarks' => 'Below Average'],
            ['grade_name' => 'C-', 'grade_letter' => 'C-', 'min_mark' => 40, 'max_mark' => 44, 'gpa_point' => 2.00, 'remarks' => 'Poor'],
            ['grade_name' => 'D', 'grade_letter' => 'D', 'min_mark' => 35, 'max_mark' => 39, 'gpa_point' => 1.50, 'remarks' => 'Very Poor'],
            ['grade_name' => 'F', 'grade_letter' => 'F', 'min_mark' => 0, 'max_mark' => 34, 'gpa_point' => 0.00, 'remarks' => 'Fail'],
        ];

        foreach ($grades as $grade) {
            Grade::firstOrCreate(
                ['grade_name' => $grade['grade_name']],
                $grade
            );
        }
    }
}
