<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Program;
use App\Models\Routine;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoutineTestSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'is_current' => true,
            ]
        );

        $semester = Semester::firstOrCreate(
            ['semester_number' => 1],
            ['name' => 'Semester 1', 'description' => 'First semester']
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
            ['name' => 'Test Section', 'program_id' => $program->id],
            ['capacity' => 50]
        );

        $shift = Shift::firstOrCreate(
            ['name' => 'Morning'],
            ['start_time' => '08:00', 'end_time' => '13:00']
        );

        $subject = Subject::firstOrCreate(
            ['code' => 'R-TEST-101', 'program_id' => $program->id],
            [
                'name' => 'Routine Test Subject',
                'credits' => 3,
                'type' => 'theory',
            ]
        );

        $subject2 = Subject::firstOrCreate(
            ['code' => 'R-TEST-102', 'program_id' => $program->id],
            [
                'name' => 'Routine Test Lab',
                'credits' => 2,
                'type' => 'lab',
            ]
        );

        $teacher = User::firstOrCreate(
            ['email' => 'routine.test.teacher@school.edu'],
            [
                'name' => 'Routine Test Teacher',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$teacher->hasRole('Teacher')) {
            $teacher->assignRole('Teacher');
        }

        $teacher2 = User::firstOrCreate(
            ['email' => 'routine.test.teacher2@school.edu'],
            [
                'name' => 'Routine Test Teacher 2',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$teacher2->hasRole('Teacher')) {
            $teacher2->assignRole('Teacher');
        }

        $roomIds = $this->ensureRoomsExist();

        $routines = [
            [
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'department_id' => $department->id,
                'program_id' => $program->id,
                'shift_id' => $shift->id,
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'room_id' => $roomIds[0],
                'day_of_week' => 'monday',
                'start_time' => '08:00',
                'end_time' => '09:00',
                'status' => 'active',
            ],
            [
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'department_id' => $department->id,
                'program_id' => $program->id,
                'shift_id' => $shift->id,
                'section_id' => $section->id,
                'subject_id' => $subject2->id,
                'teacher_id' => $teacher->id,
                'room_id' => $roomIds[1],
                'day_of_week' => 'monday',
                'start_time' => '09:00',
                'end_time' => '10:00',
                'status' => 'active',
            ],
            [
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'department_id' => $department->id,
                'program_id' => $program->id,
                'shift_id' => $shift->id,
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher2->id,
                'room_id' => $roomIds[2],
                'day_of_week' => 'tuesday',
                'start_time' => '08:00',
                'end_time' => '09:00',
                'status' => 'active',
            ],
            [
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'department_id' => $department->id,
                'program_id' => $program->id,
                'shift_id' => $shift->id,
                'section_id' => $section->id,
                'subject_id' => $subject2->id,
                'teacher_id' => $teacher2->id,
                'room_id' => $roomIds[0],
                'day_of_week' => 'wednesday',
                'start_time' => '10:00',
                'end_time' => '11:00',
                'status' => 'active',
            ],
        ];

        foreach ($routines as $routine) {
            $existing = Routine::where('teacher_id', $routine['teacher_id'])
                ->where('day_of_week', $routine['day_of_week'])
                ->where('start_time', $routine['start_time'])
                ->first();

            if (!$existing) {
                Routine::create($routine);
            }
        }
    }

    private function ensureRoomsExist(): array
    {
        $rooms = DB::table('rooms')->pluck('id')->toArray();

        if (!empty($rooms)) {
            return $rooms;
        }

        $roomData = [
            ['name' => 'Test Room 1', 'code' => 'T-RM-1', 'capacity' => 40, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Test Room 2', 'code' => 'T-RM-2', 'capacity' => 35, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Test Lab 1', 'code' => 'T-LAB-1', 'capacity' => 25, 'type' => 'lab', 'status' => 'active'],
        ];

        foreach ($roomData as $data) {
            DB::table('rooms')->insert($data);
        }

        return DB::table('rooms')->pluck('id')->toArray();
    }
}
