<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Program;
use App\Models\Section;
use App\Models\Shift;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private AcademicYear $academicYear;

    private Program $program;

    private Section $section;

    private Shift $shift;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('Teacher');

        $department = Department::create(['name' => 'Science', 'code' => 'SCI']);

        $this->academicYear = AcademicYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);

        $this->program = Program::create([
            'department_id' => $department->id,
            'name' => 'Computer Science',
            'code' => 'CS',
            'duration_years' => 4,
        ]);

        $this->section = Section::create([
            'program_id' => $this->program->id,
            'name' => 'A',
            'capacity' => 30,
        ]);

        $this->shift = Shift::create([
            'name' => 'Morning',
            'start_time' => '08:00',
            'end_time' => '13:00',
        ]);
    }

    private function validAdmissionData(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => '2005-06-15',
            'gender' => 'male',
            'phone' => '1234567890',
            'email' => 'john@example.com',
            'address' => '123 Main St',
            'blood_group' => 'O+',
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'guardian' => [
                'name' => 'Jane Doe',
                'relation' => 'mother',
                'phone' => '0987654321',
                'email' => 'jane@example.com',
                'occupation' => 'Teacher',
                'address' => '456 Oak Ave',
            ],
        ];
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.students.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_admit_student(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.students.store'), $this->validAdmissionData());

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);
        $this->assertDatabaseHas('guardians', [
            'name' => 'Jane Doe',
            'relation' => 'mother',
        ]);
    }

    public function test_admin_can_show_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $response = $this->get(route('admin.students.show', $student));

        $response->assertStatus(200);
        $response->assertSee($student->first_name);
    }

    public function test_admin_can_edit_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $response = $this->get(route('admin.students.edit', $student->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_update_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $response = $this->put(route('admin.students.update', $student->id), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'date_of_birth' => '2006-03-20',
            'gender' => 'female',
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'guardian' => [
                'name' => 'Bob Smith',
                'relation' => 'father',
                'phone' => '1112223333',
            ],
        ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
    }

    public function test_admin_can_change_student_status(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();
        $this->assertEquals('active', $student->status);

        $response = $this->put(route('admin.students.status', $student->id), [
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => 'inactive',
        ]);
    }

    public function test_admin_can_delete_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $response = $this->delete(route('admin.students.destroy', $student->id));

        $response->assertRedirect(route('admin.students.index'));
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_teacher_cannot_access_index(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.students.index'));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_admit_student(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->post(route('admin.students.store'), $this->validAdmissionData());

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_show_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.students.show', $student->id));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_edit_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $this->actingAs($this->teacher);

        $response = $this->get(route('admin.students.edit', $student->id));

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_update_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $this->actingAs($this->teacher);

        $response = $this->put(route('admin.students.update', $student->id), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'date_of_birth' => '2006-03-20',
            'gender' => 'female',
            'academic_year_id' => $this->academicYear->id,
            'program_id' => $this->program->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'guardian' => [
                'name' => 'Bob Smith',
                'relation' => 'father',
                'phone' => '1112223333',
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_delete_student(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $this->actingAs($this->teacher);

        $response = $this->delete(route('admin.students.destroy', $student->id));

        $response->assertStatus(403);
    }

    public function test_admission_requires_first_name(): void
    {
        $this->actingAs($this->admin);

        $data = $this->validAdmissionData();
        $data['first_name'] = '';

        $response = $this->post(route('admin.students.store'), $data);

        $response->assertSessionHasErrors('first_name');
    }

    public function test_admission_requires_guardian_name(): void
    {
        $this->actingAs($this->admin);

        $data = $this->validAdmissionData();
        $data['guardian']['name'] = '';

        $response = $this->post(route('admin.students.store'), $data);

        $response->assertSessionHasErrors('guardian.name');
    }

    public function test_admission_requires_valid_gender(): void
    {
        $this->actingAs($this->admin);

        $data = $this->validAdmissionData();
        $data['gender'] = 'invalid';

        $response = $this->post(route('admin.students.store'), $data);

        $response->assertSessionHasErrors('gender');
    }

    public function test_admission_roll_no_must_be_unique_per_shift(): void
    {
        $this->actingAs($this->admin);

        $data = $this->validAdmissionData();
        $data['roll_no'] = '101';

        $this->post(route('admin.students.store'), $data);

        $response = $this->post(route('admin.students.store'), $data);

        $response->assertSessionHasErrors('roll_no');
    }

    public function test_status_must_be_valid(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.students.store'), $this->validAdmissionData());
        $student = Student::first();

        $response = $this->put(route('admin.students.status', $student->id), [
            'status' => 'invalid-status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_date_of_birth_must_be_before_today(): void
    {
        $this->actingAs($this->admin);

        $data = $this->validAdmissionData();
        $data['date_of_birth'] = now()->addDay()->format('Y-m-d');

        $response = $this->post(route('admin.students.store'), $data);

        $response->assertSessionHasErrors('date_of_birth');
    }
}
