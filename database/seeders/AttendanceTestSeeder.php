<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Department;
use App\Models\Group;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSemestersExist();

        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'is_current' => true,
            ]
        );

        $department = Department::firstOrCreate(
            ['code' => 'CS'],
            ['name' => 'Computer Science']
        );

        $program = Program::firstOrCreate(
            ['code' => 'BCS'],
            [
                'department_id' => $department->id,
                'name' => 'Bachelor of Computer Science',
                'duration_years' => 4,
            ]
        );

        $section = Section::firstOrCreate(
            ['name' => 'A', 'program_id' => $program->id],
            ['capacity' => 50]
        );

        $shift = Shift::firstOrCreate(
            ['name' => 'Morning'],
            [
                'start_time' => '08:00',
                'end_time' => '13:00',
            ]
        );

        $subject = Subject::firstOrCreate(
            ['code' => 'CS101', 'program_id' => $program->id],
            [
                'name' => 'Introduction to Programming',
                'program_id' => $program->id,
                'credits' => 3,
                'type' => 'theory',
            ]
        );

        $group = Group::firstOrCreate(
            ['name' => 'G1', 'program_id' => $program->id],
            [
                'name' => 'G1',
                'program_id' => $program->id,
                'capacity' => 30,
            ]
        );

        $teacher = User::firstOrCreate(
            ['email' => 'attendance.test.teacher@school.edu'],
            [
                'name' => 'Test Teacher',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$teacher->hasRole('Teacher')) {
            $teacher->assignRole('Teacher');
        }

        $student1 = Student::firstOrCreate(
            ['admission_no' => 'TEST-ATT-001'],
            [
                'roll_no' => '9991',
                'first_name' => 'Test',
                'last_name' => 'StudentOne',
                'date_of_birth' => '2005-06-15',
                'gender' => 'male',
                'phone' => '1111111111',
                'email' => 'test.student1@school.edu',
                'address' => 'Test Address 1',
                'blood_group' => 'O+',
                'status' => 'active',
                'academic_year_id' => $academicYear->id,
                'program_id' => $program->id,
                'section_id' => $section->id,
                'shift_id' => $shift->id,
                'group_id' => $group->id,
            ]
        );

        $student2 = Student::firstOrCreate(
            ['admission_no' => 'TEST-ATT-002'],
            [
                'roll_no' => '9992',
                'first_name' => 'Test',
                'last_name' => 'StudentTwo',
                'date_of_birth' => '2005-08-20',
                'gender' => 'female',
                'phone' => '2222222222',
                'email' => 'test.student2@school.edu',
                'address' => 'Test Address 2',
                'blood_group' => 'A+',
                'status' => 'active',
                'academic_year_id' => $academicYear->id,
                'program_id' => $program->id,
                'section_id' => $section->id,
                'shift_id' => $shift->id,
                'group_id' => $group->id,
            ]
        );

        $student3 = Student::firstOrCreate(
            ['admission_no' => 'TEST-ATT-003'],
            [
                'roll_no' => '9993',
                'first_name' => 'Test',
                'last_name' => 'StudentThree',
                'date_of_birth' => '2005-11-10',
                'gender' => 'male',
                'phone' => '3333333333',
                'email' => 'test.student3@school.edu',
                'address' => 'Test Address 3',
                'blood_group' => 'B+',
                'status' => 'active',
                'academic_year_id' => $academicYear->id,
                'program_id' => $program->id,
                'section_id' => $section->id,
                'shift_id' => $shift->id,
                'group_id' => $group->id,
            ]
        );

        $semester1 = Semester::where('semester_number', 1)->firstOrFail();

        $session = AttendanceSession::create([
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester1->id,
            'department_id' => $department->id,
            'program_id' => $program->id,
            'shift_id' => $shift->id,
            'group_id' => $group->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'attendance_date' => '2026-03-15',
            'total_students' => 3,
            'present_count' => 1,
            'absent_count' => 1,
            'late_count' => 1,
            'leave_count' => 0,
            'remarks' => 'Test session for feature tests',
            'status' => 'completed',
            'created_by' => $teacher->id,
            'updated_by' => $teacher->id,
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $student1->id,
            'attendance_status' => 'P',
            'remark' => null,
            'checked_at' => '2026-03-15 09:00:00',
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $student2->id,
            'attendance_status' => 'A',
            'remark' => null,
            'checked_at' => '2026-03-15 09:05:00',
        ]);

        AttendanceRecord::create([
            'attendance_session_id' => $session->id,
            'student_id' => $student3->id,
            'attendance_status' => 'L',
            'remark' => 'Traffic delay',
            'checked_at' => '2026-03-15 09:30:00',
        ]);
    }

    private function ensureSemestersExist(): void
    {
        if (Semester::count() > 0) {
            return;
        }

        $semesters = [
            ['name' => 'Semester 1', 'semester_number' => 1, 'description' => 'First semester'],
            ['name' => 'Semester 2', 'semester_number' => 2, 'description' => 'Second semester'],
            ['name' => 'Semester 3', 'semester_number' => 3, 'description' => 'Third semester'],
            ['name' => 'Semester 4', 'semester_number' => 4, 'description' => 'Fourth semester'],
            ['name' => 'Semester 5', 'semester_number' => 5, 'description' => 'Fifth semester'],
            ['name' => 'Semester 6', 'semester_number' => 6, 'description' => 'Sixth semester'],
        ];

        foreach ($semesters as $s) {
            Semester::firstOrCreate(
                ['semester_number' => $s['semester_number']],
                $s
            );
        }
    }
}
