<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('Teacher');
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.departments.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.departments.store'), [
            'name' => 'Computer Science',
            'code' => 'CSE',
        ]);

        $response->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseHas('departments', ['code' => 'CSE']);
    }

    public function test_admin_can_edit(): void
    {
        $this->actingAs($this->admin);

        $department = Department::create(['name' => 'Computer Science', 'code' => 'CSE']);

        $response = $this->put(route('admin.departments.update', $department->id), [
            'name' => 'Computer Science & Engineering',
            'code' => 'CSE',
        ]);

        $response->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseHas('departments', ['name' => 'Computer Science & Engineering']);
    }

    public function test_admin_can_delete(): void
    {
        $this->actingAs($this->admin);

        $department = Department::create(['name' => 'Temp Dept', 'code' => 'TMP']);

        $response = $this->delete(route('admin.departments.destroy', $department->id));

        $response->assertRedirect(route('admin.departments.index'));
        $this->assertSoftDeleted('departments', ['code' => 'TMP']);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.departments.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.departments.store'), [
            'name' => 'Computer Science',
            'code' => 'CSE',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit(): void
    {
        $this->actingAs($this->teacher);

        $department = Department::create(['name' => 'Computer Science', 'code' => 'CSE']);

        $response = $this->put(route('admin.departments.update', $department->id), [
            'name' => 'Computer Science & Engineering',
            'code' => 'CSE',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete(): void
    {
        $this->actingAs($this->teacher);

        $department = Department::create(['name' => 'Temp Dept', 'code' => 'TMP']);

        $response = $this->delete(route('admin.departments.destroy', $department->id));

        $response->assertStatus(403);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.departments.store'), [
            'name' => '',
            'code' => 'CSE',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_code_must_be_unique(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.departments.store'), ['name' => 'CSE', 'code' => 'CSE']);
        $response = $this->post(route('admin.departments.store'), ['name' => 'EEE', 'code' => 'CSE']);

        $response->assertSessionHasErrors('code');
    }
}
