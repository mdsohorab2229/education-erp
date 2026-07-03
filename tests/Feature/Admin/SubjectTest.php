<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private \App\Models\Program $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('Teacher');

        $department = Department::create(['name' => 'Computer Science', 'code' => 'CSE']);
        $this->program = $department->programs()->create([
            'name' => 'BSc in CSE',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.subjects.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.subjects.store'), [
            'program_id' => $this->program->id,
            'name' => 'Data Structures',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response->assertRedirect(route('admin.subjects.index'));
        $this->assertDatabaseHas('subjects', ['code' => 'CSE201']);
    }

    public function test_admin_can_edit(): void
    {
        $this->actingAs($this->admin);

        $subject = Subject::create([
            'program_id' => $this->program->id,
            'name' => 'Data Structures',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response = $this->put(route('admin.subjects.update', $subject->id), [
            'program_id' => $this->program->id,
            'name' => 'Advanced Data Structures',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response->assertRedirect(route('admin.subjects.index'));
        $this->assertDatabaseHas('subjects', ['name' => 'Advanced Data Structures']);
    }

    public function test_admin_can_delete(): void
    {
        $this->actingAs($this->admin);

        $subject = Subject::create([
            'program_id' => $this->program->id,
            'name' => 'Temp Subject',
            'code' => 'TMP',
            'credits' => 1,
            'type' => 'lab',
        ]);

        $response = $this->delete(route('admin.subjects.destroy', $subject->id));

        $response->assertRedirect(route('admin.subjects.index'));
        $this->assertSoftDeleted('subjects', ['code' => 'TMP']);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.subjects.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.subjects.store'), [
            'program_id' => $this->program->id,
            'name' => 'Data Structures',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit(): void
    {
        $this->actingAs($this->teacher);

        $subject = Subject::create([
            'program_id' => $this->program->id,
            'name' => 'Data Structures',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response = $this->put(route('admin.subjects.update', $subject->id), [
            'program_id' => $this->program->id,
            'name' => 'Advanced DS',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete(): void
    {
        $this->actingAs($this->teacher);

        $subject = Subject::create([
            'program_id' => $this->program->id,
            'name' => 'Temp Subject',
            'code' => 'TMP',
            'credits' => 1,
            'type' => 'lab',
        ]);

        $response = $this->delete(route('admin.subjects.destroy', $subject->id));

        $response->assertStatus(403);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.subjects.store'), [
            'program_id' => $this->program->id,
            'name' => '',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_code_must_be_unique_per_program(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.subjects.store'), [
            'program_id' => $this->program->id,
            'name' => 'Data Structures',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);
        $response = $this->post(route('admin.subjects.store'), [
            'program_id' => $this->program->id,
            'name' => 'Algorithms',
            'code' => 'CSE201',
            'credits' => 3,
            'type' => 'theory',
        ]);

        $response->assertSessionHasErrors('code');
    }
}
