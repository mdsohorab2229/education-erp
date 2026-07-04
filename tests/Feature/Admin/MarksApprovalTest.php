<?php
declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\Mark;
use App\Models\User;
use Database\Seeders\ExamTestSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MarksApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $teacher;

    private User $noPermission;

    private int $pendingMarkId;

    private int $approvedMarkId;

    private int $rejectedMarkId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $this->ensureApprovalPermission();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->noPermission = User::factory()->create();

        $this->seed(ExamTestSeeder::class);

        $this->teacher = User::where('email', 'exam.test.teacher@school.edu')->firstOrFail();

        $this->pendingMarkId = Mark::where('approval_status', 'pending')->firstOrFail()->id;
        $this->approvedMarkId = Mark::where('approval_status', 'approved')->firstOrFail()->id;
        $this->rejectedMarkId = Mark::where('approval_status', 'rejected')->firstOrFail()->id;
    }

    // ---- Authentication ----

    public function test_guest_redirected_to_login_for_pending(): void
    {
        $this->get(route('admin.marks.approval.pending'))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_approve(): void
    {
        $this->post(route('admin.marks.approval.approve', 1))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_reject(): void
    {
        $this->post(route('admin.marks.approval.reject', 1))->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_for_reset(): void
    {
        $this->post(route('admin.marks.approval.reset', 1))->assertRedirect(route('login'));
    }

    // ---- Authorization ----

    public function test_user_without_permission_cannot_view_pending(): void
    {
        $this->actingAs($this->noPermission)
            ->getJson(route('admin.marks.approval.pending'))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_approve(): void
    {
        $this->actingAs($this->noPermission)
            ->postJson(route('admin.marks.approval.approve', 1))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_reject(): void
    {
        $this->actingAs($this->noPermission)
            ->postJson(route('admin.marks.approval.reject', 1))
            ->assertStatus(403);
    }

    public function test_user_without_permission_cannot_reset(): void
    {
        $this->actingAs($this->noPermission)
            ->postJson(route('admin.marks.approval.reset', 1))
            ->assertStatus(403);
    }

    // ---- Teacher cannot approve (no marks-approve permission) ----

    public function test_teacher_cannot_approve(): void
    {
        $this->actingAs($this->teacher)
            ->postJson(route('admin.marks.approval.approve', $this->pendingMarkId))
            ->assertStatus(403);
    }

    // ---- Pending List ----

    public function test_admin_can_view_pending_marks(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.approval.pending'));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['success', 'message', 'data']);
    }

    // ---- Approve ----

    public function test_admin_can_approve_pending_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.approve', $this->pendingMarkId), [
            'approval_status' => 'approved',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->pendingMarkId,
            'approval_status' => 'approved',
        ]);
    }

    public function test_cannot_approve_already_approved_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.approve', $this->approvedMarkId), [
            'approval_status' => 'approved',
        ]);

        $response->assertStatus(400);
    }

    public function test_cannot_approve_rejected_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.approve', $this->rejectedMarkId), [
            'approval_status' => 'approved',
        ]);

        $response->assertStatus(400);
    }

    // ---- Reject ----

    public function test_admin_can_reject_pending_mark_with_remark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.reject', $this->pendingMarkId), [
            'approval_status' => 'rejected',
            'remark' => 'Incomplete marks, please re-submit.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->pendingMarkId,
            'approval_status' => 'rejected',
            'remark' => 'Incomplete marks, please re-submit.',
        ]);
    }

    public function test_reject_requires_remark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.reject', $this->pendingMarkId), [
            'approval_status' => 'rejected',
            'remark' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('remark');
    }

    // ---- Reset ----

    public function test_admin_can_reset_approved_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.reset', $this->approvedMarkId));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->approvedMarkId,
            'approval_status' => 'pending',
        ]);
    }

    public function test_admin_can_reset_rejected_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.reset', $this->rejectedMarkId));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseHas('marks', [
            'id' => $this->rejectedMarkId,
            'approval_status' => 'pending',
        ]);
    }

    public function test_cannot_reset_pending_mark(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.reset', $this->pendingMarkId));

        $response->assertStatus(400);
    }

    // ---- Validation ----

    public function test_approve_requires_approval_status(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.approve', $this->pendingMarkId), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('approval_status');
    }

    public function test_reject_without_remark_fails(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson(route('admin.marks.approval.reject', $this->pendingMarkId), [
            'approval_status' => 'rejected',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('remark');
    }

    // ---- Resources (JSON structure) ----

    public function test_pending_list_has_mark_resource_structure(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.marks.approval.pending'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'obtained_mark',
                    'practical_mark',
                    'viva_mark',
                    'total_mark',
                    'approval_status',
                    'remark',
                ],
            ],
        ]);
    }

    // ---- Helpers ----

    private function ensureApprovalPermission(): void
    {
        Permission::firstOrCreate(['name' => 'marks-approve']);
        \Spatie\Permission\Models\Role::where('name', 'Admin')->first()
            ->givePermissionTo('marks-approve');
    }
}
