<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Content;
use App\Models\ContentComment;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ContentTestSeeder extends Seeder
{
    public function run(): void
    {
        $section = Section::firstOrCreate(
            ['name' => 'Content Test Section'],
            [
                'program_id' => $this->ensureProgram()->id,
                'capacity' => 50,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['code' => 'CTEST-101', 'program_id' => $section->program_id],
            [
                'name' => 'Content Testing 101',
                'type' => 'theory',
                'credits' => 3,
            ]
        );

        $teacher = $this->ensureTeacherUser();

        $content = Content::firstOrCreate(
            ['title' => 'Test PDF Content — Content Test Section'],
            [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'type' => 'pdf',
                'file_name' => 'test-document.pdf',
                'file_path' => 'contents/test-document.pdf',
                'file_size' => 102400,
                'mime_type' => 'application/pdf',
                'description' => 'A test PDF document for automated testing.',
                'status' => 'active',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Content::firstOrCreate(
            ['title' => 'Test Video Content — Content Test Section'],
            [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'type' => 'video',
                'file_name' => 'test-video.mp4',
                'file_path' => 'contents/test-video.mp4',
                'file_size' => 50000000,
                'mime_type' => 'video/mp4',
                'description' => 'A test video file for automated testing.',
                'status' => 'active',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Content::firstOrCreate(
            ['title' => 'Test Notes Content — Content Test Section'],
            [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'type' => 'notes',
                'file_name' => 'test-notes.docx',
                'file_path' => 'contents/test-notes.docx',
                'file_size' => 50000,
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'description' => 'A test notes document for automated testing.',
                'status' => 'active',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Content::firstOrCreate(
            ['title' => 'Processing Content — Content Test Section'],
            [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'type' => 'video',
                'file_name' => 'processing-video.mp4',
                'file_path' => 'contents/processing-video.mp4',
                'file_size' => 75000000,
                'mime_type' => 'video/mp4',
                'description' => 'A video still being processed.',
                'status' => 'processing',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        Content::firstOrCreate(
            ['title' => 'Failed Content — Content Test Section'],
            [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'type' => 'video',
                'file_name' => 'failed-video.mp4',
                'file_path' => 'contents/failed-video.mp4',
                'file_size' => 60000000,
                'mime_type' => 'video/mp4',
                'description' => 'A video that failed processing.',
                'status' => 'failed',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $testUser = $this->ensureTestUser();

        if (!ContentComment::where('content_id', $content->id)->exists()) {
            ContentComment::create([
                'content_id' => $content->id,
                'user_id' => $testUser->id,
                'comment' => 'Great material! Very helpful for understanding the topic.',
            ]);

            ContentComment::create([
                'content_id' => $content->id,
                'user_id' => $teacher->id,
                'comment' => 'Glad you found it useful. Let me know if you have questions.',
            ]);
        }

        $assignment = Assignment::firstOrCreate(
            ['title' => 'Test Assignment — Content Test Section'],
            [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'section_id' => $section->id,
                'description' => 'This is a test assignment used for automated feature testing.',
                'attachment' => null,
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'total_marks' => 100.00,
                'status' => 'active',
                'created_by' => $teacher->id,
                'updated_by' => $teacher->id,
            ]
        );

        $student = Student::firstOrCreate(
            ['admission_no' => 'CTEST-STU-001'],
            [
                'roll_no' => '9999',
                'first_name' => 'Content',
                'last_name' => 'TestStudent',
                'date_of_birth' => '2005-06-15',
                'gender' => 'male',
                'phone' => '0000000000',
                'email' => 'content.test.student@school.edu',
                'address' => '123 Test Street',
                'blood_group' => 'O+',
                'status' => 'active',
                'academic_year_id' => $this->ensureAcademicYear()->id,
                'program_id' => $section->program_id,
                'section_id' => $section->id,
                'shift_id' => $this->ensureShift()->id,
            ]
        );

        AssignmentSubmission::firstOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $student->id],
            [
                'submission_file' => 'submissions/test-submission.pdf',
                'submitted_at' => now(),
                'marks' => null,
                'feedback' => null,
                'status' => 'submitted',
            ]
        );

        $this->command?->info('Content test data seeded successfully.');
    }

    private function ensureProgram(): mixed
    {
        $department = \App\Models\Department::firstOrCreate(
            ['code' => 'CTEST'],
            ['name' => 'Content Testing Department']
        );

        return \App\Models\Program::firstOrCreate(
            ['code' => 'CTEST-BCS'],
            [
                'department_id' => $department->id,
                'name' => 'Content Test Program',
                'duration_years' => 4,
            ]
        );
    }

    private function ensureTeacherUser(): User
    {
        $user = User::firstOrCreate(
            ['email' => 'content.test.teacher@school.edu'],
            [
                'name' => 'Content Test Teacher',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('Teacher')) {
            $user->assignRole('Teacher');
        }

        return $user;
    }

    private function ensureTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'content.test.user@school.edu'],
            [
                'name' => 'Content Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    private function ensureAcademicYear(): mixed
    {
        return \App\Models\AcademicYear::firstOrCreate(
            ['is_current' => true],
            [
                'name' => '2025-2026',
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
            ]
        );
    }

    private function ensureShift(): mixed
    {
        return \App\Models\Shift::firstOrCreate(
            ['name' => 'Morning'],
            ['start_time' => '08:00', 'end_time' => '13:00']
        );
    }
}
