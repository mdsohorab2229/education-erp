<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\User;
use Database\Seeders\ExamTestSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MarksEntryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private User $noPermission;

    private int $midtermExamId;

    private int $examSubjectId;

    private int $finalExamSubjectId;

    private int $student1Id;

    private int $student2Id;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->ensureMarksPermission();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->noPermission = User::factory()->create();

        $this->seed(ExamTestSeeder::class);

        $this->teacher = User::where('email', 'exam.test.teacher@school.edu')->firstOrFail();

        $midterm = Exam::where('title', 'Midterm — Exam Test Section (2025-2026)')->firstOrFail();
        $this->midtermExamId = $midterm->id;

        $this->examSubjectId = ExamSubject::where('exam_id', $midterm->id)->firstOrFail()->id;

        $final = Exam::where('title', 'Final — Exam Test Section (2025-2026)')->firstOrFail();
        $this->finalExamSubjectId = ExamSubject::where('exam_id', $final->id)->firstOrFail()->id;

        $studentIds = Mark::whereHas('examSubject', fn ($q) => $q->where('exam_id', $midterm->id))
            ->pluck('student_id')
            ->unique()
            ->values()
            ->toArray();
        $this->student1Id = $studentIds[0] ?? 1;
        $this->student2Id = $studentIds[1] ?? 2;
    }

    // ---- Authentication ----

    public function test_guest_redirected_to_login_for_index(): void
    {
        $this->get(route('admin.marks.index'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_load_students(): void
    {
        $this->get(route('admin.marks.load-students'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_store(): void
    {
        $this->post(route('admin.marks.store'), [])->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_update(): void
    {
        $this->put(route('admin.marks.update', 1), [])->assertRedirect(route('login'));
    }

    // ---- Authorization ----

    public function test_user_without_permission_cannot_access_index(): void
    {
        $this->actingAs($this->noPermission)
            ->getJson(route('admin.marks.index'))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_load_students(): void
    {
        $this->actingAs($this->noPermission)
            ->getJson(route('admin.marks.load-students'))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_store(): void
    {
        $this->actingAs($this->noPermission)
            ->postJson(route('admin.marks.store'), [])
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_update(): void
    {
        $this->actingAs($this->noPermission)
            ->putJson(route('admin.marks.update', 1), [])
            ->assertStatus(403);
    }

    // ---- Marks Index ----

    public function test_admin_can_view_marks_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.index', ['exam_id' => $this->midtermExamId]));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['success', 'message', 'data']);
    }

    // ---- Load Students ----

    public function test_admin_can_load_students(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.load-students', [
            'exam_subject_id' => $this->examSubjectId,
        ]));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success', 'message',
            'data' => ['exam_subject', 'marks'],
        ]);
    }

    // ---- Bulk Store ----

    public function test_admin_can_bulk_store_marks(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->finalExamSubjectId,
            'marks' => [
                [
                    'student_id' => $this->student1Id,
                    'obtained_mark' => 85.00,
                    'practical_mark' => null,
                    'viva_mark' => null,
                    'remark' => null,
                ],
                [
                    'student_id' => $this->student2Id,
                    'obtained_mark' => 65.00,
                    'practical_mark' => null,
                    'viva_mark' => null,
                    'remark' => 'Needs improvement',
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
    }

    public function test_bulk_store_requires_marks_array(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubjectId,
            'marks' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('marks');
    }

    public function test_bulk_store_requires_student_id(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubjectId,
            'marks' => [
                ['obtained_mark' => 50.00],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('marks.0.student_id');
    }

    // ---- Single Update ----

    public function test_admin_can_update_single_mark(): void
    {
        $this->actingAs($this->admin);

        $mark = Mark::first();

        $response = $this->putJson(route('admin.marks.update', $mark->id), [
            'obtained_mark' => 90.00,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }

    public function test_update_returns_404_for_missing_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->putJson(route('admin.marks.update', 99999), [
            'obtained_mark' => 50.00,
        ]);

        $response->assertStatus(404);
    }

    // ---- Boundary Marks ----

    public function test_can_store_zero_marks(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->finalExamSubjectId,
            'marks' => [
                [
                    'student_id' => $this->student1Id,
                    'obtained_mark' => 0,
                    'practical_mark' => null,
                    'viva_mark' => null,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
    }

    public function test_rejects_negative_marks(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubjectId,
            'marks' => [
                [
                    'student_id' => $this->student1Id,
                    'obtained_mark' => -5,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('marks.0.obtained_mark');
    }

    public function test_rejects_mark_exceeding_full_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubjectId,
            'marks' => [
                [
                    'student_id' => $this->student1Id,
                    'obtained_mark' => 999,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('marks.0.obtained_mark');
    }

    // ---- Resources (JSON structure) ----

    public function test_mark_resource_structure(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.index', ['exam_id' => $this->midtermExamId]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'obtained_mark', 'practical_mark', 'viva_mark', 'total_mark',
                    'approval_status', 'remark',
                ],
            ],
        ]);
    }

    // ---- Helpers ----

    private function ensureMarksPermission(): void
    {
        Permission::firstOrCreate(['name' => 'marks-entry']);
        \Spatie\Permission\Models\Role::where('name', 'Admin')->first()
            ->givePermissionTo('marks-entry');
    }
}
