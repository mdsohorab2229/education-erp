<?php
declare(strict_types=1);

namespace Tests\Feature\Attendance;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\User;
use Database\Seeders\AttendanceTestSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private User $userWithoutPermission;

    private array $loadStudentsPayload;

    private int $sessionId;

    private int $student1Id;

    private int $student2Id;

    private int $student3Id;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->userWithoutPermission = User::factory()->create();

        $this->seed(AttendanceTestSeeder::class);

        $this->teacher = User::where('email', 'attendance.test.teacher@school.edu')->firstOrFail();

        $session = AttendanceSession::firstOrFail();
        $this->sessionId = $session->id;

        $records = AttendanceRecord::where('attendance_session_id', $session->id)
            ->orderBy('student_id')
            ->get();

        $this->student1Id = $records[0]->student_id;
        $this->student2Id = $records[1]->student_id;
        $this->student3Id = $records[2]->student_id;

        $this->loadStudentsPayload = [
            'academic_year_id' => $session->academic_year_id,
            'semester_id' => $session->semester_id,
            'department_id' => $session->department_id,
            'shift_id' => $session->shift_id,
            'group_id' => $session->group_id,
            'section_id' => $session->section_id,
            'subject_id' => $session->subject_id,
            'attendance_date' => $session->attendance_date->format('Y-m-d'),
        ];
    }

    public function test_guest_redirected_to_login_for_index(): void
    {
        $response = $this->get(route('attendance.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_load_students(): void
    {
        $response = $this->post(route('attendance.load'), $this->loadStudentsPayload);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_update(): void
    {
        $response = $this->post(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'P',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_bulk_update(): void
    {
        $response = $this->post(route('attendance.bulk-update'), [
            'attendance_session_id' => $this->sessionId,
            'attendance_status' => 'P',
            'student_ids' => [$this->student1Id, $this->student2Id],
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_session(): void
    {
        $response = $this->get(route('attendance.session', $this->sessionId));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_history(): void
    {
        $response = $this->get(route('attendance.history'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_index(): void
    {
        $this->actingAs($this->userWithoutPermission);

        $response = $this->get(route('attendance.index'));

        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_load_students(): void
    {
        $this->actingAs($this->userWithoutPermission);

        $response = $this->post(route('attendance.load'), $this->loadStudentsPayload);

        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_update(): void
    {
        $this->actingAs($this->userWithoutPermission);

        $response = $this->post(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'P',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_bulk_update(): void
    {
        $this->actingAs($this->userWithoutPermission);

        $response = $this->post(route('attendance.bulk-update'), [
            'attendance_session_id' => $this->sessionId,
            'attendance_status' => 'P',
            'student_ids' => [$this->student1Id, $this->student2Id],
        ]);

        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_view_session(): void
    {
        $this->actingAs($this->userWithoutPermission);

        $response = $this->get(route('attendance.session', $this->sessionId));

        $response->assertStatus(403);
    }

    public function test_user_without_permission_cannot_view_history(): void
    {
        $this->actingAs($this->userWithoutPermission);

        $response = $this->get(route('attendance.history'));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_index_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('attendance.index'));

        $response->assertStatus(200);
        $response->assertViewIs('attendance.index');
    }

    public function test_teacher_can_load_students_for_existing_session(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('attendance.load'), $this->loadStudentsPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Students loaded successfully.',
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'session' => ['id', 'attendance_date', 'summary', 'status'],
                'records' => [
                    '*' => ['student_id', 'attendance_status', 'remark', 'checked_at'],
                ],
                'summary' => ['total', 'present', 'absent', 'late', 'leave'],
            ],
        ]);
    }

    public function test_load_students_validates_required_fields(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('attendance.load'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'academic_year_id', 'semester_id', 'department_id',
            'shift_id', 'section_id', 'subject_id', 'attendance_date',
        ]);
    }

    public function test_load_students_validates_date_format(): void
    {
        $this->actingAs($this->teacher);
        $payload = $this->loadStudentsPayload;
        $payload['attendance_date'] = '15-03-2026';

        $response = $this->postJson(route('attendance.load'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('attendance_date');
    }

    public function test_load_students_validates_existing_ids(): void
    {
        $this->actingAs($this->teacher);
        $payload = $this->loadStudentsPayload;
        $payload['academic_year_id'] = 99999;

        $response = $this->postJson(route('attendance.load'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('academic_year_id');
    }

    public function test_admin_can_update_attendance_record(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'A',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Attendance updated successfully.',
            'data' => [
                'attendance' => [
                    'status' => 'A',
                ],
            ],
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'A',
        ]);
    }

    public function test_admin_can_update_attendance_with_remark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student2Id,
            'attendance_status' => 'L',
            'remark' => 'Medical appointment',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student2Id,
            'attendance_status' => 'L',
            'remark' => 'Medical appointment',
        ]);
    }

    public function test_update_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.update'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'attendance_session_id', 'student_id', 'attendance_status',
        ]);
    }

    public function test_update_validates_attendance_status_enum(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'X',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('attendance_status');
    }

    public function test_all_enum_status_values_are_accepted(): void
    {
        $this->actingAs($this->admin);

        $statuses = ['P', 'A', 'L', 'LV'];

        foreach ($statuses as $status) {
            $response = $this->postJson(route('attendance.update'), [
                'attendance_session_id' => $this->sessionId,
                'student_id' => $this->student1Id,
                'attendance_status' => $status,
            ]);

            $response->assertStatus(200);
            $response->assertJsonPath('data.attendance.status', $status);
        }
    }

    public function test_update_recalculates_summary(): void
    {
        $this->actingAs($this->admin);

        $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'A',
        ]);

        $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student3Id,
            'attendance_status' => 'P',
        ]);

        $session = AttendanceSession::find($this->sessionId);
        $this->assertEquals(1, $session->present_count);
        $this->assertEquals(2, $session->absent_count);
        $this->assertEquals(0, $session->late_count);
        $this->assertEquals(0, $session->leave_count);

        $this->actingAs($this->teacher);
        $response = $this->postJson(route('attendance.load'), $this->loadStudentsPayload);

        $response->assertJson([
            'data' => [
                'summary' => [
                    'present' => 1,
                    'absent' => 2,
                    'late' => 0,
                    'leave' => 0,
                ],
            ],
        ]);
    }

    public function test_admin_can_bulk_update_attendance(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.bulk-update'), [
            'attendance_session_id' => $this->sessionId,
            'attendance_status' => 'P',
            'student_ids' => [$this->student1Id, $this->student2Id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Bulk attendance updated successfully.',
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'P',
        ]);
        $this->assertDatabaseHas('attendance_records', [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student2Id,
            'attendance_status' => 'P',
        ]);
    }

    public function test_bulk_update_validates_required_fields(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.bulk-update'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'attendance_session_id', 'attendance_status', 'student_ids',
        ]);
    }

    public function test_bulk_update_validates_student_ids_array(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.bulk-update'), [
            'attendance_session_id' => $this->sessionId,
            'attendance_status' => 'P',
            'student_ids' => 'not-an-array',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('student_ids');
    }

    public function test_bulk_update_recalculates_summary(): void
    {
        $this->actingAs($this->admin);

        $this->postJson(route('attendance.bulk-update'), [
            'attendance_session_id' => $this->sessionId,
            'attendance_status' => 'A',
            'student_ids' => [$this->student1Id, $this->student2Id, $this->student3Id],
        ]);

        $session = AttendanceSession::find($this->sessionId);
        $this->assertEquals(3, $session->absent_count);
        $this->assertEquals(0, $session->present_count);
        $this->assertEquals(0, $session->late_count);
        $this->assertEquals(0, $session->leave_count);

        $response = $this->postJson(route('attendance.bulk-update'), [
            'attendance_session_id' => $this->sessionId,
            'attendance_status' => 'P',
            'student_ids' => [$this->student1Id, $this->student2Id, $this->student3Id],
        ]);

        $response->assertJson([
            'data' => [
                'summary' => [
                    'total' => 3,
                    'present' => 3,
                    'absent' => 0,
                    'late' => 0,
                    'leave' => 0,
                ],
            ],
        ]);
    }

    public function test_admin_can_view_session_details(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('attendance.session', $this->sessionId));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->sessionId,
                'status' => 'completed',
            ],
        ]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'attendance_date',
                'academic_year',
                'semester',
                'department',
                'program',
                'shift',
                'section',
                'subject',
                'summary' => [
                    'total_students',
                    'present_count',
                    'absent_count',
                    'late_count',
                    'leave_count',
                ],
                'status',
            ],
        ]);
    }

    public function test_session_not_found_returns_404(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('attendance.session', 99999));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Attendance session not found.',
        ]);
    }

    public function test_admin_can_view_history(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('attendance.history'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Attendance history retrieved successfully.',
        ]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'attendance_date', 'summary', 'status'],
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);
    }

    public function test_can_filter_history_by_date(): void
    {
        $this->actingAs($this->admin);

        $session = AttendanceSession::firstOrFail();

        $newSession = AttendanceSession::create([
            'academic_year_id' => $session->academic_year_id,
            'semester_id' => $session->semester_id,
            'department_id' => $session->department_id,
            'program_id' => $session->program_id,
            'shift_id' => $session->shift_id,
            'section_id' => $session->section_id,
            'subject_id' => $session->subject_id,
            'teacher_id' => $session->teacher_id,
            'attendance_date' => '2026-03-20',
            'total_students' => 2,
            'present_count' => 2,
            'absent_count' => 0,
            'late_count' => 0,
            'leave_count' => 0,
            'status' => 'active',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $response = $this->getJson(route('attendance.history', ['attendance_date' => '2026-03-20']));

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $this->assertEquals($newSession->id, $response->json('data.0.id'));
    }

    public function test_load_students_creates_records_for_new_session(): void
    {
        $this->actingAs($this->teacher);

        $payload = $this->loadStudentsPayload;
        $payload['attendance_date'] = '2026-04-01';

        $response = $this->postJson(route('attendance.load'), $payload);

        $response->assertStatus(200);

        $newSession = AttendanceSession::whereDate('attendance_date', '2026-04-01')->first();
        $this->assertNotNull($newSession);

        $records = AttendanceRecord::where('attendance_session_id', $newSession->id)->get();
        $this->assertCount(3, $records);

        foreach ($records as $record) {
            $this->assertNull($record->attendance_status);
        }
    }

    public function test_duplicate_session_returns_existing(): void
    {
        $this->actingAs($this->teacher);

        $this->postJson(route('attendance.load'), $this->loadStudentsPayload);
        $response = $this->postJson(route('attendance.load'), $this->loadStudentsPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'session' => [
                    'id' => $this->sessionId,
                ],
            ],
        ]);

        $sessions = AttendanceSession::whereDate('attendance_date', '2026-03-15')->get();
        $this->assertCount(1, $sessions);

        $records = AttendanceRecord::where('attendance_session_id', $this->sessionId)->get();
        $this->assertCount(3, $records);
    }

    public function test_update_upserts_existing_record(): void
    {
        $this->actingAs($this->admin);

        $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'L',
        ]);

        $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'LV',
        ]);

        $records = AttendanceRecord::where('attendance_session_id', $this->sessionId)
            ->where('student_id', $this->student1Id)
            ->get();

        $this->assertCount(1, $records);
        $this->assertEquals('LV', $records->first()->attendance_status);
    }

    public function test_checked_at_is_set_on_update(): void
    {
        $this->actingAs($this->admin);

        $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student3Id,
            'attendance_status' => 'P',
            'remark' => 'Updated late to present',
        ]);

        $record = AttendanceRecord::where('attendance_session_id', $this->sessionId)
            ->where('student_id', $this->student3Id)
            ->first();

        $this->assertNotNull($record->checked_at);
        $this->assertEquals('P', $record->attendance_status);
        $this->assertEquals('Updated late to present', $record->remark);
    }

    public function test_group_id_is_optional_in_load_students(): void
    {
        $this->actingAs($this->teacher);

        $payload = $this->loadStudentsPayload;
        unset($payload['group_id']);

        $response = $this->postJson(route('attendance.load'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['records', 'summary'],
        ]);
    }

    public function test_remark_is_optional_in_update(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'LV',
        ]);

        $response->assertStatus(200);

        $record = AttendanceRecord::where('attendance_session_id', $this->sessionId)
            ->where('student_id', $this->student1Id)
            ->first();

        $this->assertNull($record->remark);
    }

    public function test_history_respects_pagination(): void
    {
        $this->actingAs($this->admin);

        $session = AttendanceSession::firstOrFail();
        for ($i = 1; $i <= 5; $i++) {
            AttendanceSession::create([
                'academic_year_id' => $session->academic_year_id,
                'semester_id' => $session->semester_id,
                'department_id' => $session->department_id,
                'program_id' => $session->program_id,
                'shift_id' => $session->shift_id,
                'section_id' => $session->section_id,
                'subject_id' => $session->subject_id,
                'teacher_id' => $session->teacher_id,
                'attendance_date' => "2026-03-{$i}",
                'total_students' => 3,
                'present_count' => 2,
                'absent_count' => 1,
                'late_count' => 0,
                'leave_count' => 0,
                'status' => 'active',
                'created_by' => $this->admin->id,
                'updated_by' => $this->admin->id,
            ]);
        }

        $response = $this->getJson(route('attendance.history', ['per_page' => 2]));

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('meta.per_page'));
        $this->assertEquals(1, $response->json('meta.current_page'));
        $this->assertCount(2, $response->json('data'));
    }

    public function test_teacher_with_permission_can_access_attendance(): void
    {
        $this->actingAs($this->teacher);

        $this->get(route('attendance.index'))->assertStatus(200);
        $this->postJson(route('attendance.load'), $this->loadStudentsPayload)->assertStatus(200);
        $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => $this->student1Id,
            'attendance_status' => 'A',
        ])->assertStatus(200);
        $this->getJson(route('attendance.history'))->assertStatus(200);
    }

    public function test_invalid_bulk_update_rejects_empty_student_ids(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.bulk-update'), [
            'attendance_session_id' => $this->sessionId,
            'attendance_status' => 'P',
            'student_ids' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('student_ids');
    }

    public function test_validation_fails_for_non_existent_foreign_keys(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('attendance.update'), [
            'attendance_session_id' => $this->sessionId,
            'student_id' => 99999,
            'attendance_status' => 'P',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('student_id');
    }
}
