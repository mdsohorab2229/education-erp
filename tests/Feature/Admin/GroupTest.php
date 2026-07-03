<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
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

        $response = $this->get(route('admin.groups.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.groups.store'), [
            'program_id' => $this->program->id,
            'name' => 'Group A',
        ]);

        $response->assertRedirect(route('admin.groups.index'));
        $this->assertDatabaseHas('groups', ['name' => 'Group A', 'program_id' => $this->program->id]);
    }

    public function test_admin_can_edit(): void
    {
        $this->actingAs($this->admin);

        $group = $this->program->groups()->create(['name' => 'Group A']);

        $response = $this->put(route('admin.groups.update', $group->id), [
            'program_id' => $this->program->id,
            'name' => 'Group B',
        ]);

        $response->assertRedirect(route('admin.groups.index'));
        $this->assertDatabaseHas('groups', ['name' => 'Group B']);
    }

    public function test_admin_can_delete(): void
    {
        $this->actingAs($this->admin);

        $group = $this->program->groups()->create(['name' => 'Temp Group']);

        $response = $this->delete(route('admin.groups.destroy', $group->id));

        $response->assertRedirect(route('admin.groups.index'));
        $this->assertSoftDeleted('groups', ['name' => 'Temp Group']);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.groups.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.groups.store'), [
            'program_id' => $this->program->id,
            'name' => 'Group A',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit(): void
    {
        $this->actingAs($this->teacher);

        $group = $this->program->groups()->create(['name' => 'Group A']);

        $response = $this->put(route('admin.groups.update', $group->id), [
            'program_id' => $this->program->id,
            'name' => 'Group B',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete(): void
    {
        $this->actingAs($this->teacher);

        $group = $this->program->groups()->create(['name' => 'Temp Group']);

        $response = $this->delete(route('admin.groups.destroy', $group->id));

        $response->assertStatus(403);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.groups.store'), [
            'program_id' => $this->program->id,
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_be_unique_per_program(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.groups.store'), [
            'program_id' => $this->program->id,
            'name' => 'Group A',
        ]);
        $response = $this->post(route('admin.groups.store'), [
            'program_id' => $this->program->id,
            'name' => 'Group A',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
