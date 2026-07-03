<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTest extends TestCase
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

        $response = $this->get(route('admin.sections.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.sections.store'), [
            'program_id' => $this->program->id,
            'name' => 'A',
        ]);

        $response->assertRedirect(route('admin.sections.index'));
        $this->assertDatabaseHas('sections', ['name' => 'A', 'program_id' => $this->program->id]);
    }

    public function test_admin_can_edit(): void
    {
        $this->actingAs($this->admin);

        $section = $this->program->sections()->create(['name' => 'A']);

        $response = $this->put(route('admin.sections.update', $section->id), [
            'program_id' => $this->program->id,
            'name' => 'B',
        ]);

        $response->assertRedirect(route('admin.sections.index'));
        $this->assertDatabaseHas('sections', ['name' => 'B']);
    }

    public function test_admin_can_delete(): void
    {
        $this->actingAs($this->admin);

        $section = $this->program->sections()->create(['name' => 'Temp']);

        $response = $this->delete(route('admin.sections.destroy', $section->id));

        $response->assertRedirect(route('admin.sections.index'));
        $this->assertSoftDeleted('sections', ['name' => 'Temp']);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.sections.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.sections.store'), [
            'program_id' => $this->program->id,
            'name' => 'A',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit(): void
    {
        $this->actingAs($this->teacher);

        $section = $this->program->sections()->create(['name' => 'A']);

        $response = $this->put(route('admin.sections.update', $section->id), [
            'program_id' => $this->program->id,
            'name' => 'B',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete(): void
    {
        $this->actingAs($this->teacher);

        $section = $this->program->sections()->create(['name' => 'Temp']);

        $response = $this->delete(route('admin.sections.destroy', $section->id));

        $response->assertStatus(403);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.sections.store'), [
            'program_id' => $this->program->id,
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_be_unique_per_program(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.sections.store'), [
            'program_id' => $this->program->id,
            'name' => 'A',
        ]);
        $response = $this->post(route('admin.sections.store'), [
            'program_id' => $this->program->id,
            'name' => 'A',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
