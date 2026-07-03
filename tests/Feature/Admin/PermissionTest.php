<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionTest extends TestCase
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

    public function test_admin_can_access_permissions_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.permissions.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_permission(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.permissions.store'), [
            'name' => 'manage-users',
        ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseHas('permissions', ['name' => 'manage-users']);
    }

    public function test_admin_can_edit_a_permission(): void
    {
        $this->actingAs($this->admin);

        $permission = Permission::create(['name' => 'manage-users']);

        $response = $this->put(route('admin.permissions.update', $permission->id), [
            'name' => 'manage-roles',
        ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseHas('permissions', ['name' => 'manage-roles']);
    }

    public function test_admin_can_delete_a_permission(): void
    {
        $this->actingAs($this->admin);

        $permission = Permission::create(['name' => 'temp-perm']);

        $response = $this->delete(route('admin.permissions.destroy', $permission->id));

        $response->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseMissing('permissions', ['name' => 'temp-perm']);
    }

    public function test_teacher_cannot_access_permissions_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.permissions.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create_a_permission(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.permissions.store'), [
            'name' => 'manage-users',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit_a_permission(): void
    {
        $this->actingAs($this->teacher);

        $permission = Permission::create(['name' => 'manage-users']);

        $response = $this->put(route('admin.permissions.update', $permission->id), [
            'name' => 'manage-roles',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete_a_permission(): void
    {
        $this->actingAs($this->teacher);

        $permission = Permission::create(['name' => 'temp-perm']);

        $response = $this->delete(route('admin.permissions.destroy', $permission->id));

        $response->assertStatus(403);
    }

    public function test_permission_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.permissions.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_permission_name_must_be_unique(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.permissions.store'), ['name' => 'manage-users']);
        $response = $this->post(route('admin.permissions.store'), ['name' => 'manage-users']);

        $response->assertSessionHasErrors('name');
    }
}
