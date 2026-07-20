<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Assignment;
use App\Models\Department;
use App\Models\Program;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private User $userWithoutPermission;

    private Section $section;

    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->userWithoutPermission = User::factory()->create();

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('Teacher');

        $department = Department::factory()->create(['code' => 'CS', 'name' => 'Computer Science']);
        $program = Program::factory()->create(['department_id' => $department->id, 'code' => 'BCS']);
        $this->section = Section::factory()->create(['program_id' => $program->id]);
        $this->subject = Subject::factory()->create(['program_id' => $program->id]);
    }

    // -- Authentication --

    public function test_guest_redirected_to_login_for_index_view(): void
    {
        $this->get(route('admin.assignment.index'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_create_view(): void
    {
        $this->get(route('admin.assignment.create'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_json_index(): void
    {
        $this->getJson(route('admin.assignments.index'))->assertUnauthorized();
    }

    // -- Authorization --

    public function test_user_without_permission_cannot_access_index_view(): void
    {
        $this->actingAs($this->userWithoutPermission);
        $this->get(route('admin.assignment.index'))->assertStatus(403);
    }

    public function test_user_without_permission_cannot_create(): void
    {
        $this->actingAs($this->userWithoutPermission);
        $this->postJson(route('admin.assignments.create'), [
            'title' => 'Test',
            'teacher_id' => $this->admin->id,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'total_marks' => 100,
        ])->assertStatus(403);
    }

    public function test_user_without_permission_cannot_update(): void
    {
        $this->actingAs($this->userWithoutPermission);
        $assignment = Assignment::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
        ]);
        $this->putJson(route('admin.assignments.update', $assignment->id), ['title' => 'Hacked'])->assertStatus(403);
    }

    public function test_user_without_permission_cannot_delete(): void
    {
        $this->actingAs($this->userWithoutPermission);
        $assignment = Assignment::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
        ]);
        $this->deleteJson(route('admin.assignments.destroy', $assignment->id))->assertStatus(403);
    }

    // -- Teacher permissions --

    public function test_teacher_can_access_index_view(): void
    {
        $this->actingAs($this->teacher);
        $this->get(route('admin.assignment.index'))->assertStatus(200);
    }

    public function test_teacher_can_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.assignments.create'), [
            'title' => 'Teacher Assignment',
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'total_marks' => 100,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('assignments', ['title' => 'Teacher Assignment']);
    }

    public function test_teacher_can_edit(): void
    {
        $this->actingAs($this->teacher);
        $assignment = Assignment::factory()->create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
        ]);
        $this->putJson(route('admin.assignments.update', $assignment->id), ['title' => 'Edited'])->assertStatus(200);
    }

    public function test_teacher_can_delete(): void
    {
        $this->actingAs($this->teacher);
        $assignment = Assignment::factory()->create([
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
        ]);
        $this->deleteJson(route('admin.assignments.destroy', $assignment->id))->assertStatus(200);
    }

    // -- Admin CRUD --

    public function test_admin_can_access_index_view(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('admin.assignment.index'))->assertStatus(200);
    }

    public function test_admin_can_access_create_view(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('admin.assignment.create'))->assertStatus(200);
    }

    public function test_admin_can_list_assignments(): void
    {
        Assignment::factory()->count(3)->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
        ]);

        $this->actingAs($this->admin);
        $response = $this->getJson(route('admin.assignments.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_admin_can_create_assignment(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.assignments.create'), [
            'teacher_id' => $this->admin->id,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
            'title' => 'Midterm Assignment',
            'description' => 'Solve all problems.',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'total_marks' => 100,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.title', 'Midterm Assignment');
        $this->assertDatabaseHas('assignments', ['title' => 'Midterm Assignment']);
    }

    public function test_admin_can_show_assignment(): void
    {
        $this->actingAs($this->admin);
        $assignment = Assignment::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
        ]);

        $response = $this->getJson(route('admin.assignments.show', $assignment->id));

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $assignment->id);
    }

    public function test_admin_can_update_assignment(): void
    {
        $this->actingAs($this->admin);
        $assignment = Assignment::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
        ]);

        $response = $this->putJson(route('admin.assignments.update', $assignment->id), [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.title', 'Updated Title');
        $this->assertDatabaseHas('assignments', ['title' => 'Updated Title']);
    }

    public function test_admin_can_delete_assignment(): void
    {
        $this->actingAs($this->admin);
        $assignment = Assignment::factory()->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
        ]);

        $response = $this->deleteJson(route('admin.assignments.destroy', $assignment->id));

        $response->assertStatus(200);
        $this->assertSoftDeleted('assignments', ['id' => $assignment->id]);
    }

    // -- Validation --

    public function test_title_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.assignments.create'), [
            'teacher_id' => $this->admin->id,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'total_marks' => 100,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    public function test_due_date_must_be_future(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.assignments.create'), [
            'teacher_id' => $this->admin->id,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
            'title' => 'Past Due',
            'due_date' => now()->subDay()->format('Y-m-d'),
            'total_marks' => 100,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('due_date');
    }

    public function test_teacher_id_must_exist(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.assignments.create'), [
            'teacher_id' => 99999,
            'subject_id' => $this->subject->id,
            'section_id' => $this->section->id,
            'title' => 'Test',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'total_marks' => 100,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('teacher_id');
    }

    // -- Search / Export / Print --

    public function test_search_returns_paginated_results(): void
    {
        Assignment::factory()->count(5)->create([
            'section_id' => $this->section->id,
            'subject_id' => $this->subject->id,
        ]);

        $this->actingAs($this->admin);
        $response = $this->getJson(route('admin.assignments.search'));

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_export_returns_placeholder(): void
    {
        $this->actingAs($this->admin);
        $response = $this->getJson(route('admin.assignments.export'));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Export functionality coming soon.');
    }

    public function test_print_returns_placeholder(): void
    {
        $this->actingAs($this->admin);
        $response = $this->getJson(route('admin.assignments.print'));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Print functionality coming soon.');
    }

    public function test_showing_nonexistent_assignment_returns_404(): void
    {
        $this->actingAs($this->admin);
        $this->getJson(route('admin.assignments.show', 99999))->assertStatus(404);
    }
}
