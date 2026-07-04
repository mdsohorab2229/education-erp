<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private User $noPermission;

    private ExamType $examType;

    private AcademicYear $academicYear;

    private Semester $semester;

    private Department $department;

    private Program $program;

    private Shift $shift;

    private Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->ensureExamPermissions();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('Teacher');

        $this->noPermission = User::factory()->create();

        $this->examType = ExamType::factory()->create();
        $this->academicYear = AcademicYear::factory()->create();
        $this->semester = Semester::factory()->create();
        $this->department = Department::factory()->create();
        $this->program = Program::factory()->create(['department_id' => $this->department->id]);
        $this->shift = Shift::factory()->create();
        $this->section = Section::factory()->create(['program_id' => $this->program->id]);
    }

    // ---- Authentication ----

    public function test_guest_redirected_to_login_for_index(): void
    {
        $this->get(route('admin.exams.index'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_store(): void
    {
        $this->post(route('admin.exams.store'), [])->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_show(): void
    {
        $this->get(route('admin.exams.show', 1))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_update(): void
    {
        $this->put(route('admin.exams.update', 1), [])->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_destroy(): void
    {
        $this->delete(route('admin.exams.destroy', 1))->assertRedirect(route('login'));
    }

    // ---- Authorization ----

    public function test_user_without_permission_cannot_access_index(): void
    {
        $this->actingAs($this->noPermission)
            ->getJson(route('admin.exams.index'))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_store(): void
    {
        $this->actingAs($this->noPermission)
            ->postJson(route('admin.exams.store'), [])
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_show(): void
    {
        $this->actingAs($this->noPermission)
            ->getJson(route('admin.exams.show', 1))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_update(): void
    {
        $this->actingAs($this->noPermission)
            ->putJson(route('admin.exams.update', 1), [])
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_destroy(): void
    {
        $this->actingAs($this->noPermission)
            ->deleteJson(route('admin.exams.destroy', 1))
            ->assertStatus(403);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher)
            ->getJson(route('admin.exams.index'))
            ->assertStatus(403);
    }

    public function test_teacher_cannot_store(): void
    {
        $this->actingAs($this->teacher)
            ->postJson(route('admin.exams.store'), [])
            ->assertStatus(403);
    }

    public function test_teacher_cannot_destroy(): void
    {
        $this->actingAs($this->teacher)
            ->deleteJson(route('admin.exams.destroy', 1))
            ->assertStatus(403);
    }

    // ---- CRUD ----

    public function test_admin_can_list_exams(): void
    {
        Exam::factory()->count(3)->create([
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'program_id' => $this->program->id,
            'shift_id' => $this->shift->id,
            'section_id' => $this->section->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.exams.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 'message', 'data', 'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('meta.total', 3);
    }

    public function test_admin_can_create_exam(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.exams.store'), [
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'program_id' => $this->program->id,
            'shift_id' => $this->shift->id,
            'section_id' => $this->section->id,
            'title' => 'Midterm Examination 2025',
            'start_date' => '2025-04-01',
            'end_date' => '2025-04-15',
            'status' => 'draft',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('exams', ['title' => 'Midterm Examination 2025']);
    }

    public function test_admin_can_view_exam(): void
    {
        $exam = Exam::factory()->create([
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'program_id' => $this->program->id,
            'shift_id' => $this->shift->id,
            'section_id' => $this->section->id,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.exams.show', $exam->id));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', $exam->id);
    }

    public function test_admin_can_update_exam(): void
    {
        $exam = Exam::factory()->create([
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'program_id' => $this->program->id,
            'shift_id' => $this->shift->id,
            'section_id' => $this->section->id,
            'title' => 'Original Title',
        ]);

        $this->actingAs($this->admin);

        $response = $this->putJson(route('admin.exams.update', $exam->id), [
            'title' => 'Updated Title',
            'start_date' => '2025-05-01',
            'end_date' => '2025-05-15',
            'status' => 'published',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'title' => 'Updated Title']);
    }

    public function test_admin_can_delete_exam(): void
    {
        $exam = Exam::factory()->create([
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'program_id' => $this->program->id,
            'shift_id' => $this->shift->id,
            'section_id' => $this->section->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->deleteJson(route('admin.exams.destroy', $exam->id));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertSoftDeleted('exams', ['id' => $exam->id]);
    }

    // ---- Validation ----

    public function test_store_requires_title(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.exams.store'), [
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'start_date' => '2025-04-01',
            'end_date' => '2025-04-15',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    public function test_store_requires_valid_dates(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.exams.store'), [
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'program_id' => $this->program->id,
            'title' => 'Test Exam',
            'start_date' => '2025-04-15',
            'end_date' => '2025-04-01',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('end_date');
    }

    public function test_store_requires_existing_foreign_keys(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.exams.store'), [
            'exam_type_id' => 99999,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'program_id' => $this->program->id,
            'title' => 'Test Exam',
            'start_date' => '2025-04-01',
            'end_date' => '2025-04-15',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('exam_type_id');
    }

    public function test_show_returns_404_for_missing_exam(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.exams.show', 99999));

        $response->assertStatus(404);
        $response->assertJsonPath('success', false);
    }

    public function test_pagination_works(): void
    {
        Exam::factory()->count(25)
            ->sequence(fn ($seq) => ['start_date' => now()->addDays($seq->index)->format('Y-m-d')])
            ->create([
                'exam_type_id' => $this->examType->id,
                'academic_year_id' => $this->academicYear->id,
                'semester_id' => $this->semester->id,
                'department_id' => $this->department->id,
                'program_id' => $this->program->id,
                'shift_id' => $this->shift->id,
                'section_id' => $this->section->id,
            ]);

        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.exams.index', ['per_page' => 10]));

        $response->assertStatus(200);
        $response->assertJsonPath('meta.per_page', 10);
        $response->assertJsonPath('meta.total', 25);
        $response->assertJsonPath('meta.last_page', 3);
    }

    // ---- Permissions (Teacher cannot access exam CRUD) ----

    public function test_teacher_cannot_update_exam(): void
    {
        $exam = Exam::factory()->create([
            'exam_type_id' => $this->examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $this->semester->id,
            'department_id' => $this->department->id,
            'program_id' => $this->program->id,
            'shift_id' => $this->shift->id,
            'section_id' => $this->section->id,
        ]);

        $this->actingAs($this->teacher);

        $this->putJson(route('admin.exams.update', $exam->id), ['title' => 'Hacked'])->assertStatus(403);
    }

    // ---- Helpers ----

    private function ensureExamPermissions(): void
    {
        $perms = ['exam-list', 'exam-create', 'exam-edit', 'exam-delete'];
        foreach ($perms as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        \Spatie\Permission\Models\Role::where('name', 'Admin')->first()
            ->givePermissionTo($perms);
    }
}
