<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSemestersExist();

        $academicYear = AcademicYear::where('is_current', true)->first();
        $shifts = Shift::all();
        $sections = Section::with('program.department')->get();
        $subjects = Subject::with('program.department')->get();
        $students = Student::with('section')->get();

        $teacherUsers = $this->ensureTeacherUsers();

        for ($i = 0; $i < 5; $i++) {
            $date = now()->subDays(rand(1, 20))->format('Y-m-d');
            $section = $sections->random();
            $shift = $shifts->random();
            $subject = $subjects->filter(fn ($s) => $s->program_id === $section->program_id)->random();
            $teacher = $teacherUsers->random();

            $department = $section->program->department;
            $sectionStudents = $students->filter(fn ($s) => $s->section_id === $section->id);
            $total = $sectionStudents->count();

            if ($total === 0) {
                continue;
            }

            $present = (int) round($total * 0.8);
            $absent = (int) round($total * 0.1);
            $late = (int) round($total * 0.05);
            $leave = max(0, $total - $present - $absent - $late);

            $statuses = collect()
                ->pad(min($present, $total), 'P')
                ->pad(min($present + $absent, $total), 'A')
                ->pad(min($present + $absent + $late, $total), 'L')
                ->pad($total, 'LV')
                ->shuffle();

            $session = AttendanceSession::create([
                'academic_year_id' => $academicYear->id,
                'semester_id' => Semester::inRandomOrder()->value('id'),
                'department_id' => $department->id,
                'program_id' => $section->program_id,
                'shift_id' => $shift->id,
                'group_id' => $section->program->groups()->inRandomOrder()->first()?->id,
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'attendance_date' => $date,
                'total_students' => $total,
                'present_count' => $present,
                'absent_count' => $absent,
                'late_count' => $late,
                'leave_count' => $leave,
                'remarks' => fake()->optional(0.3)->sentence(),
                'status' => 'completed',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]);

            $sectionStudents->values()->each(function (Student $student, int $idx) use ($session, $statuses) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $student->id,
                    'attendance_status' => $statuses[$idx] ?? 'P',
                    'remark' => fake()->optional(0.1)->sentence(),
                    'checked_at' => $session->attendance_date->format('Y-m-d') . ' ' . fake()->time('H:i:s'),
                ]);
            });

            $this->command?->info("Session {$session->id}: {$section->name} on {$date} — {$total} students ({$present}P/{$absent}A/{$late}L/{$leave}LV)");
        }
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

    private function ensureTeacherUsers(): iterable
    {
        $teachers = [
            ['name' => 'David Wilson', 'email' => 'david.wilson@school.edu'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@school.edu'],
            ['name' => 'Michael Brown', 'email' => 'michael.brown@school.edu'],
            ['name' => 'Emily Davis', 'email' => 'emily.davis@school.edu'],
        ];

        $users = collect();

        foreach ($teachers as $t) {
            $user = User::firstOrCreate(
                ['email' => $t['email']],
                [
                    'name' => $t['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            if (!$user->hasRole('Teacher')) {
                $user->assignRole('Teacher');
            }

            $users->push($user);
        }

        return $users;
    }
}
