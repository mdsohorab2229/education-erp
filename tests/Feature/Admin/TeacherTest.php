<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacherUser;

    private Department $department;

    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->teacherUser = User::factory()->create();
        $this->teacherUser->assignRole('Teacher');

        $this->department = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $this->subject = Subject::create([
            'program_id' => $this->department->programs()->create([
                'name' => 'Test Program',
                'code' => 'TPROG',
                'duration_years' => 4,
            ])->id,
            'name' => 'Data Structures',
            'code' => 'DS101',
            'credits' => 3.0,
            'type' => 'theory',
        ]);
    }

    private function validTeacherData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@school.edu',
            'phone' => '1234567890',
            'date_of_birth' => '1985-06-15',
            'gender' => 'male',
            'address' => '123 Main St',
            'designation' => 'Senior Lecturer',
            'joining_date' => '2020-01-15',
            'status' => 'active',
        ];
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.teachers.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_teacher(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.teachers.store'), $this->validTeacherData());

        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@school.edu',
        ]);
    }

    public function test_admin_can_show_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->get(route('admin.teachers.show', $teacher));

        $response->assertStatus(200);
        $response->assertSee($teacher->first_name);
    }

    public function test_admin_can_edit_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->get(route('admin.teachers.edit', $teacher->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->put(route('admin.teachers.update', $teacher->id), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@school.edu',
            'designation' => 'Professor',
        ]);

        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'designation' => 'Professor',
        ]);
    }

    public function test_admin_can_delete_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->delete(route('admin.teachers.destroy', $teacher->id));

        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertSoftDeleted('teachers', ['id' => $teacher->id]);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacherUser);

        $response = $this->get(route('admin.teachers.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_teacher(): void
    {
        $this->actingAs($this->teacherUser);

        $response = $this->post(route('admin.teachers.store'), $this->validTeacherData());

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_show_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $this->actingAs($this->teacherUser);

        $response = $this->get(route('admin.teachers.show', $teacher->id));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $this->actingAs($this->teacherUser);

        $response = $this->get(route('admin.teachers.edit', $teacher->id));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_update_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $this->actingAs($this->teacherUser);

        $response = $this->put(route('admin.teachers.update', $teacher->id), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $this->actingAs($this->teacherUser);

        $response = $this->delete(route('admin.teachers.destroy', $teacher->id));

        $response->assertStatus(403);
    }

    public function test_admin_can_assign_subjects_to_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->post(route('admin.teachers.subjects', $teacher->id), [
            'subject_ids' => [$this->subject->id],
        ]);

        $response->assertRedirect(route('admin.teachers.show', $teacher->id));
        $this->assertDatabaseHas('teacher_subjects', [
            'teacher_id' => $teacher->id,
            'subject_id' => $this->subject->id,
        ]);
    }

    public function test_admin_can_assign_departments_to_teacher(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->post(route('admin.teachers.departments', $teacher->id), [
            'department_ids' => [$this->department->id],
        ]);

        $response->assertRedirect(route('admin.teachers.show', $teacher->id));
        $this->assertDatabaseHas('teacher_departments', [
            'teacher_id' => $teacher->id,
            'department_id' => $this->department->id,
        ]);
    }

    public function test_subject_assignment_requires_valid_subject_ids(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->post(route('admin.teachers.subjects', $teacher->id), [
            'subject_ids' => [99999],
        ]);

        $response->assertSessionHasErrors('subject_ids.0');
    }

    public function test_department_assignment_requires_valid_department_ids(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $response = $this->post(route('admin.teachers.departments', $teacher->id), [
            'department_ids' => [99999],
        ]);

        $response->assertSessionHasErrors('department_ids.0');
    }

    public function test_teacher_cannot_assign_subjects(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $this->actingAs($this->teacherUser);

        $response = $this->post(route('admin.teachers.subjects', $teacher->id), [
            'subject_ids' => [$this->subject->id],
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_assign_departments(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $this->actingAs($this->teacherUser);

        $response = $this->post(route('admin.teachers.departments', $teacher->id), [
            'department_ids' => [$this->department->id],
        ]);

        $response->assertStatus(403);
    }

    public function test_first_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $data = $this->validTeacherData();
        $data['first_name'] = '';

        $response = $this->post(route('admin.teachers.store'), $data);

        $response->assertSessionHasErrors('first_name');
    }

    public function test_last_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $data = $this->validTeacherData();
        $data['last_name'] = '';

        $response = $this->post(route('admin.teachers.store'), $data);

        $response->assertSessionHasErrors('last_name');
    }

    public function test_employee_id_is_auto_generated(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.teachers.store'), $this->validTeacherData());
        $teacher = Teacher::first();

        $this->assertNotNull($teacher->employee_id);
        $this->assertStringStartsWith('EMP-', $teacher->employee_id);
    }
}
