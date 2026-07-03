<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $sections = Section::with('program')->get();

        if ($sections->isEmpty()) {
            $this->command?->warn('No sections found. Skipping AssignmentDemoSeeder.');

            return;
        }

        $teachers = User::role('Teacher')->get();

        if ($teachers->isEmpty()) {
            $this->command?->warn('No teacher users found. Skipping AssignmentDemoSeeder.');

            return;
        }

        $students = Student::with('section')->get();

        if ($students->isEmpty()) {
            $this->command?->warn('No students found. Skipping AssignmentDemoSeeder.');

            return;
        }

        $assignmentTemplates = [
            [
                'title' => 'Homework Assignment',
                'description' => 'Complete the problems from Chapter 2 and submit your work in PDF format. Show all steps clearly.',
                'total_marks' => 20,
            ],
            [
                'title' => 'Lab Report',
                'description' => 'Write a detailed lab report following the standard format: objective, methodology, observations, and conclusions.',
                'total_marks' => 30,
            ],
            [
                'title' => 'Research Essay',
                'description' => 'Write a 1500-word essay on a topic related to the current module. Include at least 5 references from peer-reviewed sources.',
                'total_marks' => 100,
            ],
            [
                'title' => 'Quiz Preparation Worksheet',
                'description' => 'Complete the worksheet to prepare for next week\'s quiz. This is a formative assessment.',
                'total_marks' => 10,
            ],
            [
                'title' => 'Group Project — Phase 1',
                'description' => 'Submit the proposal and outline for your group project. Include problem statement, scope, and timeline.',
                'total_marks' => 50,
            ],
        ];

        $assignmentCount = 0;
        $submissionCount = 0;

        foreach ($sections as $section) {
            $sectionSubjects = $section->program?->subjects ?? collect();
            $sectionStudents = $students->filter(fn (Student $s) => $s->section_id === $section->id);

            if ($sectionSubjects->isEmpty()) {
                continue;
            }

            $teacher = $teachers->random();
            $subject = $sectionSubjects->random();
            $templatesToUse = fake()->randomElements($assignmentTemplates, fake()->numberBetween(1, 3));

            foreach ($templatesToUse as $template) {
                $dueDate = now()->addDays(fake()->numberBetween(3, 21));

                $existing = Assignment::where('title', $template['title'])
                    ->where('section_id', $section->id)
                    ->exists();

                if ($existing) {
                    continue;
                }

                $assignment = Assignment::create([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'section_id' => $section->id,
                    'title' => $template['title'] . ' — ' . $section->name,
                    'description' => $template['description'],
                    'attachment' => null,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'total_marks' => $template['total_marks'],
                    'status' => 'active',
                    'created_by' => $teacher->id,
                    'updated_by' => $teacher->id,
                ]);

                $assignmentCount++;

                $count = $sectionStudents->count();
                if ($count === 0) {
                    continue;
                }
                $submittingStudents = $sectionStudents->random(min(
                    $count,
                    fake()->numberBetween(3, max(3, $count))
                ));

                foreach ($submittingStudents as $student) {
                    $submittedAt = fake()->dateTimeBetween('-2 days', $dueDate->format('Y-m-d'));

                    $graded = fake()->boolean(40);

                    AssignmentSubmission::create([
                        'assignment_id' => $assignment->id,
                        'student_id' => $student->id,
                        'submission_file' => 'submissions/' . $student->admission_no . '_' . $assignment->id . '.pdf',
                        'submitted_at' => $submittedAt,
                        'marks' => $graded ? fake()->randomFloat(2, 0, (float) $assignment->total_marks) : null,
                        'feedback' => $graded && fake()->boolean(70)
                            ? fake()->randomElement([
                                'Good work! Please pay attention to formatting.',
                                'Well done. Clear and concise.',
                                'Needs improvement in analysis section.',
                                'Excellent submission. Keep it up!',
                                'Some errors in calculations, otherwise good.',
                            ])
                            : null,
                        'status' => $graded ? 'graded' : 'submitted',
                    ]);

                    $submissionCount++;
                }
            }

            $this->command?->info("Assignments created for section: {$section->name}");
        }

        $this->command?->info("Demo assignments created: {$assignmentCount} with {$submissionCount} submissions.");
    }
}
