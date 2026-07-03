<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Shift;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTest extends TestCase
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

        $response = $this->get(route('admin.shifts.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.shifts.store'), [
            'name' => 'Morning',
        ]);

        $response->assertRedirect(route('admin.shifts.index'));
        $this->assertDatabaseHas('shifts', ['name' => 'Morning']);
    }

    public function test_admin_can_edit(): void
    {
        $this->actingAs($this->admin);

        $shift = Shift::create(['name' => 'Morning']);

        $response = $this->put(route('admin.shifts.update', $shift->id), [
            'name' => 'Evening',
        ]);

        $response->assertRedirect(route('admin.shifts.index'));
        $this->assertDatabaseHas('shifts', ['name' => 'Evening']);
    }

    public function test_admin_can_delete(): void
    {
        $this->actingAs($this->admin);

        $shift = Shift::create(['name' => 'Temp']);

        $response = $this->delete(route('admin.shifts.destroy', $shift->id));

        $response->assertRedirect(route('admin.shifts.index'));
        $this->assertSoftDeleted('shifts', ['name' => 'Temp']);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.shifts.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.shifts.store'), [
            'name' => 'Morning',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit(): void
    {
        $this->actingAs($this->teacher);

        $shift = Shift::create(['name' => 'Morning']);

        $response = $this->put(route('admin.shifts.update', $shift->id), [
            'name' => 'Evening',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete(): void
    {
        $this->actingAs($this->teacher);

        $shift = Shift::create(['name' => 'Temp']);

        $response = $this->delete(route('admin.shifts.destroy', $shift->id));

        $response->assertStatus(403);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.shifts.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_be_unique(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.shifts.store'), ['name' => 'Morning']);
        $response = $this->post(route('admin.shifts.store'), ['name' => 'Morning']);

        $response->assertSessionHasErrors('name');
    }
}
