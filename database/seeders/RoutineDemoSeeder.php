<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Routine;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutineDemoSeeder extends Seeder
{
    private array $usedTeacherSlots = [];

    private array $usedRoomSlots = [];

    private array $usedSectionSlots = [];

    public function run(): void
    {
        $this->ensureRoomsExist();

        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $this->command?->warn('No current academic year found. Skipping routine demo seeder.');

            return;
        }

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $timeSlots = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00'];

        $semester = Semester::inRandomOrder()->first();
        $shifts = Shift::all();
        $sections = Section::with('program.department')->get();
        $subjects = Subject::with('program')->get();
        $teachers = User::role('Teacher')->get();
        $rooms = DB::table('rooms')->get();

        if ($teachers->isEmpty() || $rooms->isEmpty() || $sections->isEmpty()) {
            $this->command?->warn('Missing teachers, rooms, or sections. Skipping routine demo seeder.');

            return;
        }

        $routineCount = 0;

        foreach ($sections as $section) {
            $sectionSubjects = $subjects->filter(
                fn ($s) => $s->program_id === $section->program_id
            )->values();

            if ($sectionSubjects->isEmpty()) {
                continue;
            }

            $shift = $shifts->random();

            foreach ($days as $day) {
                $availableSlots = $timeSlots;

                foreach ($availableSlots as $startTime) {
                    if (fake()->boolean(40)) {
                        continue;
                    }

                    $subject = $sectionSubjects->random();
                    $teacher = $teachers->random();
                    $room = $rooms->random();

                    if ($this->isSlotTaken($teacher->id, $room->id, $section->id, $day, $startTime)) {
                        continue;
                    }

                    $endHour = ((int) substr($startTime, 0, 2)) + 1;
                    $endTime = sprintf('%02d:00', $endHour);

                    Routine::create([
                        'academic_year_id' => $academicYear->id,
                        'semester_id' => $semester?->id ?? Semester::factory(),
                        'department_id' => $section->program->department_id,
                        'program_id' => $section->program_id,
                        'shift_id' => $shift->id,
                        'group_id' => null,
                        'section_id' => $section->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'room_id' => $room->id,
                        'day_of_week' => $day,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'status' => 'active',
                    ]);

                    $this->markSlotTaken($teacher->id, $room->id, $section->id, $day, $startTime);
                    $routineCount++;

                    break;
                }
            }

            $this->command?->info("Routines generated for section: {$section->name}");
        }

        $this->command?->info("Total routines created: {$routineCount}");
    }

    private function ensureRoomsExist(): void
    {
        if (DB::table('rooms')->count() > 0) {
            return;
        }

        $rooms = [
            ['name' => 'Room 101', 'code' => 'R101', 'capacity' => 40, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Room 102', 'code' => 'R102', 'capacity' => 40, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Room 103', 'code' => 'R103', 'capacity' => 35, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Lab 1', 'code' => 'LAB1', 'capacity' => 25, 'type' => 'lab', 'status' => 'active'],
            ['name' => 'Lab 2', 'code' => 'LAB2', 'capacity' => 25, 'type' => 'lab', 'status' => 'active'],
            ['name' => 'Lecture Hall', 'code' => 'LH-A', 'capacity' => 100, 'type' => 'lecture_hall', 'status' => 'active'],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->insert($room);
        }
    }

    private function isSlotTaken(int $teacherId, int $roomId, int $sectionId, string $day, string $startTime): bool
    {
        return isset($this->usedTeacherSlots["{$teacherId}_{$day}_{$startTime}"])
            || isset($this->usedRoomSlots["{$roomId}_{$day}_{$startTime}"])
            || isset($this->usedSectionSlots["{$sectionId}_{$day}_{$startTime}"]);
    }

    private function markSlotTaken(int $teacherId, int $roomId, int $sectionId, string $day, string $startTime): void
    {
        $this->usedTeacherSlots["{$teacherId}_{$day}_{$startTime}"] = true;
        $this->usedRoomSlots["{$roomId}_{$day}_{$startTime}"] = true;
        $this->usedSectionSlots["{$sectionId}_{$day}_{$startTime}"] = true;
    }
}
