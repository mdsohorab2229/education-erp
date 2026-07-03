<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $sections = Section::with('program')->get();

        if ($sections->isEmpty()) {
            $this->command?->warn('No sections found. Skipping ContentDemoSeeder.');

            return;
        }

        $teachers = User::role('Teacher')->get();

        if ($teachers->isEmpty()) {
            $this->command?->warn('No teacher users found. Skipping ContentDemoSeeder.');

            return;
        }

        $contentData = [
            [
                'title' => 'Introduction to Course Syllabus',
                'type' => 'pdf',
                'description' => 'Complete syllabus breakdown including grading policy, schedule, and required materials.',
                'file_name' => 'syllabus-overview.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 245000,
            ],
            [
                'title' => 'Chapter 1 — Lecture Recording',
                'type' => 'video',
                'description' => 'Recorded lecture covering foundational concepts from Chapter 1.',
                'file_name' => 'chapter-1-lecture.mp4',
                'mime_type' => 'video/mp4',
                'file_size' => 85000000,
            ],
            [
                'title' => 'Chapter 1 — Handwritten Notes',
                'type' => 'notes',
                'description' => 'Concise handwritten-style notes summarizing Chapter 1 key points.',
                'file_name' => 'chapter-1-notes.docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'file_size' => 180000,
            ],
            [
                'title' => 'Practice Problems Set 1',
                'type' => 'pdf',
                'description' => 'Practice questions with answer key for self-assessment.',
                'file_name' => 'practice-set-1.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 320000,
            ],
            [
                'title' => 'Midterm Review Session',
                'type' => 'video',
                'description' => 'Comprehensive review session covering all topics before the midterm exam.',
                'file_name' => 'midterm-review.mp4',
                'mime_type' => 'video/mp4',
                'file_size' => 120000000,
            ],
            [
                'title' => 'Formula Sheet & Cheat Sheet',
                'type' => 'pdf',
                'description' => 'Quick reference sheet with all important formulas and definitions.',
                'file_name' => 'formula-sheet.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 95000,
            ],
        ];

        $created = 0;

        foreach ($sections as $section) {
            $teacher = $teachers->random();
            $sectionSubjects = $section->program?->subjects ?? collect();

            if ($sectionSubjects->isEmpty()) {
                continue;
            }

            $subject = $sectionSubjects->random();
            $itemsToCreate = fake()->numberBetween(2, 4);

            foreach (array_slice($contentData, 0, $itemsToCreate) as $data) {
                $exists = Content::where('title', $data['title'])
                    ->where('section_id', $section->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Content::create([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'section_id' => $section->id,
                    'title' => $data['title'] . ' — ' . $section->name,
                    'type' => $data['type'],
                    'file_name' => $data['file_name'],
                    'file_path' => 'contents/' . $data['file_name'],
                    'file_size' => $data['file_size'],
                    'mime_type' => $data['mime_type'],
                    'description' => $data['description'],
                    'status' => 'active',
                    'created_by' => $teacher->id,
                    'updated_by' => $teacher->id,
                ]);

                $created++;
            }
        }

        $this->command?->info("Demo content created: {$created} records across {$sections->count()} sections.");
    }
}
