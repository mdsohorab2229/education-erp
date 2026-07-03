<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Content;
use App\Models\ContentComment;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\ContentTestSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private User $userWithoutPermission;

    private int $pdfContentId;

    private int $videoContentId;

    private int $notesContentId;

    private int $assignmentId;

    private int $studentId;

    private int $sectionId;

    private int $subjectId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->userWithoutPermission = User::factory()->create();

        $this->seed(ContentTestSeeder::class);

        $this->teacher = User::where('email', 'content.test.teacher@school.edu')->firstOrFail();

        $this->pdfContentId = Content::where('title', 'Test PDF Content — Content Test Section')->firstOrFail()->id;
        $this->videoContentId = Content::where('title', 'Test Video Content — Content Test Section')->firstOrFail()->id;
        $this->notesContentId = Content::where('title', 'Test Notes Content — Content Test Section')->firstOrFail()->id;

        $this->assignmentId = Assignment::where('title', 'Test Assignment — Content Test Section')->firstOrFail()->id;

        $this->studentId = Student::where('admission_no', 'CTEST-STU-001')->firstOrFail()->id;

        $section = Section::where('name', 'Content Test Section')->firstOrFail();
        $this->sectionId = $section->id;
        $this->subjectId = Subject::where('code', 'CTEST-101')->firstOrFail()->id;
    }

    // -- Authentication (guests redirected to login) --

    public function test_guest_redirected_to_login_for_content_index(): void
    {
        $this->get(route('admin.contents.index'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_content_upload(): void
    {
        $this->post(route('admin.contents.upload'), [])->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_content_show(): void
    {
        $this->get(route('admin.contents.show', $this->pdfContentId))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_content_download(): void
    {
        $this->get(route('admin.contents.download', $this->pdfContentId))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_content_comments(): void
    {
        $this->get(route('admin.contents.comments', $this->pdfContentId))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_content_add_comment(): void
    {
        $this->post(route('admin.contents.comments.store', $this->pdfContentId), [])->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_content_delete(): void
    {
        $this->delete(route('admin.contents.destroy', $this->pdfContentId))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_content_by_section(): void
    {
        $this->get(route('admin.contents.by-section', ['section_id' => $this->sectionId]))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_assignment_index(): void
    {
        $this->get(route('admin.assignments.index'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_assignment_create(): void
    {
        $this->post(route('admin.assignments.create'), [])->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_assignment_show(): void
    {
        $this->get(route('admin.assignments.show', $this->assignmentId))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_assignment_submit(): void
    {
        $this->post(route('admin.assignments.submit'), [])->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_assignment_delete(): void
    {
        $this->delete(route('admin.assignments.destroy', $this->assignmentId))->assertRedirect(route('login'));
    }

    // -- Authorization (user without permission) --

    public function test_user_without_permission_cannot_list_content(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->getJson(route('admin.contents.index'))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_upload_content(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->postJson(route('admin.contents.upload'), [])
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_show_content(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->getJson(route('admin.contents.show', $this->pdfContentId))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_download_content(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->getJson(route('admin.contents.download', $this->pdfContentId))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_delete_content(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->deleteJson(route('admin.contents.destroy', $this->pdfContentId))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_list_comments(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->getJson(route('admin.contents.comments', $this->pdfContentId))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_add_comment(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->postJson(route('admin.contents.comments.store', $this->pdfContentId), ['comment' => 'test'])
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_list_assignments(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->getJson(route('admin.assignments.index'))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_create_assignment(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->postJson(route('admin.assignments.create'), [])
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_submit_assignment(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->postJson(route('admin.assignments.submit'), [])
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_delete_assignment(): void
    {
        $this->actingAs($this->userWithoutPermission)
            ->deleteJson(route('admin.assignments.destroy', $this->assignmentId))
            ->assertStatus(403);
    }

    // -- Permission checks (teacher with permission) --

    public function test_teacher_with_permission_can_list_content(): void
    {
        $this->actingAs($this->teacher)
            ->getJson(route('admin.contents.index'))
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_teacher_with_permission_can_upload_pdf_content(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('lecture-notes.pdf', 100, 'application/pdf');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'PDF Upload Test',
            'type' => 'pdf',
            'description' => 'Test PDF upload',
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'title', 'type', 'file_name', 'file_size', 'mime_type', 'status']]);
        $this->assertDatabaseHas('contents', ['title' => 'PDF Upload Test', 'type' => 'pdf']);
    }

    public function test_teacher_with_permission_can_upload_video_content(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('lecture.mp4', 500, 'video/mp4');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Video Upload Test',
            'type' => 'video',
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.type', 'video');
    }

    public function test_teacher_with_permission_can_upload_notes_content(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('notes.docx', 50, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Notes Upload Test',
            'type' => 'notes',
            'file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.type', 'notes');
    }

    public function test_teacher_can_show_content(): void
    {
        $this->actingAs($this->teacher)
            ->getJson(route('admin.contents.show', $this->pdfContentId))
            ->assertStatus(200)
            ->assertJsonPath('data.id', $this->pdfContentId)
            ->assertJsonStructure(['data' => ['id', 'title', 'type', 'file_name', 'file_size', 'mime_type', 'status']]);
    }

    public function test_teacher_can_update_content(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->putJson(route('admin.contents.update', $this->pdfContentId), [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('contents', ['id' => $this->pdfContentId, 'title' => 'Updated Title']);
    }

    // -- Download --

    public function test_teacher_can_download_content(): void
    {
        $this->actingAs($this->teacher);

        Storage::fake('public');
        Storage::disk('public')->put('contents/test-document.pdf', 'fake pdf content');

        $response = $this->get(route('admin.contents.download', $this->pdfContentId));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_teacher_can_delete_content(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->deleteJson(route('admin.contents.destroy', $this->pdfContentId));
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Content deleted successfully.']);
        $this->assertSoftDeleted('contents', ['id' => $this->pdfContentId]);
    }

    public function test_download_returns_404_for_nonexistent_content(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.download', 99999));
        $response->assertStatus(404);
    }

    // -- Comments --

    public function test_teacher_can_list_comments(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.comments', $this->pdfContentId));
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure(['data' => [['id', 'comment', 'created_at', 'user']]]);
    }

    public function test_teacher_can_add_comment(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.contents.comments.store', $this->pdfContentId), [
            'comment' => 'This is a test comment.',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.comment', 'This is a test comment.');
        $this->assertDatabaseHas('content_comments', [
            'content_id' => $this->pdfContentId,
            'comment' => 'This is a test comment.',
        ]);
    }

    public function test_add_comment_validates_required_comment(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.contents.comments.store', $this->pdfContentId), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('comment');
    }

    // -- Section Access Control --

    public function test_by_section_returns_filtered_content(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.by-section', ['section_id' => $this->sectionId]));
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_by_section_returns_empty_for_nonexistent_section(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.by-section', ['section_id' => 99999]));
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function test_by_teacher_returns_filtered_content(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.by-teacher', ['teacher_id' => $this->teacher->id]));
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    public function test_by_section_can_filter_by_type(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.by-section', [
            'section_id' => $this->sectionId,
            'type' => 'pdf',
        ]));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.type', 'pdf');
    }

    // -- Assignment Creation --

    public function test_teacher_can_create_assignment(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.assignments.create'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'New Test Assignment',
            'description' => 'Assignment description for testing.',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'total_marks' => 50,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'title', 'description', 'due_date', 'total_marks', 'status']]);
        $this->assertDatabaseHas('assignments', ['title' => 'New Test Assignment']);
    }

    public function test_create_assignment_validates_required_fields(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.assignments.create'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['teacher_id', 'subject_id', 'section_id', 'title', 'due_date', 'total_marks']);
    }

    public function test_create_assignment_validates_due_date_must_be_future(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.assignments.create'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Past Due Assignment',
            'due_date' => now()->subDays(1)->format('Y-m-d'),
            'total_marks' => 50,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('due_date');
    }

    public function test_teacher_can_update_assignment(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->putJson(route('admin.assignments.update', $this->assignmentId), [
            'title' => 'Updated Assignment Title',
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'total_marks' => 75,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('assignments', ['id' => $this->assignmentId, 'title' => 'Updated Assignment Title']);
    }

    public function test_teacher_can_delete_assignment(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->deleteJson(route('admin.assignments.destroy', $this->assignmentId));
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Assignment deleted successfully.']);
        $this->assertSoftDeleted('assignments', ['id' => $this->assignmentId]);
    }

    public function test_teacher_can_show_assignment(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.assignments.show', $this->assignmentId));
        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $this->assignmentId);
        $response->assertJsonStructure(['data' => ['id', 'title', 'description', 'due_date', 'total_marks', 'status']]);
    }

    // -- Assignment Submission --

    public function test_student_can_submit_assignment(): void
    {
        $this->actingAs($this->teacher);

        $newAssignment = Assignment::factory()->create([
            'section_id' => $this->sectionId,
            'subject_id' => $this->subjectId,
            'teacher_id' => $this->teacher->id,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'active',
        ]);

        $file = UploadedFile::fake()->create('submission.pdf', 200, 'application/pdf');

        $response = $this->postJson(route('admin.assignments.submit'), [
            'assignment_id' => $newAssignment->id,
            'student_id' => $this->studentId,
            'submission_file' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'status']]);
        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $newAssignment->id,
            'student_id' => $this->studentId,
            'status' => 'submitted',
        ]);
    }

    public function test_submit_assignment_validates_required_fields(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.assignments.submit'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['assignment_id', 'submission_file']);
    }

    public function test_teacher_can_mark_submission(): void
    {
        $this->actingAs($this->teacher);

        $newAssignment = Assignment::factory()->create([
            'section_id' => $this->sectionId,
            'subject_id' => $this->subjectId,
            'teacher_id' => $this->teacher->id,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'active',
        ]);

        $file = UploadedFile::fake()->create('submission.pdf', 200, 'application/pdf');
        $submitResponse = $this->postJson(route('admin.assignments.submit'), [
            'assignment_id' => $newAssignment->id,
            'student_id' => $this->studentId,
            'submission_file' => $file,
        ]);
        $submissionId = $submitResponse->json('data.id');

        $response = $this->putJson(route('admin.assignments.review', $submissionId), [
            'marks' => 85.50,
            'feedback' => 'Great work!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.marks', 85.5);
        $response->assertJsonPath('data.feedback', 'Great work!');
        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submissionId,
            'marks' => 85.5,
            'feedback' => 'Great work!',
            'status' => 'graded',
        ]);
    }

    // -- File Validation (Content Upload) --

    public function test_upload_validates_file_is_required(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'No File Test',
            'type' => 'pdf',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_upload_validates_title_is_required(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'type' => 'pdf',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    public function test_upload_validates_type_is_valid(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Invalid Type',
            'type' => 'invalid-type',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('type');
    }

    public function test_upload_validates_foreign_keys_exist(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => 99999,
            'subject_id' => 99999,
            'section_id' => 99999,
            'title' => 'Bad FK Test',
            'type' => 'pdf',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['teacher_id', 'subject_id', 'section_id']);
    }

    // -- MIME Validation --

    public function test_mime_validates_pdf_content_type(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('fake.mp4', 100, 'video/mp4');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Wrong MIME for PDF',
            'type' => 'pdf',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_mime_validates_video_content_type(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('fake.pdf', 500, 'application/pdf');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Wrong MIME for Video',
            'type' => 'video',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_mime_validates_notes_content_type(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('fake.mp4', 50, 'video/mp4');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Wrong MIME for Notes',
            'type' => 'notes',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    // -- Maximum File Size --

    public function test_pdf_upload_exceeds_max_size(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('large.pdf', 51201, 'application/pdf');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Oversized PDF',
            'type' => 'pdf',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_notes_upload_exceeds_max_size(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('large.docx', 10241, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Oversized Notes',
            'type' => 'notes',
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('file');
    }

    public function test_video_within_size_limit_succeeds(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('reasonable.mp4', 50000, 'video/mp4');

        $response = $this->postJson(route('admin.contents.upload'), [
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subjectId,
            'section_id' => $this->sectionId,
            'title' => 'Reasonable Video',
            'type' => 'video',
            'file' => $file,
        ]);

        $response->assertStatus(201);
    }

    public function test_submission_file_exceeds_max_size(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('large-submission.pdf', 51201, 'application/pdf');

        $response = $this->postJson(route('admin.assignments.submit'), [
            'assignment_id' => $this->assignmentId,
            'student_id' => $this->studentId,
            'submission_file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('submission_file');
    }

    // -- Content not found --

    public function test_show_returns_404_for_nonexistent_content(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.show', 99999));
        $response->assertStatus(404);
    }

    public function test_assignment_show_returns_404_for_nonexistent(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.assignments.show', 99999));
        $response->assertStatus(404);
    }

    // -- Resource structure --

    public function test_content_resource_exposes_expected_structure(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [['id', 'title', 'type', 'file_name', 'file_size', 'mime_type', 'status']],
        ]);
    }

    public function test_comment_resource_exposes_expected_structure(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.contents.comments', $this->pdfContentId));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [['id', 'comment', 'created_at', 'user']],
        ]);
    }

    // -- Upcoming assignments --

    public function test_upcoming_assignments_returns_filtered(): void
    {
        $this->actingAs($this->teacher);

        Assignment::factory()->create([
            'section_id' => $this->sectionId,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ]);
        Assignment::factory()->create([
            'section_id' => $this->sectionId,
            'due_date' => now()->addDays(20)->format('Y-m-d'),
        ]);

        $response = $this->getJson(route('admin.assignments.upcoming', ['section_id' => $this->sectionId]));
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_by_section_assignments_returns_filtered(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.assignments.by-section', ['section_id' => $this->sectionId]));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_by_teacher_assignments_returns_filtered(): void
    {
        $this->actingAs($this->teacher);

        $response = $this->getJson(route('admin.assignments.by-teacher', ['teacher_id' => $this->teacher->id]));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    // -- Assignment submission uniqueness --

    public function test_duplicate_submission_is_rejected(): void
    {
        $this->actingAs($this->teacher);

        $file = UploadedFile::fake()->create('first.pdf', 200, 'application/pdf');
        $this->postJson(route('admin.assignments.submit'), [
            'assignment_id' => $this->assignmentId,
            'student_id' => $this->studentId,
            'submission_file' => $file,
        ]);

        $file2 = UploadedFile::fake()->create('second.pdf', 200, 'application/pdf');
        $response = $this->postJson(route('admin.assignments.submit'), [
            'assignment_id' => $this->assignmentId,
            'student_id' => $this->studentId,
            'submission_file' => $file2,
        ]);

        $response->assertStatus(422);
    }
}
