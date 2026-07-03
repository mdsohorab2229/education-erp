<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('academic_year_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('semester_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('department_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('program_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('shift_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('group_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('section_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('subject_id')
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->date('attendance_date');
            $table->unsignedInteger('total_students')->default(0);
            $table->unsignedInteger('present_count')->default(0);
            $table->unsignedInteger('absent_count')->default(0);
            $table->unsignedInteger('late_count')->default(0);
            $table->unsignedInteger('leave_count')->default(0);
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('open');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();

            $table->unique([
                'academic_year_id',
                'semester_id',
                'department_id',
                'program_id',
                'shift_id',
                'group_id',
                'section_id',
                'subject_id',
                'attendance_date',
            ], 'attendance_session_unique');

            $table->index('status');
            $table->index('teacher_id');
            $table->index('attendance_date');
            $table->index('section_id');
            $table->index('department_id');
            $table->index('semester_id');
            $table->index('academic_year_id');
            $table->index('subject_id');

            $table->index(['attendance_date', 'section_id'], 'attendance_monthly_idx');
            $table->index(['teacher_id', 'attendance_date'], 'attendance_teacher_idx');
            $table->index(['subject_id', 'attendance_date'], 'attendance_subject_idx');
            $table->index(['department_id', 'attendance_date'], 'attendance_department_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
