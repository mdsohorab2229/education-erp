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

class MarksApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Mark $pendingMark;

    private Mark $approvedMark;

    private Mark $rejectedMark;

    private function setUpPermissions(): void
    {
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        Permission::firstOrCreate(['name' => 'marks-approve']);
        Permission::firstOrCreate(['name' => 'marks-entry']);

        \Spatie\Permission\Models\Role::where('name', 'Admin')->first()
            ->givePermissionTo(['marks-approve', 'marks-entry']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpPermissions();

        $this->seed(GradeSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $department = Department::factory()->create();
        $program = Program::factory()->create(['department_id' => $department->id]);
        $section = Section::factory()->create(['program_id' => $program->id]);
        $academicYear = AcademicYear::factory()->create();
        $semester = Semester::factory()->create();
        $shift = Shift::factory()->create();
        $examType = ExamType::factory()->create();
        $subject = Subject::factory()->create(['program_id' => $program->id]);
        $teacher = User::factory()->create();

        $exam = Exam::factory()->create([
            'exam_type_id' => $examType->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'department_id' => $department->id,
            'program_id' => $program->id,
            'shift_id' => $shift->id,
            'section_id' => $section->id,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $examSubject = ExamSubject::factory()->create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $grade = Grade::first();

        $student1 = Student::factory()->create([
            'program_id' => $program->id,
            'section_id' => $section->id,
            'shift_id' => $shift->id,
            'academic_year_id' => $academicYear->id,
            'group_id' => null,
        ]);

        $student2 = Student::factory()->create([
            'program_id' => $program->id,
            'section_id' => $section->id,
            'shift_id' => $shift->id,
            'academic_year_id' => $academicYear->id,
            'group_id' => null,
        ]);

        $student3 = Student::factory()->create([
            'program_id' => $program->id,
            'section_id' => $section->id,
            'shift_id' => $shift->id,
            'academic_year_id' => $academicYear->id,
            'group_id' => null,
        ]);

        $this->pendingMark = Mark::factory()->create([
            'exam_subject_id' => $examSubject->id,
            'student_id' => $student1->id,
            'grade_id' => $grade->id,
            'total_mark' => 50,
            'approval_status' => 'pending',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->approvedMark = Mark::factory()->approved()->create([
            'exam_subject_id' => $examSubject->id,
            'student_id' => $student2->id,
            'grade_id' => $grade->id,
            'total_mark' => 85,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $this->rejectedMark = Mark::factory()->rejected()->create([
            'exam_subject_id' => $examSubject->id,
            'student_id' => $student3->id,
            'grade_id' => $grade->id,
            'total_mark' => 30,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);
    }

    public function test_guest_cannot_access_pending(): void
    {
        $this->get(route('admin.marks.approval.pending'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_pending(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('admin.marks.approval.pending'))
            ->assertStatus(403);
    }

    public function test_admin_can_view_pending_marks(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.approval.pending'));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success', 'message', 'data', 'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
    }

    public function test_admin_can_approve_pending_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.approve', $this->pendingMark->id),
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->pendingMark->id,
            'approval_status' => 'approved',
        ]);
    }

    public function test_admin_cannot_approve_already_approved_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.approve', $this->approvedMark->id),
        );

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }

    public function test_admin_can_reject_pending_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.reject', $this->pendingMark->id),
            ['remark' => 'Low performance'],
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->pendingMark->id,
            'approval_status' => 'rejected',
        ]);
    }

    public function test_reject_requires_remark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.reject', $this->pendingMark->id),
            [],
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('remark');
    }

    public function test_admin_can_reset_approved_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.reset', $this->approvedMark->id),
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->approvedMark->id,
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function test_admin_can_reset_rejected_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.reset', $this->rejectedMark->id),
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->rejectedMark->id,
            'approval_status' => 'pending',
        ]);
    }

    public function test_admin_cannot_reset_pending_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.reset', $this->pendingMark->id),
        );

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
    }

    public function test_approve_returns_404_for_missing_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(
            route('admin.marks.approval.approve', 99999),
        );

        $response->assertStatus(404);
    }
}
