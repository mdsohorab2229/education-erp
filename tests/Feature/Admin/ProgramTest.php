<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('Teacher');

        $this->department = Department::create(['name' => 'Computer Science', 'code' => 'CSE']);
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.programs.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.programs.store'), [
            'department_id' => $this->department->id,
            'name' => 'BSc in Computer Science',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);

        $response->assertRedirect(route('admin.programs.index'));
        $this->assertDatabaseHas('programs', ['code' => 'CSE-BSC']);
    }

    public function test_admin_can_edit(): void
    {
        $this->actingAs($this->admin);

        $program = $this->department->programs()->create([
            'name' => 'BSc in CSE',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);

        $response = $this->put(route('admin.programs.update', $program->id), [
            'department_id' => $this->department->id,
            'name' => 'BSc in Computer Science & Engineering',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);

        $response->assertRedirect(route('admin.programs.index'));
        $this->assertDatabaseHas('programs', ['name' => 'BSc in Computer Science & Engineering']);
    }

    public function test_admin_can_delete(): void
    {
        $this->actingAs($this->admin);

        $program = $this->department->programs()->create([
            'name' => 'Temp Program',
            'code' => 'TMP',
            'duration_years' => 2,
        ]);

        $response = $this->delete(route('admin.programs.destroy', $program->id));

        $response->assertRedirect(route('admin.programs.index'));
        $this->assertSoftDeleted('programs', ['code' => 'TMP']);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.programs.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.programs.store'), [
            'department_id' => $this->department->id,
            'name' => 'BSc in CSE',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit(): void
    {
        $this->actingAs($this->teacher);

        $program = $this->department->programs()->create([
            'name' => 'BSc in CSE',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);

        $response = $this->put(route('admin.programs.update', $program->id), [
            'department_id' => $this->department->id,
            'name' => 'BSc in CSE (Updated)',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete(): void
    {
        $this->actingAs($this->teacher);

        $program = $this->department->programs()->create([
            'name' => 'Temp Program',
            'code' => 'TMP',
            'duration_years' => 2,
        ]);

        $response = $this->delete(route('admin.programs.destroy', $program->id));

        $response->assertStatus(403);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.programs.store'), [
            'department_id' => $this->department->id,
            'name' => '',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_code_must_be_unique(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.programs.store'), [
            'department_id' => $this->department->id,
            'name' => 'BSc in CSE',
            'code' => 'CSE-BSC',
            'duration_years' => 4,
        ]);
        $response = $this->post(route('admin.programs.store'), [
            'department_id' => $this->department->id,
            'name' => 'MSc in CSE',
            'code' => 'CSE-BSC',
            'duration_years' => 2,
        ]);

        $response->assertSessionHasErrors('code');
    }
}
