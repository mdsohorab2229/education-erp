<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\ExamType;
use App\Models\Grade;
use App\Models\Mark;
use App\Models\Program;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Shift;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\GradeSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MarksEntryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Exam $exam;

    private ExamSubject $examSubject;

    private Student $student;

    private Program $program;

    private Department $department;

    private AcademicYear $academicYear;

    private Section $section;

    private Shift $shift;

    private function setUpPermissions(): void
    {
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        Permission::firstOrCreate(['name' => 'marks-entry']);
        Permission::firstOrCreate(['name' => 'marks-approve']);

        \Spatie\Permission\Models\Role::where('name', 'Admin')->first()
            ->givePermissionTo(['marks-entry', 'marks-approve']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpPermissions();

        $this->seed(GradeSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->department = Department::factory()->create();
        $this->program = Program::factory()->create(['department_id' => $this->department->id]);
        $this->section = Section::factory()->create(['program_id' => $this->program->id]);
        $this->academicYear = AcademicYear::factory()->create();
        $semester = Semester::factory()->create();
        $this->shift = Shift::factory()->create();
        $examType = ExamType::factory()->create();
        $subject = Subject::factory()->create(['program_id' => $this->program->id]);
        $teacher = User::factory()->create();

        $this->exam = Exam::factory()->create([
            'exam_type_id' => $examType->id,
            'academic_year_id' => $this->academicYear->id,
            'semester_id' => $semester->id,
            'department_id' => $this->department->id,
            'program_id' => $this->program->id,
            'shift_id' => $this->shift->id,
            'section_id' => $this->section->id,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->examSubject = ExamSubject::factory()->create([
            'exam_id' => $this->exam->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'full_mark' => 100,
            'pass_mark' => 40,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->student = Student::factory()->create([
            'program_id' => $this->program->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'academic_year_id' => $this->academicYear->id,
        ]);
    }

    public function test_guest_cannot_access_marks_index(): void
    {
        $this->get(route('admin.marks.index'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_store_marks(): void
    {
        $this->post(route('admin.marks.store'), [])->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_marks(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('admin.marks.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_list_exam_marks(): void
    {
        $students = Student::factory()->count(3)->create([
            'program_id' => $this->program->id,
            'section_id' => $this->section->id,
            'shift_id' => $this->shift->id,
            'academic_year_id' => $this->academicYear->id,
            'group_id' => null,
        ]);

        foreach ($students as $s) {
            Mark::factory()->create([
                'exam_subject_id' => $this->examSubject->id,
                'student_id' => $s->id,
                'grade_id' => Grade::first()->id,
                'total_mark' => 50,
                'created_by' => $this->admin->id,
                'updated_by' => $this->admin->id,
            ]);
        }

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.marks.index'));

        $response->assertStatus(200);
        $response->assertSee('Marks Entry');
        $response->assertSee('Load Students');
    }

    public function test_admin_can_bulk_store_marks(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubject->id,
            'marks' => [
                [
                    'student_id' => $this->student->id,
                    'obtained_mark' => 75.50,
                    'practical_mark' => null,
                    'viva_mark' => null,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_bulk_store_validates_marks_array(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubject->id,
            'marks' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('marks');
    }

    public function test_bulk_store_validates_student_exists(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubject->id,
            'marks' => [
                ['student_id' => 99999, 'obtained_mark' => 50],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('marks.0.student_id');
    }

    public function test_admin_can_update_mark(): void
    {
        $mark = Mark::factory()->create([
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'grade_id' => Grade::first()->id,
            'practical_mark' => null,
            'viva_mark' => null,
            'total_mark' => 50,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->putJson(route('admin.marks.update', $mark->id), [
            'obtained_mark' => 85.00,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $mark->id,
            'obtained_mark' => 85.00,
        ]);
    }

    public function test_update_returns_404_for_missing_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->putJson(route('admin.marks.update', 99999), [
            'obtained_mark' => 50,
        ]);

        $response->assertStatus(404);
    }

    public function test_admin_can_load_students_for_exam_subject(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.load-students', [
            'exam_subject_id' => $this->examSubject->id,
        ]));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success', 'data' => ['exam_subject', 'marks'],
        ]);
    }

    public function test_load_students_returns_404_for_missing_exam_subject(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.load-students', [
            'exam_subject_id' => 99999,
        ]));

        $response->assertStatus(404);
        $response->assertJsonPath('success', false);
    }

    public function test_admin_can_load_students_via_index_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.marks.index', [
            'exam_subject_id' => $this->examSubject->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Marks Entry');
        $response->assertSee($this->examSubject->subject->name);
    }

    public function test_index_page_shows_empty_state_without_exam_subject(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.marks.index'));

        $response->assertStatus(200);
        $response->assertSee('Select an exam subject and click Load Students');
        $response->assertSee('Load Students');
    }

    public function test_marks_form_submits_correctly(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubject->id,
            'marks' => [
                [
                    'student_id' => $this->student->id,
                    'obtained_mark' => 60.00,
                    'practical_mark' => 15.00,
                    'viva_mark' => 10.00,
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertRedirect(route('admin.marks.index', ['exam_subject_id' => $this->examSubject->id]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('marks', [
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'obtained_mark' => 60.00,
        ]);
    }

    public function test_marks_api_store_returns_json(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.store'), [
            'exam_subject_id' => $this->examSubject->id,
            'marks' => [
                [
                    'student_id' => $this->student->id,
                    'obtained_mark' => 60.00,
                    'practical_mark' => 15.00,
                    'viva_mark' => 10.00,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'exam_subject_id' => $this->examSubject->id,
            'student_id' => $this->student->id,
            'obtained_mark' => 60.00,
        ]);
    }
}
