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
use Database\Seeders\GradeSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExamTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSemestersExist();

        $academicYear = $this->ensureAcademicYear();
        $semester = Semester::where('semester_number', 1)->firstOrFail();
        $section = $this->ensureSection();
        $shift = $this->ensureShift();

        $subject1 = Subject::firstOrCreate(
            ['code' => 'ETEST-101', 'program_id' => $section->program_id],
            [
                'name' => 'Exam Testing Theory',
                'credits' => 3,
                'type' => 'theory',
            ]
        );

        $subject2 = Subject::firstOrCreate(
            ['code' => 'ETEST-102', 'program_id' => $section->program_id],
            [
                'name' => 'Exam Testing Lab',
                'credits' => 2,
                'type' => 'lab',
            ]
        );

        $teacher = $this->ensureTeacherUser();

        $midtermType = ExamType::firstOrCreate(
            ['name' => 'Midterm'],
            [
                'code' => 'MID',
                'description' => 'Midterm examination',
                'status' => 'active',
            ]
        );

        $finalType = ExamType::firstOrCreate(
            ['name' => 'Final'],
            [
                'code' => 'FIN',
                'description' => 'Final examination',
                'status' => 'active',
            ]
        );

        $this->ensureGrades();

        $midtermExam = Exam::firstOrCreate(
            ['title' => 'Midterm — Exam Test Section (2025-2026)'],
            [
                'exam_type_id' => $midtermType->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'department_id' => $section->program->department_id,
                'program_id' => $section->program_id,
                'shift_id' => $shift->id,
                'section_id' => $section->id,
                'start_date' => '2026-02-15',
                'end_date' => '2026-02-20',
                'status' => 'published',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $finalExam = Exam::firstOrCreate(
            ['title' => 'Final — Exam Test Section (2025-2026)'],
            [
                'exam_type_id' => $finalType->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'department_id' => $section->program->department_id,
                'program_id' => $section->program_id,
                'shift_id' => $shift->id,
                'section_id' => $section->id,
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-25',
                'status' => 'published',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $examSubject1 = ExamSubject::firstOrCreate(
            ['exam_id' => $midtermExam->id, 'subject_id' => $subject1->id],
            [
                'teacher_id' => $teacher->id,
                'full_mark' => 100,
                'pass_mark' => 40,
                'practical_mark' => null,
                'viva_mark' => null,
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $examSubject2 = ExamSubject::firstOrCreate(
            ['exam_id' => $midtermExam->id, 'subject_id' => $subject2->id],
            [
                'teacher_id' => $teacher->id,
                'full_mark' => 100,
                'pass_mark' => 40,
                'practical_mark' => 25,
                'viva_mark' => 10,
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $examSubject3 = ExamSubject::firstOrCreate(
            ['exam_id' => $finalExam->id, 'subject_id' => $subject1->id],
            [
                'teacher_id' => $teacher->id,
                'full_mark' => 100,
                'pass_mark' => 40,
                'practical_mark' => null,
                'viva_mark' => null,
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $student1 = Student::firstOrCreate(
            ['admission_no' => 'TEST-EXM-001'],
            [
                'roll_no' => '8881',
                'first_name' => 'Exam',
                'last_name' => 'StudentOne',
                'date_of_birth' => '2005-06-15',
                'gender' => 'male',
                'phone' => '4441111111',
                'email' => 'exam.student1@school.edu',
                'address' => 'Exam Test Address 1',
                'blood_group' => 'O+',
                'status' => 'active',
                'academic_year_id' => $academicYear->id,
                'program_id' => $section->program_id,
                'section_id' => $section->id,
                'shift_id' => $shift->id,
            ]
        );

        $student2 = Student::firstOrCreate(
            ['admission_no' => 'TEST-EXM-002'],
            [
                'roll_no' => '8882',
                'first_name' => 'Exam',
                'last_name' => 'StudentTwo',
                'date_of_birth' => '2005-08-20',
                'gender' => 'female',
                'phone' => '4442222222',
                'email' => 'exam.student2@school.edu',
                'address' => 'Exam Test Address 2',
                'blood_group' => 'A+',
                'status' => 'active',
                'academic_year_id' => $academicYear->id,
                'program_id' => $section->program_id,
                'section_id' => $section->id,
                'shift_id' => $shift->id,
            ]
        );

        $student3 = Student::firstOrCreate(
            ['admission_no' => 'TEST-EXM-003'],
            [
                'roll_no' => '8883',
                'first_name' => 'Exam',
                'last_name' => 'StudentThree',
                'date_of_birth' => '2005-11-10',
                'gender' => 'male',
                'phone' => '4443333333',
                'email' => 'exam.student3@school.edu',
                'address' => 'Exam Test Address 3',
                'blood_group' => 'B+',
                'status' => 'active',
                'academic_year_id' => $academicYear->id,
                'program_id' => $section->program_id,
                'section_id' => $section->id,
                'shift_id' => $shift->id,
            ]
        );

        $gradeA = Grade::where('grade_name', 'A+')->first();
        $gradeB = Grade::where('grade_name', 'B+')->first();
        $gradeF = Grade::where('grade_name', 'F')->first();

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject1->id, 'student_id' => $student1->id],
            [
                'obtained_mark' => 85.00,
                'practical_mark' => null,
                'viva_mark' => null,
                'total_mark' => 85.00,
                'grade_id' => $gradeA?->id,
                'approval_status' => 'approved',
                'approved_by' => $teacher->id,
                'approved_at' => now(),
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject1->id, 'student_id' => $student2->id],
            [
                'obtained_mark' => 65.00,
                'practical_mark' => null,
                'viva_mark' => null,
                'total_mark' => 65.00,
                'grade_id' => $gradeB?->id,
                'approval_status' => 'pending',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject1->id, 'student_id' => $student3->id],
            [
                'obtained_mark' => 25.00,
                'practical_mark' => null,
                'viva_mark' => null,
                'total_mark' => 25.00,
                'grade_id' => $gradeF?->id,
                'approval_status' => 'rejected',
                'remark' => 'Incomplete submission, re-evaluation required.',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject2->id, 'student_id' => $student1->id],
            [
                'obtained_mark' => 70.00,
                'practical_mark' => 20.00,
                'viva_mark' => 8.00,
                'total_mark' => 98.00,
                'grade_id' => $gradeA?->id,
                'approval_status' => 'approved',
                'approved_by' => $teacher->id,
                'approved_at' => now(),
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject2->id, 'student_id' => $student2->id],
            [
                'obtained_mark' => 55.00,
                'practical_mark' => 15.00,
                'viva_mark' => 5.00,
                'total_mark' => 75.00,
                'grade_id' => $gradeB?->id,
                'approval_status' => 'pending',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject2->id, 'student_id' => $student3->id],
            [
                'obtained_mark' => 30.00,
                'practical_mark' => 10.00,
                'viva_mark' => 3.00,
                'total_mark' => 43.00,
                'grade_id' => $gradeF?->id,
                'approval_status' => 'rejected',
                'remark' => 'Below minimum pass mark.',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject3->id, 'student_id' => $student1->id],
            [
                'obtained_mark' => 90.00,
                'practical_mark' => null,
                'viva_mark' => null,
                'total_mark' => 90.00,
                'grade_id' => $gradeA?->id,
                'approval_status' => 'pending',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject3->id, 'student_id' => $student2->id],
            [
                'obtained_mark' => 45.00,
                'practical_mark' => null,
                'viva_mark' => null,
                'total_mark' => 45.00,
                'grade_id' => Grade::where('grade_name', 'C')->first()?->id,
                'approval_status' => 'pending',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Mark::firstOrCreate(
            ['exam_subject_id' => $examSubject3->id, 'student_id' => $student3->id],
            [
                'obtained_mark' => 55.00,
                'practical_mark' => null,
                'viva_mark' => null,
                'total_mark' => 55.00,
                'grade_id' => $gradeB?->id,
                'approval_status' => 'pending',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $this->command?->info('Exam test data seeded successfully.');
    }

    private function ensureAcademicYear(): AcademicYear
    {
        return AcademicYear::firstOrCreate(
            ['is_current' => true],
            [
                'name' => '2025-2026',
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
            ]
        );
    }

    private function ensureSemestersExist(): void
    {
        if (Semester::count() > 0) {
            return;
        }

        $semesters = [
            ['name' => 'Semester 1', 'semester_number' => 1, 'description' => 'First semester'],
            ['name' => 'Semester 2', 'semester_number' => 2, 'description' => 'Second semester'],
        ];

        foreach ($semesters as $s) {
            Semester::firstOrCreate(
                ['semester_number' => $s['semester_number']],
                $s
            );
        }
    }

    private function ensureSection(): Section
    {
        $department = \App\Models\Department::firstOrCreate(
            ['code' => 'ETEST'],
            ['name' => 'Exam Testing Department']
        );

        $program = \App\Models\Program::firstOrCreate(
            ['code' => 'ETEST-BCS'],
            [
                'department_id' => $department->id,
                'name' => 'Exam Test Program',
                'duration_years' => 4,
            ]
        );

        return Section::firstOrCreate(
            ['name' => 'Exam Test Section', 'program_id' => $program->id],
            ['capacity' => 50]
        );
    }

    private function ensureShift(): Shift
    {
        return Shift::firstOrCreate(
            ['name' => 'Morning'],
            [
                'start_time' => '08:00',
                'end_time' => '13:00',
            ]
        );
    }

    private function ensureTeacherUser(): User
    {
        $user = User::firstOrCreate(
            ['email' => 'exam.test.teacher@school.edu'],
            [
                'name' => 'Exam Test Teacher',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('Teacher')) {
            $user->assignRole('Teacher');
        }

        return $user;
    }

    private function ensureGrades(): void
    {
        $this->call(GradeSeeder::class);
    }
}
