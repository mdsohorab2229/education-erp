<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table): void {
            $table->index('is_current');
            $table->index('start_date');
            $table->index('end_date');
        });

        Schema::table('programs', function (Blueprint $table): void {
            $table->index('department_id');
        });

        Schema::table('sections', function (Blueprint $table): void {
            $table->index('program_id');
        });

        Schema::table('subjects', function (Blueprint $table): void {
            $table->index('program_id');
            $table->index('type');
        });

        Schema::table('groups', function (Blueprint $table): void {
            $table->index('program_id');
        });

        Schema::table('students', function (Blueprint $table): void {
            $table->index('status');
            $table->index('email');
            $table->index('program_id');
            $table->index('section_id');
            $table->index('academic_year_id');
            $table->index('shift_id');
        });

        Schema::table('teachers', function (Blueprint $table): void {
            $table->index('email');
            $table->index('status');
            $table->index('user_id');
            $table->index('designation');
        });

        Schema::table('guardians', function (Blueprint $table): void {
            $table->index('phone');
        });

        Schema::table('student_documents', function (Blueprint $table): void {
            $table->index('student_id');
            $table->index('document_type');
        });

        Schema::table('student_promotions', function (Blueprint $table): void {
            $table->index('student_id');
            $table->index('from_academic_year_id');
            $table->index('to_academic_year_id');
        });

        Schema::table('teacher_subjects', function (Blueprint $table): void {
            $table->index('subject_id');
        });

        Schema::table('teacher_departments', function (Blueprint $table): void {
            $table->index('department_id');
        });

        Schema::table('teacher_qualifications', function (Blueprint $table): void {
            $table->index('teacher_id');
        });

        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->index('checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table): void {
            $table->dropIndex(['is_current']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
        });

        Schema::table('programs', function (Blueprint $table): void {
            $table->dropIndex(['department_id']);
        });

        Schema::table('sections', function (Blueprint $table): void {
            $table->dropIndex(['program_id']);
        });

        Schema::table('subjects', function (Blueprint $table): void {
            $table->dropIndex(['program_id']);
            $table->dropIndex(['type']);
        });

        Schema::table('groups', function (Blueprint $table): void {
            $table->dropIndex(['program_id']);
        });

        Schema::table('students', function (Blueprint $table): void {
            $table->dropIndex(['status']);
            $table->dropIndex(['email']);
            $table->dropIndex(['program_id']);
            $table->dropIndex(['section_id']);
            $table->dropIndex(['academic_year_id']);
            $table->dropIndex(['shift_id']);
        });

        Schema::table('teachers', function (Blueprint $table): void {
            $table->dropIndex(['email']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['designation']);
        });

        Schema::table('guardians', function (Blueprint $table): void {
            $table->dropIndex(['phone']);
        });

        Schema::table('student_documents', function (Blueprint $table): void {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['document_type']);
        });

        Schema::table('student_promotions', function (Blueprint $table): void {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['from_academic_year_id']);
            $table->dropIndex(['to_academic_year_id']);
        });

        Schema::table('teacher_subjects', function (Blueprint $table): void {
            $table->dropIndex(['subject_id']);
        });

        Schema::table('teacher_departments', function (Blueprint $table): void {
            $table->dropIndex(['department_id']);
        });

        Schema::table('teacher_qualifications', function (Blueprint $table): void {
            $table->dropIndex(['teacher_id']);
        });

        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->dropIndex(['checked_at']);
        });
    }
};
