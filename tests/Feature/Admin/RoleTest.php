<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
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

    public function test_admin_can_access_roles_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_role(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.roles.store'), [
            'name' => 'Editor',
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Editor']);
    }

    public function test_admin_can_edit_a_role(): void
    {
        $this->actingAs($this->admin);

        $role = Role::create(['name' => 'Editor']);

        $response = $this->put(route('admin.roles.update', $role->id), [
            'name' => 'Senior Editor',
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Senior Editor']);
    }

    public function test_admin_can_delete_a_role(): void
    {
        $this->actingAs($this->admin);

        $role = Role::create(['name' => 'Temporary']);

        $response = $this->delete(route('admin.roles.destroy', $role->id));

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseMissing('roles', ['name' => 'Temporary']);
    }

    public function test_teacher_cannot_access_roles_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.roles.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_a_role(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.roles.store'), [
            'name' => 'Editor',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit_a_role(): void
    {
        $this->actingAs($this->teacher);

        $role = Role::create(['name' => 'Editor']);

        $response = $this->put(route('admin.roles.update', $role->id), [
            'name' => 'Senior Editor',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete_a_role(): void
    {
        $this->actingAs($this->teacher);

        $role = Role::create(['name' => 'Temporary']);

        $response = $this->delete(route('admin.roles.destroy', $role->id));

        $response->assertStatus(403);
    }

    public function test_role_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.roles.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_role_name_must_be_unique(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.roles.store'), ['name' => 'Editor']);
        $response = $this->post(route('admin.roles.store'), ['name' => 'Editor']);

        $response->assertSessionHasErrors('name');
    }
}
