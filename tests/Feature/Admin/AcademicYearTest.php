<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearTest extends TestCase
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

        $response = $this->get(route('admin.academic-years.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.academic-years.store'), [
            'name' => '2024-2025',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        $response->assertRedirect(route('admin.academic-years.index'));
        $this->assertDatabaseHas('academic_years', ['name' => '2024-2025']);
    }

    public function test_admin_can_edit(): void
    {
        $this->actingAs($this->admin);

        $ay = AcademicYear::create(['name' => '2024-2025', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31']);

        $response = $this->put(route('admin.academic-years.update', $ay->id), [
            'name' => '2025-2026',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);

        $response->assertRedirect(route('admin.academic-years.index'));
        $this->assertDatabaseHas('academic_years', ['name' => '2025-2026']);
    }

    public function test_admin_can_delete(): void
    {
        $this->actingAs($this->admin);

        $ay = AcademicYear::create(['name' => 'Temp-Year', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31']);

        $response = $this->delete(route('admin.academic-years.destroy', $ay->id));

        $response->assertRedirect(route('admin.academic-years.index'));
        $this->assertSoftDeleted('academic_years', ['name' => 'Temp-Year']);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.academic-years.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_create(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.academic-years.store'), [
            'name' => '2024-2025',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit(): void
    {
        $this->actingAs($this->teacher);

        $ay = AcademicYear::create(['name' => '2024-2025', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31']);

        $response = $this->put(route('admin.academic-years.update', $ay->id), [
            'name' => '2025-2026',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete(): void
    {
        $this->actingAs($this->teacher);

        $ay = AcademicYear::create(['name' => 'Temp-Year', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31']);

        $response = $this->delete(route('admin.academic-years.destroy', $ay->id));

        $response->assertStatus(403);
    }

    public function test_name_is_required(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.academic-years.store'), [
            'name' => '',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_be_unique(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.academic-years.store'), [
            'name' => '2024-2025',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);
        $response = $this->post(route('admin.academic-years.store'), [
            'name' => '2024-2025',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
