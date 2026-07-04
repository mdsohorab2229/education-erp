<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Program;
use App\Models\Routine;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoutineTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private User $userWithoutPermission;

    private array $validPayload;

    private \Illuminate\Support\Collection $roomIds;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('Teacher');

        $this->userWithoutPermission = User::factory()->create();

        $academicYear = AcademicYear::factory()->create(['is_current' => true]);
        $semester = Semester::factory()->create();
        $department = Department::factory()->create();
        $program = Program::factory()->create(['department_id' => $department->id]);
        $shift = Shift::factory()->create();
        $section = Section::factory()->create(['program_id' => $program->id]);
        $subject = Subject::factory()->create(['program_id' => $program->id]);

        DB::table('rooms')->insert([
            ['name' => 'Test Room 1', 'code' => 'T-RM1', 'capacity' => 40, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Test Room 2', 'code' => 'T-RM2', 'capacity' => 35, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Test Room 3', 'code' => 'T-RM3', 'capacity' => 30, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Test Room 4', 'code' => 'T-RM4', 'capacity' => 28, 'type' => 'classroom', 'status' => 'active'],
            ['name' => 'Test Lab 1', 'code' => 'T-LB1', 'capacity' => 25, 'type' => 'lab', 'status' => 'active'],
        ]);
        $this->roomIds = DB::table('rooms')->pluck('id');

        $this->validPayload = [
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'department_id' => $department->id,
            'program_id' => $program->id,
            'shift_id' => $shift->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_id' => $this->teacher->id,
            'room_id' => $this->roomIds[0],
            'day_of_week' => 'monday',
            'start_time' => '08:00',
            'end_time' => '09:00',
        ];
    }

    // -- Authentication (guests redirected to login) --

    public function test_guest_redirected_to_login_for_index(): void
    {
        $this->get(route('admin.routines.index'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_store(): void
    {
        $this->post(route('admin.routines.store'), $this->validPayload)->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_update(): void
    {
        $routine = $this->createRoutine();
        $this->put(route('admin.routines.update', $routine->id), $this->validPayload)->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_destroy(): void
    {
        $routine = $this->createRoutine();
        $this->delete(route('admin.routines.destroy', $routine->id))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_weekly(): void
    {
        $this->get(route('admin.routines.weekly', ['section_id' => 1]))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_teacher(): void
    {
        $this->get(route('admin.routines.teacher', ['teacher_id' => 1]))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_student(): void
    {
        $this->get(route('admin.routines.student', ['section_id' => 1]))->assertRedirect(route('login'));
    }

    // -- Authorization (user without permission) --

    public function test_user_without_permission_cannot_index(): void
    {
        $this->actingAs($this->userWithoutPermission)->getJson(route('admin.routines.index'))->assertStatus(403);
    }

    public function test_user_without_permission_cannot_store(): void
    {
        $this->actingAs($this->userWithoutPermission)->postJson(route('admin.routines.store'), $this->validPayload)->assertStatus(403);
    }

    public function test_user_without_permission_cannot_update(): void
    {
        $routine = $this->createRoutine();
        $this->actingAs($this->userWithoutPermission)->putJson(route('admin.routines.update', $routine->id), $this->validPayload)->assertStatus(403);
    }

    public function test_user_without_permission_cannot_destroy(): void
    {
        $routine = $this->createRoutine();
        $this->actingAs($this->userWithoutPermission)->deleteJson(route('admin.routines.destroy', $routine->id))->assertStatus(403);
    }

    public function test_user_without_permission_cannot_weekly(): void
    {
        $this->actingAs($this->userWithoutPermission)->getJson(route('admin.routines.weekly', ['section_id' => 1]))->assertStatus(403);
    }

    public function test_user_without_permission_cannot_teacher(): void
    {
        $this->actingAs($this->userWithoutPermission)->getJson(route('admin.routines.teacher', ['teacher_id' => 1]))->assertStatus(403);
    }

    public function test_user_without_permission_cannot_student(): void
    {
        $this->actingAs($this->userWithoutPermission)->getJson(route('admin.routines.student', ['section_id' => 1]))->assertStatus(403);
    }

    // -- Authorization (teacher) --

    public function test_teacher_can_index(): void
    {
        $this->actingAs($this->teacher)->getJson(route('admin.routines.index'))->assertStatus(200);
    }

    public function test_teacher_cannot_store(): void
    {
        $this->actingAs($this->teacher)->postJson(route('admin.routines.store'), $this->validPayload)->assertStatus(403);
    }

    public function test_teacher_cannot_update(): void
    {
        $routine = $this->createRoutine();
        $this->actingAs($this->teacher)->putJson(route('admin.routines.update', $routine->id), $this->validPayload)->assertStatus(403);
    }

    public function test_teacher_cannot_destroy(): void
    {
        $routine = $this->createRoutine();
        $this->actingAs($this->teacher)->deleteJson(route('admin.routines.destroy', $routine->id))->assertStatus(403);
    }

    // -- CRUD (admin) --

    public function test_admin_can_index(): void
    {
        $this->actingAs($this->admin);
        $this->createRoutine();
        $this->createRoutine(['day_of_week' => 'tuesday', 'start_time' => '09:00', 'end_time' => '10:00']);

        $response = $this->getJson(route('admin.routines.index'));
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_admin_can_store(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.routines.store'), $this->validPayload);
        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'day', 'start_time', 'end_time', 'status']]);
        $this->assertDatabaseHas('routines', ['day_of_week' => 'monday', 'start_time' => '08:00']);
    }

    public function test_admin_can_update(): void
    {
        $this->actingAs($this->admin);
        $routine = $this->createRoutine();

        $response = $this->putJson(route('admin.routines.update', $routine->id), [
            'day_of_week' => 'tuesday',
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('routines', ['id' => $routine->id, 'day_of_week' => 'tuesday']);
    }

    public function test_admin_can_destroy(): void
    {
        $this->actingAs($this->admin);
        $routine = $this->createRoutine();

        $response = $this->deleteJson(route('admin.routines.destroy', $routine->id));
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Routine deleted successfully.']);
        $this->assertSoftDeleted('routines', ['id' => $routine->id]);
    }

    // -- Conflict detection --

    public function test_teacher_double_booking_conflict(): void
    {
        $this->actingAs($this->admin);
        $this->postJson(route('admin.routines.store'), $this->validPayload);

        $response = $this->postJson(route('admin.routines.store'), $this->validPayload);
        $response->assertStatus(422);
    }

    public function test_room_double_booking_conflict(): void
    {
        $this->actingAs($this->admin);
        $this->postJson(route('admin.routines.store'), $this->validPayload);

        $payload = $this->validPayload;
        $payload['teacher_id'] = User::factory()->create()->id;

        $response = $this->postJson(route('admin.routines.store'), $payload);
        $response->assertStatus(422);
    }

    public function test_section_time_overlap_conflict(): void
    {
        $this->actingAs($this->admin);
        $this->postJson(route('admin.routines.store'), $this->validPayload);

        $payload = $this->validPayload;
        $payload['teacher_id'] = User::factory()->create()->id;
        $otherRoom = DB::table('rooms')->insertGetId([
            'name' => 'Other Room', 'code' => 'O-RM', 'capacity' => 30, 'type' => 'classroom', 'status' => 'active',
        ]);
        $payload['room_id'] = $otherRoom;

        $response = $this->postJson(route('admin.routines.store'), $payload);
        $response->assertStatus(422);
    }

    // -- Validation --

    public function test_validation_requires_all_required_fields(): void
    {
        $this->actingAs($this->admin);
        $response = $this->postJson(route('admin.routines.store'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'academic_year_id', 'semester_id', 'department_id',
            'shift_id', 'section_id', 'subject_id',
            'teacher_id', 'room_id', 'day_of_week',
            'start_time', 'end_time',
        ]);
    }

    public function test_validation_end_time_must_be_after_start_time(): void
    {
        $this->actingAs($this->admin);
        $payload = $this->validPayload;
        $payload['start_time'] = '10:00';
        $payload['end_time'] = '09:00';

        $response = $this->postJson(route('admin.routines.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('end_time');
    }

    public function test_validation_day_of_week_must_be_valid(): void
    {
        $this->actingAs($this->admin);
        $payload = $this->validPayload;
        $payload['day_of_week'] = 'invalid-day';

        $response = $this->postJson(route('admin.routines.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('day_of_week');
    }

    public function test_validation_foreign_keys_must_exist(): void
    {
        $this->actingAs($this->admin);
        $payload = $this->validPayload;
        $payload['academic_year_id'] = 99999;
        $payload['room_id'] = 99999;

        $response = $this->postJson(route('admin.routines.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['academic_year_id', 'room_id']);
    }

    // -- Query endpoints --

    public function test_weekly_routine_returns_filtered_results(): void
    {
        $this->actingAs($this->admin);
        $sectionId = $this->validPayload['section_id'];

        $otherSection = Section::factory()->create(['program_id' => $this->validPayload['program_id']]);

        $this->createRoutine(['section_id' => $sectionId]);
        $this->createRoutine(['section_id' => $sectionId, 'day_of_week' => 'tuesday']);
        $this->createRoutine(['section_id' => $otherSection->id]);

        $response = $this->getJson(route('admin.routines.weekly', ['section_id' => $sectionId]));
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_weekly_routine_filters_by_day(): void
    {
        $this->actingAs($this->admin);
        $sectionId = $this->validPayload['section_id'];

        $this->createRoutine(['section_id' => $sectionId, 'day_of_week' => 'monday']);
        $this->createRoutine(['section_id' => $sectionId, 'day_of_week' => 'tuesday', 'start_time' => '09:00', 'end_time' => '10:00']);

        $response = $this->getJson(route('admin.routines.weekly', ['section_id' => $sectionId, 'day_of_week' => 'monday']));
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_teacher_routine_returns_filtered_results(): void
    {
        $this->actingAs($this->admin);
        $teacher2 = User::factory()->create();
        $otherRoom = DB::table('rooms')->first()->id;
        $teacher2Payload = array_merge($this->validPayload, [
            'teacher_id' => $teacher2->id,
            'room_id' => $otherRoom,
            'day_of_week' => 'tuesday',
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $this->postJson(route('admin.routines.store'), $this->validPayload);
        $this->postJson(route('admin.routines.store'), $teacher2Payload);

        $response = $this->getJson(route('admin.routines.teacher', ['teacher_id' => $this->teacher->id]));
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_student_routine_returns_filtered_results(): void
    {
        $this->actingAs($this->admin);
        $sectionId = $this->validPayload['section_id'];

        $otherSection = Section::factory()->create(['program_id' => $this->validPayload['program_id']]);

        $this->createRoutine(['section_id' => $sectionId]);
        $this->createRoutine(['section_id' => $sectionId, 'day_of_week' => 'tuesday']);
        $this->createRoutine(['section_id' => $otherSection->id]);

        $response = $this->getJson(route('admin.routines.student', ['section_id' => $sectionId]));
        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    // -- Resource structure --

    public function test_routine_resource_exposes_expected_structure(): void
    {
        $this->actingAs($this->admin);
        $this->createRoutine();

        $response = $this->getJson(route('admin.routines.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [['id', 'day', 'start_time', 'end_time', 'status']],
        ]);
    }

    public function test_routine_resource_excludes_timestamps(): void
    {
        $this->actingAs($this->admin);
        $this->createRoutine();

        $response = $this->getJson(route('admin.routines.index'));
        $response->assertStatus(200);
        $response->assertJsonMissing(['created_at', 'updated_at', 'deleted_at']);
    }

    // -- Helpers --

    private int $routineIndex = 0;

    private function createRoutine(array $overrides = []): Routine
    {
        $this->routineIndex++;
        $data = array_merge($this->validPayload, $overrides);
        $data['teacher_id'] = User::factory()->create()->id;
        if (!isset($overrides['room_id'])) {
            $data['room_id'] = $this->roomIds[($this->routineIndex - 1) % count($this->roomIds)];
        }
        if (!isset($overrides['day_of_week'])) {
            $data['day_of_week'] = fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
        }
        if (!isset($overrides['start_time'])) {
            $data['start_time'] = fake()->randomElement(['08:00', '09:00', '10:00', '11:00', '14:00', '15:00']);
        }
        if (!isset($overrides['end_time'])) {
            $data['end_time'] = fake()->randomElement(['09:00', '10:00', '11:00', '12:00', '15:00', '16:00']);
        }

        return Routine::create($data);
    }
}
